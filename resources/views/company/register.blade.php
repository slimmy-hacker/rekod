<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-800">Register Your Company</h2>
            <p class="text-gray-500 mt-1 text-sm">Create an account to manage attachment opportunities</p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.portal.store', ['portal' => 'company']) }}" class="space-y-4">
            @csrf

            {{-- Company Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Company Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Company Address</label>
                <input type="text" name="address" value="{{ old('address') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Telephone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Telephone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Contact Person --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Contact Person Name</label>
                <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Role in Company</label>
                <input type="text" name="role" value="{{ old('role') }}" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-lg shadow-md transition duration-200">
                Register Company
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-5">
            Already have an account?
            <a href="{{ route('login.portal', ['portal' => 'company']) }}" class="text-blue-600 hover:underline font-medium">
                Login here
            </a>
        </p>
    </div>

</body>
</html>
