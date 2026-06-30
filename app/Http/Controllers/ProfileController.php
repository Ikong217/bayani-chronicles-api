<?php

namespace App\Http\Controllers;

use App\Helper\AdminChecker;
use App\Mail\UserChangeCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function ProfileShow()
    {
        return view('profile');
    }

    // Upload profile picture
    public function ProfileSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile' => 'required|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $user = AdminChecker::getUser();
        $file = $request->file('profile');
        $filename = now()->timestamp . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Use base_path() instead of public_path()
        $path = base_path('../assets/images/profiles/');

        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            return response()->json(['status' => 'error', 'message' => 'Cannot create upload directory.'], 500);
        }

        try {
            $file->move($path, $filename);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to move uploaded file.'], 500);
        }

        // Delete old profile safely
        if (!empty($user->profile) && $user->profile !== 'user.png' && file_exists($path . $user->profile)) {
            @unlink($path . $user->profile);
        }

        $user->profile = $filename;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Profile picture updated.']);
    }

    // Update profile info (requires current password in request)
    public function ChangeCredential(Request $request)
    {
        $user = AdminChecker::getUser();
        $id = $user->id;

        $validator = Validator::make($request->all(), [
            'name' => "required|string|unique:teachers,name,$id",
            'email' => "required|email|unique:teachers,email,$id",
            'contact' => 'required|string',
            'birthday' => 'required|date',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Verify current password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect password.'], 403);
        }

        // Attempt to send notification emails (non-fatal if mail fails — but return error so user knows)
        try {
            Mail::to($request->email)->send(new UserChangeCredential("Changed profile", 'You have successfully changed your credentials.'));
            if ($user->email && $user->email !== $request->email) {
                Mail::to($user->email)->send(new UserChangeCredential("Changed profile", 'Your credentials were changed. If this wasn\'t you, contact admin.'));
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to send notification email.'], 500);
        }

        // Update user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->birthday = $request->birthday;
        $user->contact = $request->contact;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Profile updated successfully.']);
    }

    // Change password - uses strong password rules
    public function ChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currpass' => 'required|string', // current password
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $user = AdminChecker::getUser();

        // Fix: Check current password correctly
        if (!Hash::check($request->currpass, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect current password.'], 403);
        }

        // Fix: Use the correct field name
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Password changed successfully.']);
    }
}
