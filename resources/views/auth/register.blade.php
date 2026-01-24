@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-10">
    <div class="w-full max-w-lg bg-white p-8 rounded shadow">
        <h2 class="text-2xl font-bold text-center mb-6">User Registration</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="regForm">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full mt-1 p-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full mt-1 p-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Register As</label>
                <select name="role" id="role" required class="w-full mt-1 p-2 border rounded">
                    <option value="">-- Select Role --</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="lecturer" {{ old('role') == 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                </select>
            </div>
            <div id="student-fields" class="hidden">
    <div class="mb-4">
        <label class="block text-sm font-medium">Registration Number</label>
        <input type="text" name="reg_no" value="{{ old('reg_no') }}" class="w-full mt-1 p-2 border rounded">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium">Year of Study</label>
        <select name="year_of_study" class="w-full mt-1 p-2 border rounded">
            <option value="">-- Select Year --</option>
            <option value="Year 1">Year 1</option>
            <option value="Year 2">Year 2</option>
            <option value="Year 3">Year 3</option>
            <option value="Year 4">Year 4</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium">Program</label>
        <select name="program_id" class="w-full mt-1 p-2 border rounded">
            <option value="">-- Select Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium">Phone Number</label>
        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full mt-1 p-2 border rounded">
    </div>
</div>
            <div id="lecturer-fields" class="hidden">
                <div class="mb-4">
                    <label class="block text-sm">Staff Number</label>
                    <input type="text" name="staff_number" value="{{ old('staff_number') }}" class="w-full mt-1 p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm">Phone Number</label>
                    <input type="text" name="office_phone" value="{{ old('office_phone') }}" class="w-full mt-1 p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm">Job Grade</label>
                    <input type="text" name="job_grade" value="{{ old('job_grade') }}" class="w-full mt-1 p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Department</label>
                    <select name="department" class="w-full mt-1 p-2 border rounded">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
    <option value="{{ $dept->id }}" {{ old('department') == $dept->id ? 'selected' : '' }}>
        {{ $dept->name }}
    </option>
@endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required class="w-full mt-1 p-2 border rounded">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full mt-1 p-2 border rounded">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Register
            </button>
        </form>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('student-fields');
    const lecturerFields = document.getElementById('lecturer-fields');

    function toggleFields() {
        const role = roleSelect.value;
        
        const setupSection = (section, isVisible) => {
            section.classList.toggle('hidden', !isVisible);
            section.querySelectorAll('input, select').forEach(el => el.disabled = !isVisible);
        };

        setupSection(studentFields, role === 'student');
        setupSection(lecturerFields, role === 'lecturer');
    }

    roleSelect.addEventListener('change', toggleFields);
    window.addEventListener('load', toggleFields);
</script>
@endsection