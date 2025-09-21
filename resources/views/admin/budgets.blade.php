@extends('layouts.app')

@section('title', 'Budgets')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Budgets</h1>

    <form action="{{ route('admin.budgets.store') }}" method="POST" enctype="multipart/form-data" class="mb-6">
        @csrf
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm">Staff Number</label>
                <input type="text" name="staffnumber" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-sm">Grade</label>
                <input type="text" name="grade" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-sm">Lecturer Name</label>
                <input type="text" name="lecturer_name" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-sm">Daily Allowance</label>
                <input type="number" step="0.01" name="daily_allowance" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-sm">Transport (Town)</label>
                <input type="number" step="0.01" name="transport_town" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block text-sm">Totals</label>
                <input type="number" step="0.01" name="totals" class="w-full border p-2 rounded">
            </div>
            <div class="col-span-2">
                <label class="block text-sm">Upload Student List (CSV)</label>
                <input type="file" name="student_list_file" class="w-full border p-2 rounded">
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Budget</button>
    </form>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">Staff #</th>
