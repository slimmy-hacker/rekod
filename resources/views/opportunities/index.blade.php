@extends('layouts.my_app')

@section('title', 'Opportunities')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Available Opportunities</h1>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Company: Create Form --}}
    @if ($isCompany)
        <div class="bg-white p-6 rounded-xl shadow-md mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700">Post a New Opportunity</h2>
            <form method="POST" action="{{ route('opportunities.store') }}" class="grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600">Title</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Location</label>
                    <input type="text" name="location" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600">Description</label>
                    <textarea name="description" class="w-full border-gray-300 rounded-lg p-2" rows="3" required></textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Expiry (days)</label>
                    <input type="number" name="expiry_days" min="1" max="90" class="w-full border-gray-300 rounded-lg p-2" required>
                </div>
                <div class="md:col-span-2">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Post Opportunity</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Opportunities List --}}
    <div class="grid gap-6">
        @foreach ($opportunities as $opportunity)
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold text-gray-800">{{ $opportunity->title }}</h3>
                <p class="text-gray-600 mt-2">{{ $opportunity->description }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    📍 {{ $opportunity->location }} | 
                    ⏳ Expires: {{ \Carbon\Carbon::parse($opportunity->expiry_date)->format('M d, Y') }}
                </p>

                @if(!$isCompany)
                    <p class="text-sm text-gray-500 mt-1">🏢 Posted by: {{ $opportunity->company->name ?? 'Company' }}</p>

                    <!-- {{-- Apply button for students only --}}
                    <form action="{{ route('opportunities.apply', $opportunity->id) }}" method="GET" class="mt-3">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Apply
                        </button>
                    </form> -->
                @else
                    {{-- View Applications button for companies only --}}
                    <a href="{{ route('opportunities.applications', ['opportunity' => $opportunity->id]) }}" 
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mt-3">
                       View Applications
                    </a>

                    {{-- Delete option for companies --}}
                    <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-600 text-white px-4 py-1 rounded-lg hover:bg-red-700 text-sm">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
