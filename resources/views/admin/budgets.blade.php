@extends('layouts.my_app')

@section('title', 'Budgets')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Budget Management</h1>

    {{-- Budget Creation Form --}}
    <form action="{{ route('admin.budgets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="staffnumber" class="block text-sm font-medium text-gray-700">Staff Number</label>
                <input type="text" id="staffnumber" name="staffnumber" value="{{ old('staffnumber') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('staffnumber')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="grade" class="block text-sm font-medium text-gray-700">Grade</label>
                <input type="text" id="grade" name="grade" value="{{ old('grade') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('grade')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lecturer_name" class="block text-sm font-medium text-gray-700">Lecturer Name</label>
                <input type="text" id="lecturer_name" name="lecturer_name" value="{{ old('lecturer_name') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('lecturer_name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="daily_allowance" class="block text-sm font-medium text-gray-700">Daily Allowance (Ksh)</label>
                <input type="number" step="0.01" id="daily_allowance" name="daily_allowance" value="{{ old('daily_allowance') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('daily_allowance')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="transport_town" class="block text-sm font-medium text-gray-700">Transport (Town) (Ksh)</label>
                <input type="number" step="0.01" id="transport_town" name="transport_town" value="{{ old('transport_town') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('transport_town')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="totals" class="block text-sm font-medium text-gray-700">Total Amount (Ksh)</label>
                <input type="number" step="0.01" id="totals" name="totals" value="{{ old('totals') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('totals')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="student_list_file" class="block text-sm font-medium text-gray-700">Upload Student List (CSV)</label>
                <input type="file" id="student_list_file" name="student_list_file" accept=".csv"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('student_list_file')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                Save Budget
            </button>
        </div>
    </form>

    {{-- Budget List --}}
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Existing Budgets</h2>

        <table class="w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="border px-4 py-2 text-left">Staff #</th>
                    <th class="border px-4 py-2 text-left">Grade</th>
                    <th class="border px-4 py-2 text-left">Lecturer Name</th>
                    <th class="border px-4 py-2 text-left">Daily Allowance</th>
                    <th class="border px-4 py-2 text-left">Transport (Town)</th>
                    <th class="border px-4 py-2 text-left">Totals</th>
                    <th class="border px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $budget)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $budget->staffnumber }}</td>
                        <td class="border px-4 py-2">{{ $budget->grade }}</td>
                        <td class="border px-4 py-2">{{ $budget->lecturer_name }}</td>
                        <td class="border px-4 py-2">{{ number_format($budget->daily_allowance, 2) }}</td>
                        <td class="border px-4 py-2">{{ number_format($budget->transport_town, 2) }}</td>
                        <td class="border px-4 py-2">{{ number_format($budget->totals, 2) }}</td>
                        <td class="border px-4 py-2 text-center">
                            <form action="{{ route('admin.budgets.destroy', $budget->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">No budgets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
