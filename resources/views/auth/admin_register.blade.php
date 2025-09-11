@php
    $portal = $portal ?? 'admin';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">
            Register
        </h2>

        <form method="POST" action="{{ route('register.portal.store', ['portal' => $portal]) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-600">Full Name</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Email Address</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Password</label>
                <input type="password" name="password" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                Register
            </button>

            <p class="text-center text-sm text-gray-600 mt-4">
                Already have an account?
                <a href="{{ route('login.portal', ['portal' => $portal]) }}" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>
