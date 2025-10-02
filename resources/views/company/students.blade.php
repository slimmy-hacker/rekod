
@extends('layouts.my_app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Attached Students</h1>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Course</th>
                <th class="p-2 border">Start Date</th>
                <th class="p-2 border">End Date</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td class="p-2 border">{{ $student->name }}</td>
                <td class="p-2 border">{{ $student->course }}</td>
                <td class="p-2 border">{{ $student->start_date }}</td>
                <td class="p-2 border">{{ $student->end_date }}</td>
                <td class="p-2 border">
                    <a href="{{ route('company.students.show', $student->id) }}" class="text-blue-600">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
