@extends('layouts.matare')

@section('content')
<div class="container">
    <h2>تعديل بيانات المستخدم</h2>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- <label>الاسم:</label>
        <input type="text" name="name" value="{{ $user->name }}" required><br><br>

        <label>البريد:</label>
        <input type="email" name="email" value="{{ $user->email }}" required><br><br> --}}

        <label>رصيد المحفظة:</label>
        <input type="number" name="wallet_balance" value="{{ $user->wallet_balance }}"><br><br>

        <button type="submit">تحديث</button>
    </form>
</div>
@endsection
