<?php
namespace App\Helper;

use App\Models\Admin;
use App\Models\AppliedSection;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Throwable;
use function Termwind\renderUsing;

class AdminChecker
{

    public static function isAdmin($id)
    {
        try {
            if (! $id) {
                $teacher = Auth::guard('teacher')->user();
            } else {
                $teacher = Teacher::findOrFail($id);
            }
            return Admin::where('teacher_id', $teacher->id)->exists();
        } catch (\Exception $e) {
            return false;
        }

    }

    public static function isMeAdmin()
    {
        if (Auth::guard('teacher')->check()) {
            $teacher = Auth::guard('teacher')->user();
            return Admin::where('teacher_id', $teacher->id)->exists();
        }

        return false;
    }

    public static function getUser()
    {
        if (Auth::guard('teacher')->check()) {
            return Auth::guard('teacher')->user();
        }

        return null;
    }

    public static function isCurrentUser($id)
    {
        try {
            $currentUser = Auth::guard('teacher')->user();
            return $currentUser->id == $id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function userHasSection($id)
    {
        try {
            return AppliedSection::where('user_id', $id)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function userGetSection($id)
    {
        try {
            return AppliedSection::where('user_id', $id)->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function allowCurrentTeacher($id)
    {
        try {
            $teacher = AdminChecker::getUser();
            return Section::where('teacher_id', $teacher->id)->where('id', $id)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function CheckTeacher()
    {
        return Auth::guard('teacher')->check();
    }

    public static function LoginTeacher($id)
    {
        try {
            return Auth::guard('teacher')->loginUsingId($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function AuthenticateCookie(Request $request): bool
    {
        try {
            $token = Cookie::get('remember');
            //dd($token);
            $email = null;
            $type  = 'Admin';
            if ($token) {
                $remember = Crypt::decrypt($token); // decrypt the token
                                                    //dd($remember);
                $values = explode('_', $remember);
                //dd($values);

                // $values expected: [ip, userAgent, teacher_id]
                $ipMatch    = isset($values[0]) && $values[0] === $request->ip();
                $agentMatch = isset($values[1]) && $values[1] === $request->userAgent();
                $teacher    = Teacher::find($values[2]);

                if ($teacher) {
                    $email = Adminchecker::LoginTeacher($teacher->id)->email;
                    $type  = AdminChecker::isMeAdmin() ? 'Super Admin' : 'Admin';
                    if ($ipMatch && $agentMatch && isset($values[2])) {
                        AdminChecker::SessionCreate($request);
                        return true;
                    }

                    Audit::Set('Cache Cookie Validation', 'Denied Due to Change in Environment', 'fail', $request, $type, $email);
                    Cookie::queue(Cookie::forget('remember'));
                    return false;
                }
                Audit::Set('Cache Cookie Validation', 'User Not Found(Deleted or Cookie Altered)', 'fail', $request, $type);
                Cookie::queue(Cookie::forget('remember'));
                return false;
            }
            Audit::Set('Cookie Authentication', 'Expired Cookie Certificate', 'fail', $request, $type);
            Cookie::queue(Cookie::forget('remember')); // clear invalid session
            return false;

        } catch (\Exception $e) {
            Audit::Set('Cookie Authentication', 'Invalid Cookie Certificate', 'fail', $request, 'Admin');
            Cookie::forget('remember');
            return false;
        }
    }

    public static function SessionCreate(Request $request): bool
    {
        try {
            $ip    = $request->ip();
            $agent = $request->userAgent();

            $token = Crypt::encrypt($ip . '_' . $agent);

            Session::put('session_token', $token);
            $addedTime = 1;
            Session::put('session_timeout', Carbon::now()->addMinutes($addedTime));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function SessionCheck(Request $request): bool
    {
        try {
            $token = Session::get('session_token', '');

            if ($token === '') {
                Session::forget(['session_token', 'session_timeout']);
                Auth::guard('teacher')->logout();
                return false;
            }

            $decrypted    = Crypt::decrypt($token);
            [$ip, $agent] = explode('_', $decrypted);

            if ($ip !== $request->ip() || $agent !== $request->userAgent()) {
                Session::forget(['session_token', 'session_timeout']);
                Auth::guard('teacher')->logout();
                return false;
            }

            $timeout = Session::get('session_timeout', Carbon::now());

            if (Carbon::now()->greaterThanOrEqualTo($timeout)) {
                Session::forget(['session_token', 'session_timeout']);
                Auth::guard('teacher')->logout();
                return false;
            }

            $addedTime = 1;
            Session::put('session_timeout', Carbon::now()->addMinutes($addedTime));
            return true;
        } catch (Exception $e) {
            Session::forget(['session_token', 'session_timeout']);
            Auth::guard('teacher')->logout();
            return false;
        }
    }
    public static function GetPlayer($id){
        try{
            return User::findOrFail($id);
        }catch(Throwable){
            return null;
        }
    }
}
