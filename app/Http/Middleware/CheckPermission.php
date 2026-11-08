<?php

namespace App\Http\Middleware;

use App\Models\Cashbook;
use App\Models\CashbookMember;
use App\Models\Transaction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = $request->user() ?? Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->isGlobalAdmin()) {
            return $next($request);
        }

        // Direct or role permission check
        if ($user->hasPermission($action, $subject)) {
            return $next($request);
        }

        // Contextual allowance for cashbooks
        if ($subject === 'cashbooks') {
            if ($action === 'view' && $user->hasPermission('list', 'cashbooks')) {
                return $next($request);
            }

            $cashbookId = $request->route('id');
            if ($cashbookId && is_numeric($cashbookId) && in_array($action, ['view', 'list'])) {
                $isAuthorized = Cashbook::where('id', $cashbookId)
                    ->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->orWhereHas('members', function ($m) use ($user) {
                                $m->where('users.id', $user->id);
                            });
                    })->exists();

                if ($isAuthorized) {
                    return $next($request);
                }
            }
        }

        // Contextual allowance for transactions:
        if ($subject === 'transactions') {
            // If user has the corresponding cashbook permission
            if ($user->hasPermission($action, 'cashbooks')) {
                return $next($request);
            }
            if ($action === 'list' && ($user->hasPermission('list', 'cashbooks') || $user->hasPermission('view', 'cashbooks'))) {
                return $next($request);
            }

            // Route: /api/cashbooks/{id}/transactions
            $cashbookId = $request->route('cashbook_id') ?? $request->route('id');
            if ($cashbookId && is_numeric($cashbookId) && $request->is('*cashbooks*')) {
                $isAuthorized = Cashbook::where('id', $cashbookId)
                    ->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->orWhereHas('members', function ($m) use ($user) {
                                $m->where('users.id', $user->id);
                            });
                    })->exists();

                if ($isAuthorized) {
                    return $next($request);
                }
            }

            // Route: /api/transactions/{id}
            $transactionId = $request->route('id');
            if ($transactionId && is_numeric($transactionId) && $request->is('*transactions*')) {
                $transaction = Transaction::find($transactionId);
                if ($transaction) {
                    $isAuthorized = Cashbook::where('id', $transaction->cashbook_id)
                        ->where(function ($query) use ($user) {
                            $query->where('user_id', $user->id)
                                ->orWhereHas('members', function ($m) use ($user) {
                                    $m->where('users.id', $user->id);
                                });
                        })->exists();

                    if ($isAuthorized) {
                        return $next($request);
                    }
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not have permission to ' . $action . ' ' . $subject . '.'
        ], 403);
    }
}
