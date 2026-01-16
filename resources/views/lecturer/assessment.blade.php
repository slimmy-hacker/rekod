@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-3xl">
    <h1 class="text-3xl font-bold mb-6">School Supervisor Assessment</h1>

    <form action="{{ route('attachment-assessment.school.store') }}" method="POST">
        @csrf
        {{-- Updated name to match your controller validation --}}
        <input type="hidden" name="attachment_student_id" value="{{ $student->id ?? '' }}">

        <div class="space-y-6">
            {{-- 1. Practical Orientation --}}
            <div class="border-b pb-4">
                <label for="practical_orientation_marks" class="block font-medium">Practical Orientation Marks (0-5)</label>
                <input type="number" min="0" max="5" id="practical_orientation_marks" name="practical_orientation_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="practical_orientation_remarks" class="block font-medium">Practical Orientation Remarks</label>
                <textarea id="practical_orientation_remarks" name="practical_orientation_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>

            {{-- 2. Intellectual Activity --}}
            <div class="border-b pb-4">
                <label for="intellectual_activity_marks" class="block font-medium text-red-700">Intellectual Activity Marks (0-5)</label>
                <input type="number" min="0" max="5" id="intellectual_activity_marks" name="intellectual_activity_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="intellectual_activity_remarks" class="block font-medium">Intellectual Activity Remarks</label>
                <textarea id="intellectual_activity_remarks" name="intellectual_activity_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>

            {{-- 3. Independence --}}
            <div class="border-b pb-4">
                <label for="independence_marks" class="block font-medium">Independence Marks (0-5)</label>
                <input type="number" min="0" max="5" id="independence_marks" name="independence_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="independence_remarks" class="block font-medium">Independence Remarks</label>
                <textarea id="independence_remarks" name="independence_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>

            {{-- 4. Communication --}}
            <div class="border-b pb-4">
                <label for="communication_marks" class="block font-medium">Communication Marks (0-5)</label>
                <input type="number" min="0" max="5" id="communication_marks" name="communication_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="communication_remarks" class="block font-medium">Communication Remarks</label>
                <textarea id="communication_remarks" name="communication_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>

            {{-- 5. Technology and Skills --}}
            <div class="border-b pb-4">
                <label for="technology_and_skills_marks" class="block font-medium">Technology and Skills Marks (0-5)</label>
                <input type="number" min="0" max="5" id="technology_and_skills_marks" name="technology_and_skills_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="technology_and_skills_remarks" class="block font-medium">Technology and Skills Remarks</label>
                <textarea id="technology_and_skills_remarks" name="technology_and_skills_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>

            {{-- 6. Innovativeness --}}
            <div class="pb-4">
                <label for="innovativeness_marks" class="block font-medium">Innovativeness Marks (0-5)</label>
                <input type="number" min="0" max="5" id="innovativeness_marks" name="innovativeness_marks" class="w-full border rounded p-2 mb-2" required>
                
                <label for="innovativeness_remarks" class="block font-medium">Innovativeness Remarks</label>
                <textarea id="innovativeness_remarks" name="innovativeness_remarks" rows="2" class="w-full border rounded p-2" required></textarea>
            </div>
        </div>

        <button type="submit" class="mt-6 px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full md:w-auto">
            Submit School Assessment
        </button>
    </form>
</div>
@endsection