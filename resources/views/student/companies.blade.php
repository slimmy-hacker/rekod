@extends('layouts.my_app')

@section('title', 'Company Details ')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Submit Company Details</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('student.companies.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block font-semibold">Company Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full border rounded p-2" required>
            @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="alias" class="block font-semibold">Company Alias</label>
            <input type="text" name="alias" id="alias" value="{{ old('alias') }}"
                   class="w-full border rounded p-2" required>
            @error('alias') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="branch" class="block font-semibold">Company Branch</label>
            <input type="text" name="branch" id="branch" value="{{ old('branch') }}"
                   class="w-full border rounded p-2" required>
            @error('branch') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="address" class="block font-semibold">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}"
                   class="w-full border rounded p-2" required>
            @error('address') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="contact" class="block font-semibold">Contact</label>
            <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                   class="w-full border rounded p-2" required>
            @error('contact') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="county" class="block font-semibold">County</label>
            <select name="county" id="county" class="w-full border rounded p-2" required>
                <option value="">-- Select County --</option>
                @foreach($counties as $code => $county)
                    <option value="{{ $code }}" {{ old('county') == $code ? 'selected' : '' }}>
                        {{ $code }} - {{ $county }}
                    </option>
                @endforeach
            </select>
            @error('county') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Submit
        </button>
    </form>
</div>
@endsection
