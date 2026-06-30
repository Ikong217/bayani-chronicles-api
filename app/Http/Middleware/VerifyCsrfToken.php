<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //'api/user/insert',
        'api/user/login',
        'api/user/verify-otp',
        'api/user/resend-otp',
        'api/user/request/username/change',
        'api/user/request/change/email',
        'api/user/request/change/email/awaitcode',
        'api/user/request/change/email/verify',
        '/questions/request',
        '/score/start',
        '/score/update',
        '/leaderboard/request',
        '/player/data/request',
        '/player/access/request',
        '/users/forgot/request-code',
        '/users/forgot/verify',
        '/users/reset/submit',
        '/questions/summative/request',
    ];
}
