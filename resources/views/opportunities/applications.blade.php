@extends('layouts.my_app')

@section('title', 'Applications for ' . $opportunity->title)

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Applications for: {{ $opportunity->title }}</h1>

    @if($opportunity->applications->isEmpty())
        <p>No applications found for this opportunity.</p>
    @else
        <table class="w-full border border-gray-300 border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2">Applicant Name</th>
                    <th class="border border-gray-300 p-2">Cover Letter</th>
                    <th class="border border-gray-300 p-2">CV</th>
                    <th class="border border-gray-300 p-2">Applied At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opportunity->applications as $application)
                <tr>
                    <td class="border border-gray-300 p-2">{{ $application->user->name ?? 'N/A' }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->cover_letter }}</td>
                    <td class="border border-gray-300 p-2">
                        @if($application->cv_path)
                            <a href="{{ asset('storage/' . $application->cv_path) }}" target="_blank">View CV</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2">{{ $application->applied_at->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
