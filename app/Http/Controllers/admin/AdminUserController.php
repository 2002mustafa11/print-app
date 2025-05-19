<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\BalanceChanged;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'wallet_balance' => 'required|numeric',
        ]);
        
        $oldBalance = $user->wallet_balance;
        $newBalance = $validated['wallet_balance'];
        $difference = $newBalance - $oldBalance;
    
        if ($difference != 0) {
            $user->wallet_balance = $newBalance;
            $user->save();
    
            $type = $difference > 0 ? 'deposit' : 'withdrawal';
    
            $user->walletTransactions()->create([
                'type' => $type,
                'amount' => abs($difference),
                'description' => 'تعديل من قبل المسؤول. الرصيد السابق: ' . $oldBalance . '، الرصيد الجديد: ' . $newBalance,
            ]);
            $user->notify(new BalanceChanged($difference, $user->wallet_balance));

        }
    
        return redirect()->route('admin.users.index')->with('success', 'تم تعديل رصيد المستخدم وتسجيل التحويل.');
    }
    

    // حذف مستخدم
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم');
    }
}
