<?php
namespace App\Http\Middleware;

use App\Helper\AdminChecker;
use App\Helper\Audit;
use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (AdminChecker::CheckTeacher()) {
            $email = AdminChecker::getUser()->email;
            $type  = AdminChecker::isMeAdmin() ? 'Super Admin' : 'Admin';
            if (AdminChecker::SessionCheck($request)) {
                return $next($request);
            }elseif(AdminChecker::AuthenticateCookie($request)){
                return $next($request);
            }
            Audit::Set('Idle Log Out', 'Forced logout after being idle for ' . config('session.lifetime') . ' minutes', 'fail',$request,$type,$email);

            return redirect()->route('login.show')->with('error', 'You have been logged out for being idle for a few minutes.');

        } elseif (AdminChecker::AuthenticateCookie($request)) {
            AdminChecker::SessionCreate($request);
            $email = AdminChecker::getUser()->email;
            $type  = AdminChecker::isAdmin(AdminChecker::getUser()->id) ? 'Super Admin' : 'Admin';
            Audit::Set('Auto Login', 'Cookie Certificate Verified', 'success', $request, $type, $email);
            return $next($request);
        }

        Audit::Set('Middleware', 'Access Denied in Teacher Controls', 'fail', $request, 'Admin');
        abort(401, "You are not allowed here!!");
    }
}
