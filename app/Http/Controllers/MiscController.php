<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class MiscController extends Controller
{
    //
    public function DashboardShow()
    {
        $users = User::all()->count();
        $teachers = Teacher::all()->count();
        $sections = Section::all()->count();
        return view('dashboard',compact('users','teachers','sections'));
    }

    public function ShowTNC()
    {
        return view(view: 'othes.TermsAndCondition');
    }

    public function ShowAudit()
    {
        $logs = AuditLog::orderByDesc('created_at')->get();

        return view('audit', compact('logs'));
    }

    public function download($storage, $filename)
    {
        $path = storage_path('app/public/downloadable/' . $storage . '/' . $filename); // or storage/app/
                                                                                       //dd($path);
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    }

    public function OldEmailVerify($encryptedId, $code)
    {
        try {
            $userId = Crypt::decryptString($encryptedId);
        } catch (\Exception $e) {
            abort(403, 'Invalid or tampered link.');
        }

        $cacheKey = "ChEm_" . $userId;
        $cached = Cache::get($cacheKey);

        // Check if cache expired or missing
        if (!$cached) {
            abort(403, 'This link has expired or is invalid.');
        }

        // Cache format: newEmail_code_status
        $parts = explode('_', $cached);
        if (count($parts) < 3) {
            abort(403, 'Invalid data format.');
        }

        [$newEmail, $trueCode, $status] = $parts;

        // Prevent re-use of link
        if ($status === "success") {
            abort(403, 'This link has already been used.');
        }

        // Wrong code: invalidate request
        if ($trueCode != $code) {
            Cache::forget($cacheKey);
            return view('emails.ch_em_invalidated');
        }

        // Code matched: mark as validated
        $newValue = "{$newEmail}_{$trueCode}_success";
        Cache::put($cacheKey, $newValue, 300);

        return view('emails.ch_em_validated');
    }
}
