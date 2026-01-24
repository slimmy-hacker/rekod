@extends('layouts.my_app')

@section('title', 'Approve Weekly Reports')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">

    <h1 class="text-2xl font-bold mb-6">Weekly Reports Approval</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border table-auto">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Student</th>
                <th class="border p-2">Week</th>
                <th class="border p-2">Report</th>
                <th class="border p-2">Supervisor Comment</th>
                <th class="border p-2">Approval</th>
            </tr>
        </thead>
        <tbody>
        @forelse($weeklyReports as $report)
            {{-- REMOVED the check for attachmentStudent because we use student_id now --}}
            <tr>
                <td class="border p-2">
    {{-- Path: Report -> AttachmentStudent -> Student -> User -> Name --}}
    @if($report->attachmentStudent && $report->attachmentStudent->student && $report->attachmentStudent->student->user)
        <span class="font-semibold">{{ $report->attachmentStudent->student->user->name }}</span>
        <br>
        <small class="text-gray-500">{{ $report->attachmentStudent->student->admission_number }}</small>
    @else
        <span class="text-red-500">Unknown Student</span>
    @endif
</td>

                <td class="border p-2">
                    Week {{ $report->week_id }}
                </td>

                <td class="border p-2">
                    {{ $report->weekly_report }}
                </td>

                <td class="border p-2">
                    @if(!$report->is_approved)
                        <form method="POST" action="{{ route('industrial_supervisor.weekly-reports.approve', $report->id) }}">
                            @csrf
                            <textarea
                                name="industrial_supervisor_comment"
                                rows="3"
                                class="w-full border rounded p-2"
                                required
                            ></textarea>
                    @else
                        {{ $report->industrial_supervisor_comment }}
                    @endif
                    
                </td>

                <td class="border p-2 text-center">
                    @if(!$report->is_approved)
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">
                            Approve
                        </button>
                        </form>
                    @else
                        <span class="text-green-600 font-semibold">Approved</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center p-4 text-gray-500">
                    No weekly reports found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>
@endsection