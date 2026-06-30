<?php
namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Helper\Audit;
use App\Mail\FpassCode;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPassController extends Controller
{
    //
    public function ForgotPassShow(Request $request, $code)
    {
        try {
            $email = Cache::get($code);

            if (! $email) {
                Audit::Set('Forgot Password', 'Access Invalid Forgot Password', 'fail', $request, 'Admin');
                abort(410, "This Page is no longer valid");
            }

            $rawCode = str_replace("reset_", '', $code);

            //Audit::Set('Forgot Password','Access Valid Forgot Password','success',$request, 'Admin');
            return view('ForgotPasword', compact('email', 'rawCode'));
        } catch (\Exception $e) {
            abort(419, 'This page is No longer Exist');
        }
    }

    public function ForgorPassSubmit(Request $request)
    {
        try {
            $request->validate([
                'code'     => 'required',
                'password' => [
                    'required',
                    'confirmed',
                    'min:8', // length check
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                ],
            ]);

            $code  = "reset_" . $request->code;
            $email = Cache::get($code);

            if (! $email) {
                Audit::Set('Forgot Password', 'Changed Code', 'fail', $request, 'Admin');
                return redirect()->back()->with('error', 'Please don\'t change the code');
            }

            $teacher = Teacher::where('email', $email)->first();

            if (! $teacher) {
                Audit::Set('Forgot Password', 'Email Invalid', 'fail', $request, 'Admin');
                return redirect()->back()->with('error', 'Couldn\'t find your email. Please try again');
            }

            $teacher->update([
                'password' => bcrypt($request->password), // Hash password
            ]);

            Cache::forget($code);

            Audit::Set('Forgot Password', 'Passoword changed', 'success', $request, AdminChecker::isAdmin($teacher->id) ? 'Super Admin' : 'Admin', $teacher->email);
            return redirect()->route('login.show')->with('message', 'Your password has been changed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was a problem while processing your request' . $e->getMessage());
        }
    }

    // public function UserForgotShow(Request $request)
    // {
    //     $isBanned = Cache::get("banned_" . $request->ip());
    //     if ($isBanned) {
    //     abort(429, "You are banned in the service for an hour");
    //     }
    //     return view('users.forgot_pass');
    // }

    public function UserForgotPass(Request $request)
    {
        try {
            $isBanned = Cache::get("banned_" . $request->ip());
            if ($isBanned) {
                Audit::Set('Unity Forgot Password', 'Account Lockout Due to Eccessive Failed Attempts', 'fail', $request, 'Player');
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Account Banned',
                    'errors'  => ['error' => "You cannot access ForgotPassword for an Hour, Please try Again Within one Hour"],
                ]);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                Audit::Set('Unity Forgot Password', 'Validation Fail', 'fail', $request, 'Player');
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }

            $email = $request->email;

            $isBanned = Cache::get("banned_" . $request->ip());
            if ($isBanned) {
                Audit::Set('Unity Forgot Password', 'User Banned Due to Too much attempts', 'fail', $request, 'Player');
                return response()->json([
                    'status' => 'error',
                    'errors' => ['banned' => ['You are now banned, Please try again Within an Hour']],
                ]);
            }

            // Prevent abuse (1 request per 60s)
            $rateLimitKey = "fpass_sent_" . $email;
            if (Cache::has($rateLimitKey)) {
                Audit::Set('Unity Forgot Password', 'Spam click', 'pending', $request, 'Player');
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

            Audit::Set('Unity Forgot Password', 'Forgot Pass Request Success', 'fail', $request, 'Player');
            return response()->json([
                'status'  => 'success',
                'message' => "Please check your email for the code.",
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'errors' => ['exception' => ['Something went wrong. Please try again later.']],
            ]);
        }
    }

    public function UserFpassVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code'  => 'required',
            ]);

            if ($validator->fails()) {
                Audit::Set('Unity Forgot Password Verify', 'Validation Fail', 'fail', $request, 'Player');
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
                Audit::Set('Unity Forgot Password Verify', 'Banned Due to Too much Attempts', 'fail', $request, 'Player');
                Cache::put('banned_' . $request->ip(), true, 3600); // FIX: This must be before return
                Cache::forget('code_attempts_' . $email);
                Cache::forget("fpass_" . $email);
                Cache::forget('fpass_sent_' . $email);
                return response()->json([
                    'status'  => 'error',
                    'message' => "Attempt Exceeded",
                    'errors'  => ['attempts' => ['Maximum attempts exceeded. Your email are now banned. Please ask the Admin to change your password.']],
                ]);
            }

            Cache::put('code_attempts_' . $email, $codeAttempt, 300); // 5 minutes

            $cacheCode = Cache::get("fpass_" . $email);

            if (! $cacheCode) {
                Audit::Set('Unity Forgot Password Verify', 'Expired Code', 'fail', $request, 'Player');
                return response()->json([
                    'status' => 'error',
                    'errors' => ['code' => 'Email is incorrect or the code is expired. Please try again.'],
                ]);
            }

            if ($cacheCode !== $code) {
                Audit::Set('Unity Forgot Password Verify', 'Code Mismatch', 'fail', $request, 'Player');
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Code Mismatched',
                    'errors'  => ['mismatch' => ['The Code You Enter Fails']],
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

            Audit::Set('Unity Forgot Password Verify', 'Code Verified', 'fail', $request, 'Player');
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

    // public function UserResetPass(Request $request, $code)
    // {
    //     try {
    //         $email = Cache::get($code);

    //         if (! $email) {
    //             Audit::Set('Unity Reset Password','Page Expired','fail',$request, 'Player');
    //             abort(410, "This Page is no longer valid");
    //         }

    //         $rawCode = str_replace("reset_", '', $code);

    //         return view('users.reset_pass', compact('email', 'rawCode'));
    //     } catch (\Exception $e) {
    //         abort(419, 'This page is No longer Exist');
    //     }
    // }

    public function UserResetPassSubmit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code'     => 'required',
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                ],
            ]);

            if ($validator->fails()) {
                Audit::Set('Unity Reset Password', 'Validation Fail', 'fail', $request, 'Player');
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ]);
            }

            $code  = "reset_" . $request->code;
            $email = Cache::get($code);

            if (! $email) {
                Audit::Set('Unity Reset Password', 'Altered Code', 'fail', $request, 'Player');
                return response()->json([
                    'status'  => 'error',
                    'message' => "Altered or expired code.",
                    'errors'  => ['code' => ['Invalid or expired reset code. Please request again.']],
                ]);
            }

            $user = User::where('email', $email)->first();

            if (! $user) {
                Audit::Set('Unity Reset Password', 'User Not Found', 'fail', $request, 'Player');
                return response()->json([
                    'status'  => 'error',
                    'message' => "User not found.",
                    'errors'  => ['email' => ['Email address could not be found. Please try again.']],
                ]);
            }

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            Cache::forget($code);

            Audit::Set('Unity Reset Password', 'Password Changed', 'success', $request, 'Player');

            return response()->json([
                'status'  => 'success',
                'message' => 'Your password has been successfully changed. You can now log in.',
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred.',
                'errors'  => ['exception' => [$e->getMessage()]],
            ]);
        }
    }

}
