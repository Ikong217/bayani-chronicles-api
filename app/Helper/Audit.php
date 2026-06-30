<?php
namespace App\Helper;

use App\Models\AuditLog;
use Exception;
use Illuminate\Http\Request;

class Audit
{
    public static function Set(string $action, string $response, string $status, Request $request, string $type = null, string $username = null): bool
    {
        try {
            AuditLog::create([
                'ip'       => $request->ip(),
                'device'   => $request->userAgent(),
                'type'     => $type,
                'username' => $username,
                'action'   => $action,
                'response' => $response,
                'status'   => $status,
            ]);
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
