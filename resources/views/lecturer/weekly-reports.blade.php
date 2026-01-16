@extends('layouts.my_app')

@section('title', 'Lecturer Weekly Reports Review')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Weekly Reports: Academic Review</h1>
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Lecturer Portal</span>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-200 shadow-sm">
            <thead>
                <tr class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                    <th class="border p-3">Student Name</th>
                    <th class="border p-3 w-20 text-center">Week</th>
                    <th class="border p-3">Logbook Entry</th>
                    <th class="border p-3">Industry Supervisor Feedback</th>
                    <th class="border p-3">Academic Feedback (Your Comment)</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            @forelse($weeklyReports as $report)
                <tr class="hover:bg-gray-50 transition">
                    <td class="border p-3 font-semibold text-blue-900">
                        {{ $report->attachmentStudent->student->user->name ?? 'Unknown Student' }}
                    </td>

                    <td class="border p-3 text-center font-mono">
                        #{{ $report->week_id }}
                    </td>

                    <td class="border p-3 text-sm leading-relaxed">
                        <div class="max-h-32 overflow-y-auto">
                            {{ $report->weekly_report }}
                        </div>
                    </td>

                    <td class="border p-3 text-sm">
                        @if($report->is_approved)
                            <div class="flex items-center mb-1">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-bold uppercase">
                                    Approved
                                </span>
                            </div>
                            <p class="italic text-gray-600 border-l-2 border-gray-300 pl-2">
                                "{{ $report->industrial_supervisor_comment ?? 'No written comment' }}"
                            </p>
                        @else
                            <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded text-xs italic">
                                ⏳ Pending Supervisor Approval
                            </span>
                        @endif
                    </td>

                    <td class="border p-3">
                        {{-- Form for Lecturer to provide academic feedback --}}
                        <form method="POST" action="{{ route('lecturer.weekly-reports.update', $report->id) }}">
                            @csrf
                            @method('PUT')
                            
                            <textarea
                                name="lecturer_comment"
                                rows="3"
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                                placeholder="Write academic feedback here..."
                                required
                            >{{ old('lecturer_comment', $report->lecturer_comment) }}</textarea>
                            
                            <button type="submit" class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-4 rounded shadow-sm transition duration-150">
                                Save Academic Feedback
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center p-8 text-gray-500 bg-gray-50">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No weekly reports found for your assigned students.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection