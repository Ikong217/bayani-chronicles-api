<?php
namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Helper\Audit;
use App\Mail\NewEmailCode;
use App\Mail\OldEmailConfirm;
use App\Mail\OtpLogin;
use App\Mail\UserChangeCredential;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserInGameData;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class UnityUsersAuth extends Controller
{
    // Insert Data (Unity sends data)
    // public function insertData(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string|max:255',
    //         'email'    => 'required|email|unique:users,email',
    //         'password' => 'required|string|confirmed|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Invalid Input',
    //             'errors'  => $validator->errors(),
    //         ], 422);
    //     }

    //     $user = User::create([
    //         'username' => $request->username,
    //         'email'    => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);

    //     return response()->json([
    //         'status'  => 'success',
    //         'message' => 'User successfully registered',
    //         'user'    => $user,
    //     ], 201);
    // }

    public function logUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Input',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $isEmail      = filter_var($request->email, FILTER_VALIDATE_EMAIL);
        $usernameType = $isEmail ? 'email' : 'username';

        if (Auth::attempt([$usernameType => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->isBanned) {
                Audit::Set('Unity Login', 'Banned Login', 'failed', $request, 'Player', $user->email);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Banned',
                    'errors'  => ['ban status' => ['Your account is currently Banned, Please contact your adviser to remove Unban you']],
                ], 401);
            }

            if (! AdminChecker::userHasSection($user->id)) {
                Audit::Set('Unity Login', 'No Section, Invalid Access', 'fail', $request, 'Player', $user->email);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No Section',
                    'errors'  => ['unauthorized' => ['You cannot proceed to the game yet. Please contact your teacher to Enroll you']],
                ], 401);
            }

            $otpKeyString = Str::random(10);
            $otpValue     = random_int(100000, 999999);
            Cache::put($otpKeyString, $otpValue . "_" . $user->id, 300); //5 mins

            Mail::to($user->email)->send(new OtpLogin($otpValue));
            Auth::logout(); //to avoid floating active user

            Audit::Set('Unity Login', 'Verification Sent', 'success', $request, 'Player', $user->email);
            return response()->json([
                'status'  => 'success',
                'message' => 'Credential Validation Successful',
                'otp_key' => $otpKeyString,
            ]);

        } else {
            Audit::Set('Unity Login', 'Invalid Credential', 'failed', $request, 'Player');
            return response()->json([
                'status'  => 'error',
                'message' => 'No users found or invalid credentials',
            ], 401);
        }
    }

    public function Otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'otp' => 'required|integer|min:100000|max:999999',
        ]);

        if ($validator->fails()) {
            //Audit::Set('Unity OTP Verify', 'Invalid Input', 'fail', $request, 'Player');
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Input',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $key = $request->key;
        $otp = $request->otp;

        $otpCacheValue = Cache::get($key, null);

        if (! $otpCacheValue) {
            Audit::Set('Unity OTP Verify', 'Invalid Key', 'fail', $request, 'Player', "Unknown User [key=" . $key . "]");
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Key',
                'errors'  => ['otp' => ['Your OTP is missing or expired']],
            ], 401);
        }

        $splitValue = explode("_", $otpCacheValue);
        $otpValue   = $splitValue[0];
        $userID     = $splitValue[1];

        $user = AdminChecker::GetPlayer($userID);

        if ($otpValue != $otp) {
            Audit::Set('Unity OTP Verify', 'OTP Mismatched', 'fail', $request, 'Player', $user->email);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid OTP',
                'errors'  => ['otp' => ['Your OTP doesn\'t match. Please try again']],
            ], 401);
        }

        if (! $user) {
            Audit::Set('Unity OTP Verify', 'Invalid User', 'unknown', $request, 'Player', $user->email);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid User',
                'errors'  => ['otp' => ['User unidentified. Please try to log in again']],
            ], 401);
        }

        $encryptedID = Crypt::encrypt($user->id);
        Cache::forget($key); //remove from cache to avoid data overloading

        if ($user->isBanned) {
            Audit::Set('Unity OTP Verify', 'Banned User', 'fail', $request, 'Player', $user->email);
            return response()->json([
                'status'  => 'error',
                'message' => 'Banned',
                'errors'  => ['ban status' => ['Your account is currently Banned, Please contact your adviser to remove Unban you']],
            ], 401);
        }

        $section      = AdminChecker::userGetSection($user->id);
        $grade_lvl    = "";
        $section_name = "";
        if ($section) {
            $grade_lvl    = $section->section->grade_level;
            $section_name = $section->section->section_name;
        }

        $data = UserInGameData::firstWhere('user_id', $user->id);

        Audit::Set('Unity OTP Verify', 'OTP Verified', 'fail', $request, 'Player', $user->email);

        return response()->json([
            'status'  => 'success',
            'message' => 'You are now logged in',
            'user'    => [
                'user_id'      => $encryptedID,
                'username'     => $user->username,
                'email'        => $user->email,
                'gender'       => $user->gender,
                'grade_lvl'    => $grade_lvl,
                'section_name' => $section_name,
                'isBanned'     => $user->isBanned,
            ],
            'levels'  => $data->levels ?? "",
            'scrolls' => $data->scrolls ?? "",
            'summative' => $data->summative ?? "",
            'encID'   => $encryptedID,
        ], 200);

    }

    public function ResendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
        ]);

        if ($validator->fails()) {
            Audit::Set('Unity Resend OTP', 'Validation Fail', 'fail', $request, 'Player', "Unknown User [key={$request->key}");
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Input',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $key = $request->key;

        $otpCacheValue = Cache::get($key, null);

        if (! $otpCacheValue) {
            Audit::Set('Unity Resend OTP', 'Expired Source OTP', 'fail', $request, 'Player', "Unknown Username [key={$request->key}]");
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Key',
                'errors'  => ['otp' => ['Your session OTP expired. Please try logging in again']],
            ], 401);
        }

        $splitValue = explode("_", $otpCacheValue);
        $userID     = $splitValue[1];
        $user       = User::find($userID);

        if (! $user) {
            Audit::Set('Unity Resend OTP', 'User Not Found', 'unknown', $request, 'Player', "Unknown User [key={$key}]");
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid User',
                'errors'  => ['otp' => ['User unidentified. Please try to log in again']],
            ], 401);
        }

        $otpValue = random_int(100000, 999999);
        Cache::put($key, $otpValue . "_" . $userID, 300); //5 mins

        Mail::to($user->email)->send(new OtpLogin($otpValue));

        Audit::Set('Unity Resend OTP', 'Resend Successful', 'success', $request, 'Player', $user->email);
        return response()->json([
            'status'  => 'success',
            'message' => 'OTP resent successfully',
            'otp_key' => $key,
        ]);
    }

    public function RequestAccess(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id'     => 'required|string',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Audit::Set('Unity Player Game Access', 'Validation Fail', 'fail', $request, 'Player', "Unknown User");
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Input',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user_id = Crypt::decrypt($request->id);
        } catch (\Exception $e) {
            Audit::Set('Unity Player Game Access', 'Invalid Player ID', 'fail', $request, 'Player', 'Unknown User');
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid player ID',
            ], 400);
        }

        $user = User::find($user_id);

        if (! $user) {
            Audit::Set('Unity Player Game Access', 'User Not Found', 'fail', $request, 'Player', 'Unknown User');
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found',
            ], 404);
        }

        if ($user->isBanned) {
            Audit::Set('Unity Player Game Access', 'User is Banned', 'fail', $request, 'Player', $user->email);
        } elseif ($request->reason === 'game access') { // ✅ fixed syntax
            Audit::Set('Unity Player Game Access', 'Access Confirmed', 'success', $request, 'Player', $user->email);
        }

        // Optional: handle missing section gracefully
        $sectionInfo  = AdminChecker::userGetSection($user->id);
        $grade_lvl    = $sectionInfo->section->grade_level ?? 'N/A';
        $section_name = $sectionInfo->section->section_name ?? 'N/A';

        return response()->json([
            'status'   => 'success',
            'message'  => 'Access confirmed',
            'user'     => [
                'user_id'      => $request->id, // already encrypted
                'username'     => $user->username,
                'email'        => $user->email,
                'gender'       => $user->gender,
                'grade_lvl'    => $grade_lvl,
                'section_name' => $section_name,
                'isBanned'     => (bool) $user->isBanned,
            ],
            'isBanned' => (bool) $user->isBanned,
        ]);
    }

    public function UserChangeUsername(Request $request)
    {
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|string',
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ]);
        }

        // ✅ Check if username already exists
        if (User::where('username', $request->username)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username Taken',
                'errors'  => ['message' => ["Username is already taken. Please choose another one."]],
            ]);
        }

        // ✅ Decrypt user ID
        try {
            $user_id = Crypt::decrypt($request->user_id);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid user',
                'errors'  => ['exception' => [$e->getMessage()]],
            ]);
        }

        // ✅ Rate limit username changes (7 days)
        $hasChanged = Cache::get('ChUs_' . $user_id);
        if ($hasChanged && Carbon::now()->lessThan($hasChanged)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username Recently Changed',
                'errors'  => [
                    'invalid' => [
                        'You recently changed your username. Please wait a week before changing it again.',
                    ],
                ],
            ]);
        }

        // ✅ Find user
        $user = User::find($user_id);
        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found',
                'errors'  => ['missing' => ['User is missing or deleted. Please log in again.']],
            ]);
        }

        // ✅ Verify password
        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Incorrect Password',
                'errors'  => ['invalid' => ['Incorrect password, please try again.']],
            ]);
        }

        // ✅ Update username
        $oldUsername    = $user->username;
        $user->username = $request->username;
        $user->save();

        // ✅ Set cooldown (7 days)
        Cache::put('ChUs_' . $user_id, Carbon::now()->addWeeks(1));

        // ✅ Send notification emails
        try {
            // Notify student's teacher
            $section = AdminChecker::userGetSection($user_id);
            if ($section && $section->section->teacher) {
                $teacherEmail = $section->section->teacher->email;
                Mail::to($teacherEmail)->send(
                    new UserChangeCredential(
                        "Student Changed Username",
                        "$oldUsername → {$request->username}"
                    )
                );
            }

            // Notify user
            Mail::to($user->email)->send(
                new UserChangeCredential(
                    "Username Changed Successfully",
                    "You have successfully changed your username from <b>$oldUsername</b> to <b>{$request->username}</b>. You cannot change it again for the next 7 days."
                )
            );

            // Notify all admins
            foreach (Admin::all() as $admin) {
                if ($admin->teacher) {
                    Mail::to($admin->teacher->email)->send(
                        new UserChangeCredential(
                            "Student Changed Username",
                            "$oldUsername → {$request->username}"
                        )
                    );
                }
            }
        } catch (Exception $e) {
            // Don’t fail on email errors — just log
            //Log::error('Email sending failed: ' . $e->getMessage());
        }

        // ✅ Success response
        return response()->json([
            'status'  => 'success',
            'message' => 'Username changed successfully. You cannot change it again for 7 days.',
            'user'    => $user,
        ]);
    }

    public function ChangeNewEmail(Request $request)
    {
        // ===========================
        // 1. Validate input
        // ===========================
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|string',
            'new_email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ]);
        }

        // ===========================
        // 2. Decrypt user_id
        // ===========================
        try {
            $user_id = Crypt::decrypt($request->user_id);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid user ID',
                'errors'  => ['user_id' => ['Invalid or tampered user ID']],
            ]);
        }

        // ✅ Detect lockout
        $lockOut = Cache::get("ChEm_{$user_id}_Lock", now());

        if (now()->lessThan($lockOut)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lockout',
                'errors'  => [
                    'lock' => ["You cannot change your email until {$lockOut}."],
                ],
            ]);
        }

        // ===========================
        // 3. Check email duplication
        // ===========================
        if (User::where('email', $request->new_email)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email taken',
                'errors'  => ['taken' => ['Email is already taken']],
            ]);
        }

        // ===========================
        // 4. Find user
        // ===========================
        $user = User::find($user_id);
        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => "Couldn't find user",
                'errors'  => ['unknown' => ['User might have been removed or changed']],
            ]);
        }

        // ===========================
        // 5. Generate random verification codes
        // ===========================
        $trueValue = random_int(10, 99);
        do {$value1 = random_int(10, 99);} while ($value1 == $trueValue);
        do {$value2 = random_int(10, 99);} while ($value2 == $trueValue || $value2 == $value1);

        $key   = "ChEm_" . $user->id;
        $value = $request->new_email . "_" . $trueValue . "_pending";

        Cache::put($key, $value, 300); // Cache for 5 minutes

        // ===========================
        // 6. Send confirmation email
        // ===========================
        // Encrypt user_id for email link
        $encryptedId = Crypt::encryptString($user->id);

        // Randomize code order
        $codeSet = collect([$trueValue, $value1, $value2])->shuffle();

        Mail::to($user->email)->send(
            new OldEmailConfirm($codeSet, $encryptedId)
        );

        // ===========================
        // 7. Respond to Unity
        // ===========================
        return response()->json([
            'status'  => 'success',
            'message' => 'Please check your current email for the confirmation code.',
            'code'    => $trueValue, // For debugging or Unity flow
        ]);
    }

    public function AwaitCodeSuccess(Request $request)
    {
        // 1️⃣ Validate input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 2️⃣ Decrypt user_id
        try {
            $userId = Crypt::decrypt($request->user_id);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid user ID',
                'errors'  => ['user_id' => ['Failed to decrypt user ID']],
            ], 403);
        }

        // 3️⃣ Check cache
        $cacheKey = "ChEm_" . $userId;
        $cached   = Cache::get($cacheKey);

        if (! $cached) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Code failed',
                'errors'  => ['fail' => ['Failed to authenticate code']],
            ]);
        }

        // Expected format: newEmail_code_status
        $parts = explode('_', $cached);
        if (count($parts) < 3) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Corrupted cache data',
            ]);
        }

        [$newEmail, $trueCode, $status] = $parts;

        // 4️⃣ Check status
        if ($status === "pending") {
            return response()->json([
                'status'  => 'error',
                'message' => 'On Process',
            ]);
        }

        // 5️⃣ Generate new verification string
        $randomCode = strtoupper(Str::random(6));

        // Update cache for final phase (email + code)
        Cache::put($cacheKey, "{$newEmail}_{$randomCode}", 300);

        // 6️⃣ Generate a CAPTCHA-like distorted image (you can implement this later)
        // Example: app('App\\Services\\CaptchaGenerator')->createImage($randomCode)
        // Save to storage/public/tmp/ and attach or embed in the mail.

        // 7️⃣ Send mail
        Mail::to($newEmail)->send(new NewEmailCode($randomCode));

        // 8️⃣ Return success response
        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully authenticated code. Please check your NEW email.',
        ]);
    }

    public function EmailVerifyCode(Request $request)
    {
        // ✅ Validate input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'code'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ]);
        }

        try {
            // ✅ Decrypt and get user
            $user_id = Crypt::decrtypt($request->user_id);
            $user    = User::find($user_id);

            if (! $user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid user',
                ]);
            }

            // ✅ Retrieve cache
            $cacheKey = 'ChEm_' . $user_id;
            $cached   = Cache::get($cacheKey);

            if (! $cached) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'expired',
                    'errors'  => [
                        'expired' => ['The session code has expired.'],
                    ],
                ]);
            }

            // ✅ Parse cache (expected format: email|code)
            [$newEmail, $storedCode] = explode('_', $cached);

            if ($request->code !== $storedCode) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'mismatched',
                    'errors'  => [
                        'mismatch' => ['Your provided code does not match.'],
                    ],
                ]);
            }

            // ✅ Update user email
            $oldEmail    = $user->email;
            $user->email = $newEmail;
            $user->save();

            // ✅ Notify relevant people
            $availableTime = now()->addMonth()->format('F d, Y h:i A');

            try {
                // Notify teacher
                $section = AdminChecker::userGetSection($user_id);
                if ($section && $section->section->teacher) {
                    Mail::to($section->section->teacher->email)->send(
                        new UserChangeCredential(
                            "Student Changed Email",
                            "$oldEmail → {$newEmail}"
                        )
                    );
                }

                // Notify user
                Mail::to($oldEmail)->send(
                    new UserChangeCredential(
                        "Email Changed Successfully",
                        "You have successfully changed your email from <b>{$oldEmail}</b> to <b>{$newEmail}</b>. You cannot change it again until <b>{$availableTime}</b>."
                    )
                );

                // Notify admins
                foreach (Admin::all() as $admin) {
                    if ($admin->teacher) {
                        Mail::to($admin->teacher->email)->send(
                            new UserChangeCredential(
                                "Student Changed Email",
                                "$oldEmail → {$newEmail}"
                            )
                        );
                    }
                }
            } catch (Exception $e) {
                //Log::error('Email sending failed: ' . $e->getMessage());
            }

            // ✅ Cache lockout for 1 month
            Cache::put("ChEm_{$user_id}_Lock", $availableTime, now()->addMonth());

            // ✅ Return success
            return response()->json([
                'status'  => 'success',
                'message' => "Email changed successfully. You cannot change it again until {$availableTime}.",
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }
}
