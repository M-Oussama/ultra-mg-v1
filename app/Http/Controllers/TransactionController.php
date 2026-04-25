<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index($cashbook_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $cashbook = Cashbook::where('id', $cashbook_id)->firstOrFail();

        // Security check
        if ($user->organization_id) {
            if ($cashbook->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($cashbook->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $transactions = $cashbook->transactions()->latest('transaction_date')->latest('id')->get();
        return response()->json($transactions);
    }

    public function store(Request $request, $cashbook_id)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $user = Auth::user();
        $cashbook = Cashbook::where('id', $cashbook_id)->firstOrFail();

        // Security check
        if ($user->organization_id) {
            if ($cashbook->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($cashbook->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $transaction = Transaction::create([
            'cashbook_id' => $cashbook_id,
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'note' => $request->note,
            'transaction_date' => $request->transaction_date,
        ]);

        return response()->json([
            'message' => 'Transaction added successfully',
            'transaction' => $transaction
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $user = Auth::user();
        $transaction = Transaction::where('id', $id)->firstOrFail();

        // Security check
        if ($user->organization_id) {
            if ($transaction->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($transaction->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $transaction->update($request->only(['type', 'amount', 'note', 'transaction_date']));

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)->firstOrFail();

        // Security check
        if ($user->organization_id) {
            if ($transaction->organization_id !== $user->organization_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($transaction->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
