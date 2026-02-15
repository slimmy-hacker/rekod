@extends('layouts.my_app')

@section('title', 'School Supervisor Assessment')

@section('content')
<div class="max-w-4xl mx-auto mt-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        {{-- Header --}}
        <div class="bg-gray-800 p-4">
            <h2 class="text-white font-bold text-xl uppercase tracking-wider">School Supervisor Assessment</h2>
        </div>

        <div class="p-6">
            {{-- If assessment exists, show card --}}
            @if($school_assessment)
                <div class="text-center py-8">
                    <h3 class="text-2xl font-bold text-gray-800">Assessment Submitted</h3>
                    <p class="text-gray-500 mt-2">This assessment has already been submitted. You cannot edit it.</p>

                    <div class="mt-6 bg-gray-50 border rounded-lg p-4 text-left w-full">
                        @foreach([
                            'practical_orientation' => 'Practical Orientation',
                            'intellectual_activity' => 'Intellectual Activity',
                            'independence' => 'Independence',
                            'communication' => 'Communication',
                            'technology_and_skills' => 'Technology & Skills',
                            'innovativeness' => 'Innovativeness'
                        ] as $field => $label)
                            <div class="mb-4">
                                <span class="block text-xs font-semibold uppercase text-gray-400">{{ $label }}</span>
                                <span class="text-sm font-bold text-gray-700">{{ $school_assessment->{$field.'_marks'} }} / 5</span>
                                <p class="text-gray-600 text-sm">{{ $school_assessment->{$field.'_remarks'} }}</p>
                            </div>
                        @endforeach

                        <div class="mt-4 font-bold text-gray-800">
                            Total Marks: {{ $school_assessment->lecturer_total_marks ?? 0 }} / 30
                        </div>
                    </div>
                </div>

            {{-- Else, show the form --}}
            @else
                <form action="{{ route('attachment-assessment.school.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="attachment_student_id" value="{{ $student->id }}">

                    @foreach([
                        'practical_orientation' => 'Practical Orientation',
                        'intellectual_activity' => 'Intellectual Activity',
                        'independence' => 'Independence',
                        'communication' => 'Communication',
                        'technology_and_skills' => 'Technology & Skills',
                        'innovativeness' => 'Innovativeness'
                    ] as $field => $label)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">{{ $label }} Marks (0-5)</label>
                                <input type="number" name="{{ $field }}_marks" min="0" max="5" required class="w-full border p-2 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">{{ $label }} Remarks</label>
                                <textarea name="{{ $field }}_remarks" rows="2" required class="w-full border p-2 rounded"></textarea>
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 font-bold">
                            Submit Assessment
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
