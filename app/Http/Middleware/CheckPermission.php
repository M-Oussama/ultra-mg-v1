<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $action
     * @param  string  $subject
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action, $subject)
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($action, $subject)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to ' . $action . ' ' . $subject . '.'
            ], 403);
        }

        return $next($request);
    }
}
