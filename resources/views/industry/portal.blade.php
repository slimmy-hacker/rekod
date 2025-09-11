@extends('navigation')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Industry Portal Dashboard</h2>
@endsection

@section('content')
<div class="py-6 px-4">
    <div class="max-w-7xl mx-auto space-y-8">

       
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Share Attachment Opportunities</h3>
            <p class="mb-2">Post available attachment opportunities for students to apply.</p>
            <a href="{{ route('industry.opportunities') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Share Opportunity
            </a>
        </div>

       
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Received Applications</h3>
            <p class="mb-2">View applications submitted by students and approve or reject them.</p>
            <a href="{{ route('industry.applications') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                View Applications
            </a>
        </div>

        
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Give Feedback on Student Performance</h3>
            <p class="mb-2">Submit feedback for students under your supervision.</p>
            <a href="{{ route('industry.feedback') }}" class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
                Give Feedback
            </a>
        </div>

    </div>
</div>
@endsection
