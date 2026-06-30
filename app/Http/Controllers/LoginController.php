<?php
namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Helper\Audit;
use App\Mail\FpassCode;
use App\Mail\OtpLogin;
use App\Models\Admin;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PharIo\Manifest\Email;

class LoginController extends Controller
{
    //Showing Login Form
    public function showLogin(Request $request)
    {
        if (Auth::guard('teacher')->check()) {
            if (Auth::guard('teacher')->check()) {
                Auth::guard('teacher')->logout();
            } else {
                Auth::logout(); // This already removes the session and resets remember_token
            }

            session()->forget('isAdmin');

            // Invalidate session and CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Cookie::queue(Cookie::forget('remember'));
        }
        return view('login');
    }

    //Submit Log in Json Request
    public function loginSubmit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'                => 'required|email',
                'password'             => 'required|string',
                'g-recaptcha-response' => 'required|captcha',
            ]);

            if ($validator->fails()) {
                Audit::Set('Login', 'Invalid Validation', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $teacher = Teacher::where('email', $request->email)->first();

            if ($teacher) {
                $type = AdminChecker::isAdmin($teacher->id) ? 'Super Admin' : 'Admin';
                if (! Auth::guard('teacher')->attempt($request->only('email', 'password'))) {
                    Audit::Set('Login', 'Incorrect Password - access', 'fail', $request, $type, $teacher->email);
                    return response()->json([
                        'status'  => 'invalid',
                        'message' => 'Invalid Credentials',
                    ]);
                }
            } else {
                Audit::Set('Login', 'Invalid Credentials', 'fail', $request, 'Admin');
                return response()->json([
                    'status'  => 'invalid',
                    'message' => 'Invalid Credentials',
                ]);
            }

            $teacher = Auth::guard('teacher')->user();

            $superAdmin = AdminChecker::isAdmin($teacher->id);

            $otp = rand(100000, 999999);
            $key = $teacher->email . "_otp_login";

            //valid for 5 mins
            Cache::put($key, $otp, 300);

            Mail::to($teacher->email)->send(new OtpLogin($otp));
            Auth::guard('teacher')->logout();

                                                         //remember
            Cache::forget("rem_req_" . $teacher->email); //forget Cache

            if ($request->remember == 1) {
                Cache::put("rem_req_" . $teacher->email, 1, 600); //10mins
            }

            $type = $superAdmin ? 'Super Admin' : 'Teacher';
            Audit::Set('Login', 'Request Otp', 'success', $request, $type, $request->email);
            // return success otp response
            return response()->json([
                'status'  => 'success',
                'message' => 'OTP sent',
                'data'    => ['key' => Crypt::encrypt($key)],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => $e->getMessage()],
            ]);
        }
    }

    //OTP `veri`fication Json Request
    public function OtpVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'key' => 'required|string',
                'otp' => 'required|digits:6',
            ]);

            if ($validator->fails()) {
                Audit::Set('OTP Verification', 'Input Manipulation', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $key      = Crypt::decrypt($request->key);
            $otp      = $request->otp;
            $cacheOtp = Cache::get($key);

            if (! $cacheOtp) {
                Audit::set('OTP Verification', 'Expired OTP or Cache key not found', 'fail', $request, 'Admin');
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'Your OTP has expired, please try logging in again.',
                ]);
            }

            if ($cacheOtp != $otp) {
                $email      = str_replace('_otp_login', '', $key);
                $attemptKey = 'otp_attempts_' . $email;
                $attempts   = Cache::get($attemptKey);
                $attempts += 1;
                Cache::put($attemptKey, $attempts, now()->addMinutes(5));

                $teacher = Teacher::where('email', $email)->first();
                $type    = "";
                if ($teacher) {
                    $type = AdminChecker::isAdmin($teacher->id) ? 'Super Admin' : 'Admin';
                } else {
                    $type = 'Admin';
                }

                if ($attempts > 5) {
                    Audit::Set('OTP Verification', 'Maximum Attempts Reached', 'fail', $request, $type, $email);
                    Cache::forget('otp_attempts_' . $email);
                    return response()->json([
                        'status' => 'error',
                        'errors' => ['Attempts exeeded' => 'Too many incorrect attempts. Please request a new OTP.'],
                    ]);
                }

                Audit::Set('OTP Verification', 'Mismatched OTP, Attemps:' . $attempts, 'fail', $request, $type, $email);
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'OTP does not match, please try again. Attempts: ' . $attempts,
                ]);
            }

            // OTP matched — continue with login
            $email   = str_replace('_otp_login', '', $key);
            $teacher = Teacher::where('email', $email)->first();

            if ($teacher) {
                Auth::guard('teacher')->login($teacher);

                $isAdmin = Admin::where('teacher_id', $teacher->id)->exists();
                $type    = $isAdmin ? 'Super Admin' : 'Teacher';
                if ($isAdmin) {
                    session()->put('isAdmin', true);
                }

                // Clear used OTP and attempts
                Cache::forget($key);
                Cache::forget('otp_attempts_' . $email);
                Cache::forget('otp_resend_' . $email);

                $expireAtMidnight     = Carbon::now()->next(Carbon::MONDAY)->startOfDay();
                $minutesUntilMidnight = max(1, now()->diffInMinutes($expireAtMidnight));

                $remember = Cache::get("rem_req_" . $email);
                if ($remember) {
                    Cache::forget("rem_req_" . $email);
                    $tokenRaw = $request->ip() . '_' . $request->userAgent() . '_' . Auth::guard('teacher')->user()->id;
                    $token    = Crypt::encrypt($tokenRaw);
                    Cookie::queue('remember', $token, $minutesUntilMidnight);
                }

                Audit::Set('OTP Verification', 'Successful Verification', 'success', $request, $type, $teacher->email);
                AdminChecker::SessionCreate($request);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'OTP Verified',
                ]);
            }

            Audit::Set('OTP Verification', 'Failed Authentication', 'unknown', $request, 'Admin');
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => 'Unable to authenticate teacher.'],
            ]);

        } catch (\Exception $e) {
            Log::error($e); // Log it for debugging
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => 'Something went wrong. Please try again later.'],
            ]);
        }
    }

    //Resend otp
    public function ResendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'key' => 'required|string',
            ]);

            if ($validator->fails()) {
                Audit::Set('Resend OTP', 'Input Manipulation', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $key       = Crypt::decrypt($request->key);
            $email     = str_replace('_otp_login', '', $key);
            $resendKey = 'otp_resend_' . $email;

            $teacher = Teacher::where('email', $email)->first();
            $type    = "";
            if ($teacher) {
                $type = AdminChecker::isAdmin($teacher->id) ? 'Super Admin' : 'Admin';
            } else {
                $type = 'Admin';
            }

            if (Cache::has($resendKey)) {
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'Please wait a few seconds before requesting a new OTP.',
                ]);
            }

            // Throttle for 60 seconds
            Cache::put($resendKey, true, now()->addSeconds(60));

            $otp = rand(100000, 999999);

            // Store OTP and reset attempts
            Cache::put($key, $otp, now()->addMinutes(5));
            Cache::forget('otp_attempts_' . $email);

            Mail::to($email)->send(new OtpLogin($otp));

            Audit::Set('OTP Verification', 'Resend OTP Success', 'success', $request, $type, $email);

            return response()->json([
                'status'  => 'success',
                'message' => 'The OTP has been sent. Please check your spam folder if it is not in your inbox.',
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => 'Something went wrong. Please try again later.'],
            ]);
        }
    }

                                               //Remember me code : default uri - remember logic - login or auto login;
    public function Remember(Request $request) // inject Request properly
    {
        if(AdminChecker::AuthenticateCookie($request)){
            $email = AdminChecker::getUser()->email;
            $type = AdminChecker::isMeAdmin() ? 'Super Admin' : 'Admin';
            Audit::Set('Auto Log','Verified Cookie Certificate','success',$request,$type,$email);
            return redirect()->route('dashboard');
        }
        return redirect()->route('login.show');
    }

    //forget password Json Request
    public function ForgotPass(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:teachers,email',
            ]);

            if ($validator->fails()) {
                Audit::Set('Forgotpass Request', 'Failed validation', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $email = $request->email;

            $isBanned = Cache::get("banned_" . $request->ip());
            if ($isBanned) {
                Audit::Set('Forgotpass Request', 'Banned Due to Exessive Failed Request', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => ['banned' => 'You are now banned, Please try again Within an Hour'],
                ]);
            }

            // Prevent abuse (1 request per 60s)
            $rateLimitKey = "fpass_sent_" . $email;
            if (Cache::has($rateLimitKey)) {
                Audit::Set('Forgotpass Request', 'Spam Request', 'pending', $request, 'Admin');
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Code Already sent, Please wait before requesting another code.',
                ]);
            }
            Cache::put($rateLimitKey, true, 60); // 60 seconds

            $secretCode = Str::random(8);
            Cache::put("fpass_" . $email, $secretCode, now()->addMinutes(5));

            // Send the email
            Mail::to($email)->send(new FpassCode($secretCode));

            Audit::Set('Forgotpass Request', 'Sucess Request', 'success', $request, 'Admin');
            return response()->json([
                'status'  => 'success',
                'message' => "Please check your email for the code.",
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => 'Something went wrong. Please try again later.'.$e->getMessage()],
            ]);
        }
    }

    //forgotpassword verify code Json Request
    public function FpassVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:teachers,email',
                'code'  => 'required',
            ]);

            if ($validator->fails()) {
                Audit::Set('Forgotpass Verification', 'Failed validation', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $email = $request->email;
            $code  = $request->code;

            // Attempt tracking
            $codeAttempt = Cache::get('code_attempts_' . $email, 0);
            $codeAttempt += 1;

            if ($codeAttempt > 5) {
                Audit::Set('Forgotpass Verify', 'Account Lockout, User Max attempts reached', 'fail', $request, 'Admin');
                Cache::put('banned_' . $request->ip(), true, 3600); // FIX: This must be before return
                Cache::forget('code_attempts_' . $email);
                Cache::forget("fpass_" . $email);
                Cache::forget('fpass_sent_' . $email);
                return response()->json([
                    'status' => 'error',
                    'errors' => ['attempts' => 'Maximum attempts exceeded. Your email are now banned. Please ask the Admin to change your password.'],
                ]);
            }

            Cache::put('code_attempts_' . $email, $codeAttempt, 300); // 5 minutes

            $cacheCode = Cache::get("fpass_" . $email);

            if (! $cacheCode) {
                Audit::Set('Forgotpass Verify', 'Exired Code', 'fail', $request, 'Admin');
                return response()->json([
                    'status' => 'error',
                    'errors' => ['code' => 'Email is incorrect or the code is expired. Please try again.'],
                ]);
            }

            if ($cacheCode !== $code) {
                Audit::Set('Forgotpass Verify', 'Mismatched Verification', 'fail', $request, 'Admin');
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'The code does not match. Please try again.',
                ]);
            }

            // Generate a safe URI code
            $uriCode = "";
            do {
                $uriCode = 'reset_' . Str::random(24); // Safer, longer
                Cache::put($uriCode, $email, 600);     // 10 minutes
            } while (! Cache::get($uriCode));

            Cache::forget('code_attempts_' . $email);
            Cache::forget("fpass_" . $email);
            Cache::forget('fpass_sent_' . $email);

            Audit::Set('Forgotpass Verify', 'Verify Authenticity', 'success', $request, 'Admin');
            return response()->json([
                'status'  => 'success',
                'message' => "Verified code. You can change your password for 10 minutes.",
                'uri'     => $uriCode,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'warning',
                'errors' => ['exception' => 'Something went wrong. Please try again later.'],
            ]);
        }
    }

    //Logout logic s
    public function logout(Request $request)
    {
        $teacher = AdminChecker::getUser();
        $type    = AdminChecker::isAdmin($teacher->id) ? 'Super Admin' : 'Admin';
        Audit::Set('Log Out', 'Logged out Successful', 'success', $request, $type, $teacher->email);
        if (Auth::guard('teacher')->check()) {
            Auth::guard('teacher')->logout();
        } else {
            Auth::logout(); // This already removes the session and resets remember_token
        }

        session()->forget('isAdmin');

        // Invalidate session and CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Cookie::queue(Cookie::forget('remember'));
        return redirect()->route('login.show')->with('message', 'you have successfully logged out');
    }
}
