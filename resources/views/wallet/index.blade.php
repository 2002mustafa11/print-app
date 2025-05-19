@extends('layouts.matare')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6">التحويلات الصادرة</h2>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full text-sm text-left divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-6 py-3">المبلغ</th>
                    <th class="px-6 py-3">النوع</th>
                    <th class="px-6 py-3">description</th>
                    <th class="px-6 py-3">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700">
                @forelse ($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $tx->amount }} جنيه</td>
                        <td class="px-6 py-4">{{ $tx->type }}</td>
                        <td class="px-6 py-4">{{ $tx->description }}</td>
                        <td class="px-6 py-4">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">لا توجد تحويلات صادرة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
