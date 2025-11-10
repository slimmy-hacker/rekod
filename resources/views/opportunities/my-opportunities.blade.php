@extends('layouts.my_app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-200">
    <h2 class="text-xl font-bold mb-4">My Posted Opportunities</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-3">{{ session('success') }}</div>
    @endif

    <a href="{{ route('opportunities.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded-lg mb-4 inline-block">
        + New Opportunity
    </a>

    <table class="w-full border-collapse border text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Title</th>
                <th class="border p-2">Location</th>
                <th class="border p-2">Expiry Date</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opportunities as $opportunity)
                <tr>
                    <td class="border p-2">{{ $opportunity->title }}</td>
                    <td class="border p-2">{{ $opportunity->location }}</td>
                    <td class="border p-2">{{ $opportunity->expiry_date->format('d M Y') }}</td>
                    <td class="border p-2">
                        @if($opportunity->is_expired)
                            <span class="text-red-600 font-semibold">Expired</span>
                        @else
                            <span class="text-green-600 font-semibold">Active</span>
                        @endif
                    </td>
                    <td class="border p-2 text-center">
                        <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" onsubmit="return confirm('Delete this opportunity?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center p-4 text-gray-500">No opportunities posted yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
