<!DOCTYPE html>
<html>
<head>
    <title>{{ ucfirst($portal) }} Registration</title>
</head>
<body>
    <h2>Register as {{ ucfirst($portal) }}</h2>

    <form method="POST" action="{{ route('register.portal.store', ['portal' => $portal]) }}">
    
        @csrf

        {{-- Common Fields --}}
        <div>
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        {{-- Student Fields --}}
        @if($portal === 'student')
            <div>
                <label>Registration Number</label>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}" required>
            </div>
            <div>
                <label>Course</label>
                <input type="text" name="course" value="{{ old('course') }}" required>
            </div>
            <div>
                <label>Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" required>
            </div>
        @endif

        {{-- Supervisor Fields --}}
        @if($portal === 'supervisor')
            <div>
                <label>Staff Number</label>
                <input type="text" name="staff_number" value="{{ old('staff_number') }}" required>
            </div>
            <div>
                <label>Grade</label>
                <input type="text" name="grade" value="{{ old('grade') }}" required>
            </div>
            <div>
                <label>Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" required>
            </div>
        @endif

        {{-- Industry Fields --}}
        @if($portal === 'industry')
            <div>
                <label>Student Name</label>
                <input type="text" name="student_name" value="{{ old('student_name') }}" required>
            </div>
            <div>
                <label>Role in Company</label>
                <input type="text" name="role" value="{{ old('role') }}" required>
            </div>
            <div>
                <label>Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" required>
            </div>
        @endif

        {{-- Admin Fields --}}
        @if($portal === 'admin')
            <div>
                <label>Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" required>
            </div>
        @endif

        {{-- Password --}}
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Register</button>
    </form>
</body>
</html>
