<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Registration - DeKUT External Attachment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
            Industry Registration
        </h2>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register.portal.store', ['portal' => 'industrial_supervisor']) }}">

            @csrf
            <input type="hidden" name="portal" value="industrial_supervisor">

            <!-- Full Name -->
            <div class="mb-4">
                <label class="block text-gray-600">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required autofocus>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company Name -->
            <div class="mb-4">
                <label class="block text-gray-600">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}"
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('company_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label class="block text-gray-600">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role/Position -->
            <div class="mb-4">
                <label class="block text-gray-600">Role / Position</label>
                <input type="text" name="role" value="{{ old('role') }}" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Student Full Name -->
            <div class="mb-4">
                <label class="block text-gray-600">Student Full Name</label>
                <input type="text" name="student_name" value="{{ old('student_name') }}" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('student_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Telephone -->
            <div class="mb-4">
                <label class="block text-gray-600">Telephone</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" 
                    placeholder="+2547XXXXXXXX" class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-600">Password</label>
                <input type="password" name="password" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label class="block text-gray-600">Confirm Password</label>
                <input type="password" name="password_confirmation" 
                    class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                Register
            </button>

            <!-- Login Link -->
            <p class="text-center text-sm text-gray-600 mt-4">
                Already have an account?
                <a href="{{ route('login.portal', 'industry') }}" class="text-blue-600 hover:underline">
                    Login here
                </a>
            </p>
        </form>
    </div>

</body>
</html>
