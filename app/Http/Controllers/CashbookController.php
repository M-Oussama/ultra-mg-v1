<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Services\CashbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashbookController extends Controller
{
    protected $cashbookService;

    public function __construct(CashbookService $cashbookService)
    {
        $this->cashbookService = $cashbookService;
    }

    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $query = Cashbook::query();
        
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        } else {
            $query->where('user_id', $user->id);
        }

        $cashbooks = $query->withCount('transactions')->get()->map(function($cb) {
            return [
                'id' => $cb->id,
                'name' => $cb->name,
                'description' => $cb->description,
                'balance' => $cb->balance,
                'transaction_count' => $cb->transactions_count,
                'last_transaction_date' => $cb->transactions()->latest('transaction_date')->value('transaction_date'),
                'created_at' => $cb->created_at,
            ];
        });

        return response()->json($cashbooks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();

        $cashbook = Cashbook::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
        ]);

        return response()->json([
            'message' => 'Cashbook created successfully',
            'cashbook' => $cashbook
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $cashbook = Cashbook::where('id', $id)->firstOrFail();

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

        $summary = $this->cashbookService->getSummary($cashbook);

        return response()->json([
            'cashbook' => $cashbook,
            'summary' => $summary,
            'recent_transactions' => $cashbook->transactions()->latest('transaction_date')->limit(10)->get()
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $cashbook = Cashbook::where('id', $id)->firstOrFail();

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

        $cashbook->delete();

        return response()->json(['message' => 'Cashbook deleted successfully']);
    }

    public function summary($id)
    {
        return $this->show($id);
    }
}
