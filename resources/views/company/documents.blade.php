
@extends('layouts.my_app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Documents</h1>

    <form method="POST" action="{{ route('company.documents.store') }}" enctype="multipart/form-data" class="mb-4">
        @csrf
        <label class="block font-medium">Upload Document</label>
        <input type="file" name="document" class="border rounded p-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
    </form>

    <h2 class="font-semibold mb-2">Available Documents</h2>
    <ul class="list-disc pl-5">
        @foreach($documents as $doc)
            <li>
                <a href="{{ asset('storage/documents/'.$doc->file) }}" class="text-blue-600" target="_blank">{{ $doc->file }}</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
