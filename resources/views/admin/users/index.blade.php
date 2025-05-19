@extends('layouts.matare')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-4">قائمة المستخدمين</h2>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 font-semibold text-gray-700">الاسم</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">البريد</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">الرصيد</th>
                    <th class="px-6 py-3 font-semibold text-gray-700">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">{{ $user->wallet_balance }} ج</td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">تعديل</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('هل أنت متأكد؟')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
