
@extends('layouts.my_app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Reports</h1>

    <div class="bg-white shadow rounded-lg p-4">
        <p class="mb-4">Download reports about students and evaluations.</p>
        <a href="{{ route('company.reports.export', 'pdf') }}" class="bg-red-600 text-white px-4 py-2 rounded">Export PDF</a>
        <a href="{{ route('company.reports.export', 'excel') }}" class="bg-green-600 text-white px-4 py-2 rounded ml-2">Export Excel</a>
    </div>

    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Summary</h2>
        <ul class="list-disc pl-5">
            <li>Total Students Hosted: {{ $stats['students_count'] ?? 0 }}</li>
            <li>Total Evaluations Submitted: {{ $stats['evaluations_count'] ?? 0 }}</li>
            <li>Active Opportunities: {{ $stats['opportunities_count'] ?? 0 }}</li>
        </ul>
    </div>
</div>
@endsection
