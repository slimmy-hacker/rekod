<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal - DeKUT Attachment System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-700 text-white min-h-screen p-6">
        <h2 class="text-xl font-bold mb-6">🎓 Student Portal</h2>
        <nav class="space-y-4">
            <a href="{{ route('student.portal') }}" class="block hover:bg-blue-600 p-2 rounded">Dashboard</a>
            <a href="{{ route('student.attachment-form') }}" class="block hover:bg-blue-600 p-2 rounded">Attachment Details</a>
            <a href="{{ route('student.reports') }}" class="block hover:bg-blue-600 p-2 rounded">Reports</a>
            <a href="{{ route('student.logbook') }}" class="block hover:bg-blue-600 p-2 rounded">Logbook</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-6">
                @csrf
                <button class="w-full bg-red-600 hover:bg-red-700 text-white p-2 rounded">Logout</button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        @yield('content')
    </main>
</body>
</html>
