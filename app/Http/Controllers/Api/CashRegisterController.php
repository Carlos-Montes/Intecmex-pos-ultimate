<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashRegisterController extends Controller
{
    public function getStatus()
    {
        $session = CashRegisterSession::with(['account', 'user'])
            ->where('status', 'open')
            ->first();

        return response()->json([
            'opened' => $session !== null,
            'session' => $session,
            'accounts' => $session ? [] : Account::where('status', 1)
                ->orderBy('name')
                ->get(['id', 'account_number', 'name']),
        ]);
    }

    public function postOpen(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'opening_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $session = DB::transaction(function () use ($validated) {
            $account = Account::where('id', $validated['account_id'])
                ->where('status', 1)
                ->lockForUpdate()
                ->first();

            if (! $account) {
                throw ValidationException::withMessages([
                    'account_id' => 'La cuenta seleccionada no está activa.',
                ]);
            }

            return CashRegisterSession::create([
                'user_id' => Auth::id(),
                'account_id' => $account->id,
                'opening_amount' => $validated['opening_amount'],
                'status' => 'open',
                'opened_at' => now(),
            ]);
        });

        return response()->json([
            'type' => 'success',
            'title' => config('intecmex.app_name'),
            'msg' => 'La caja se abrió correctamente.',
            'session' => $session,
        ]);
    }
}
