@extends('layouts.matare')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">

    <h2 class="text-xl font-bold mb-6 text-center">طباعة ملف PDF</h2>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pdf.upload') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-medium">اختر ملف PDF:</label>
            <input type="file" name="pdf" accept="application/pdf" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">عدد النسخ:</label>
            <input type="number" name="copies" min="1" value="1" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">طريقة الطباعة:</label>
            <select name="is_color" class="w-full border rounded px-3 py-2">
                <option value="1">ملونة</option>
                <option value="0">أبيض وأسود</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 text-gray px-4 py-2 rounded hover:bg-blue-700 transition">
                طباعة
            </button>
        </div>
    </form>

</div>
@endsection
