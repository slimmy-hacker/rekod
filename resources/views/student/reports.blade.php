@extends('layouts.my_app')

@section('title', 'Weekly & Final Reports')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Weekly & Final Attachment Reports</h1>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Weekly Report Form --}}
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-3">Submit Weekly Report</h2>
        <form method="POST" action="{{ route('student.reports.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Week Number</label>
                    <select name="week_number" class="w-full border p-2 rounded" required>
                        <option value="">Select Week</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">Week {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" name="report_date" class="w-full border p-2 rounded" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Report Description</label>
                <textarea name="description" rows="5" class="w-full border p-2 rounded" placeholder="Write what you accomplished this week..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Upload Supporting File (optional)</label>
                <input type="file" name="report_file" class="w-full border p-2 rounded">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Submit Weekly Report
            </button>
        </form>
    </div>

    {{-- ✅ Final Report Form --}}
    <div class="mb-8 border-t pt-6">
        <h2 class="text-xl font-semibold mb-3">Submit Final Report</h2>
        <form method="POST" action="{{ route('student.finalReport.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Summary of Attachment Experience</label>
                <textarea name="summary" rows="6" class="w-full border p-2 rounded" placeholder="Summarize your overall attachment experience..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Upload Final Report File (PDF/DOCX)</label>
                <input type="file" name="final_report_file" class="w-full border p-2 rounded" accept=".pdf,.doc,.docx" required>
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Submit Final Report
            </button>
        </form>
    </div>

    {{-- ✅ List of Submitted Reports --}}
    <div class="border-t pt-6">
        <h2 class="text-xl font-semibold mb-3">Submitted Reports</h2>
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Week</th>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Description</th>
                    <th class="p-2 border">File</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="p-2 border">{{ ucfirst($report->type) }}</td>
                        <td class="p-2 border">{{ $report->week_number ?? '-' }}</td>
                        <td class="p-2 border">{{ $report->report_date->format('Y-m-d') }}</td>
                        <td class="p-2 border">{{ Str::limit($report->description, 50) }}</td>
                        <td class="p-2 border">
                            @if($report->file_path)
                                <a href="{{ asset('storage/' . $report->file_path) }}" class="text-blue-600 underline" target="_blank">Download</a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-2 text-center text-gray-500">No reports submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
