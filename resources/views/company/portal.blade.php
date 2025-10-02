@extends('layouts.my_app')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Welcome to the Company Portal</h1>
        <p class="mt-2 text-gray-600">
            Manage your attachment opportunities, students, documents, and reports all in one place.
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700">Opportunities</h2>
            <p class="text-gray-500">Create and manage internship/attachment opportunities.</p>
            <a href="{{ route('company.opportunities') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Manage</a>
        </div>

        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700">Students</h2>
            <p class="text-gray-500">View attached students and monitor progress.</p>
            <a href="{{ route('company.students') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">View</a>
        </div>

        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700">Documents</h2>
            <p class="text-gray-500">Upload and manage official documents.</p>
            <a href="{{ route('company.documents') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload</a>
        </div>
    </div>

    <!-- More Features -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700">Reports</h2>
            <p class="text-gray-500">View student evaluations and performance reports.</p>
            <a href="{{ route('company.reports') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">View Reports</a>
        </div>

        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold text-gray-700">Settings</h2>
            <p class="text-gray-500">Update your company profile and portal preferences.</p>
            
        </div>
    </div>
</div>
@endsection
