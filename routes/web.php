<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('upload');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\PDFController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletTransactionController;
use App\Http\Controllers\admin\AdminUserController;

Route::middleware('auth')->group(function () {
    Route::get('/wallet', [WalletTransactionController::class, 'index'])->name('wallet.index');
    
    Route::get('/upload',fn()=> view('upload'))->name('upload');
    
    Route::post('/upload', [PDFController::class, 'uploadAndPrint'])->name('pdf.upload')->middleware(['auth']);
    
    Route::post('/payment/process', [PaymentController::class, 'paymentProcess'])->middleware('auth')->name('payment.process');
});
    Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::get('/payment', function () {
    return view('payment.form');
})->name('payment.form')->middleware(['auth']);


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class)->except(['create', 'store', 'show']);
});

Route::post('/notifications/mark-as-read/{id}', function ($id) {
    $notification = auth()->user()->unreadNotifications()->find($id);
    if ($notification) {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 404);
})->name('notifications.markAsRead');
