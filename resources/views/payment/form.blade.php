@extends('layouts.matare')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">إجراء عملية دفع</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 border border-red-400 rounded p-4 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('payment.process') }}" class="bg-white shadow rounded p-6 space-y-4">
        @csrf

        <!-- المبلغ بالجنيه -->
        <div>
            <label for="amount_egp" class="block text-gray-700 font-semibold mb-1">المبلغ بالجنيه:</label>
            <input type="number" id="amount_egp" name="amount_egp" placeholder="مثال: 100" required min="1"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <!-- بيانات العنصر المخفي للطباعة -->
        <input type="hidden" name="items[0][name]" value="PDF Print">
        <input type="hidden" name="items[0][description]" value="Print PDF file">
        <input type="hidden" name="items[0][quantity]" value="1">

        <div class="text-center">
            <button type="submit"
                    class="bg-blue-600 text-gray px-6 py-2 rounded hover:bg-blue-700 transition">
                إرسال للدفع
            </button>
        </div>
    </form>
</div>
@endsection

