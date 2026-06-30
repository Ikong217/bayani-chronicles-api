<?php
namespace App\Http\Middleware;

use App\Helper\AdminChecker;
use App\Helper\Audit;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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

        if (Auth::guard('teacher')->check()) {
            if(AdminChecker::isMeAdmin()){
                return $next($request);
            }
        }
        Audit::Set('Middleware','Access Denied Admin Access','fail',$request,'Super Admin');
        abort(403, 'You do not have admin access.');
    }

}
