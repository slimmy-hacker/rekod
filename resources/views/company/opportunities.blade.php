@extends('layouts.my_app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Manage Attachment Opportunities</h2>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Create New Opportunity --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold mb-4">Create New Opportunity</h3>
        <form action="{{ route('company.opportunities.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="title" class="block font-semibold">Opportunity Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="w-full border rounded p-2" required>
                @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block font-semibold">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full border rounded p-2" required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deadline" class="block font-semibold">Application Deadline</label>
                <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                       class="w-full border rounded p-2" required>
                @error('deadline') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Opportunity
            </button>
        </form>
    </div>

    {{-- List of Opportunities --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold mb-4">Your Opportunities</h3>

        @if($opportunities->isEmpty())
            <p class="text-gray-600">No opportunities posted yet.</p>
        @else
            <table class="w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2 border">Title</th>
                        <th class="p-2 border">Description</th>
                        <th class="p-2 border">Deadline</th>
                        <th class="p-2 border">Applications</th>
                        <th class="p-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opportunities as $opportunity)
                        <tr>
                            <td class="p-2 border">{{ $opportunity->title }}</td>
                            <td class="p-2 border">{{ Str::limit($opportunity->description, 50) }}</td>
                            <td class="p-2 border">{{ $opportunity->deadline }}</td>
                            <td class="p-2 border">
                                <a href="{{ route('company.opportunities', $opportunity->id) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    View ({{ $opportunity->applications->count() }})
                                </a>
                            </td>
                            <td class="p-2 border">
                                <form action="{{ route('company.opportunities.destroy', $opportunity->id) }}"
                                      method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
