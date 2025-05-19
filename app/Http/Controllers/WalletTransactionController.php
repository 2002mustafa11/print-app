<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletTransactionController extends Controller
{
    public function index()
    {
        $transactions = WalletTransaction::where('user_id', Auth::id())
                            ->latest()
                            ->paginate(10);

        return view('wallet.index', compact('transactions'));
    }
}
