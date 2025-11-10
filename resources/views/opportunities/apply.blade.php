@extends('layouts.my_app')

@section('title', 'Apply for Opportunity')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Apply for: {{ $opportunity->title }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('opportunities.apply.submit', $opportunity->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="cover_letter" class="block text-sm font-medium text-gray-700 mb-1">Cover Letter</label>
            <textarea id="cover_letter" name="cover_letter" rows="6" required class="w-full border border-gray-300 rounded p-2">{{ old('cover_letter') }}</textarea>
        </div>

        <div class="mb-6">
            <label for="cv" class="block text-sm font-medium text-gray-700 mb-1">Upload CV (PDF, DOC, DOCX)</label>
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required class="w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Submit Application
        </button>
    </form>
</div>
@endsection
