<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - DeKUT External Attachment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-red-700 text-white min-h-screen p-4">
        <h1 class="text-xl font-bold mb-8">Admin Portal</h1>
        <nav class="space-y-2">
            <a href="{{ route('admin.portal') }}" class="block py-2 px-3 rounded hover:bg-red-600">📊 Dashboard</a>
            <a href="{{ route('admin.students') }}" class="block py-2 px-3 rounded hover:bg-red-600">🎓 Students</a>
            <a href="{{ route('admin.supervisors') }}" class="block py-2 px-3 rounded hover:bg-red-600">👨‍🏫 Supervisors</a>
            <a href="{{ route('admin.industry') }}" class="block py-2 px-3 rounded hover:bg-red-600">🏢 Industry</a>
            <a href="{{ route('admin.attachments') }}" class="block py-2 px-3 rounded hover:bg-red-600">📑 Attachments</a>
            <a href="{{ route('admin.budgets') }}" class="block py-2 px-3 rounded hover:bg-red-600">💰 Budgets</a>
            <a href="{{ route('admin.reports') }}" class="block py-2 px-3 rounded hover:bg-red-600">📄 Reports</a>
        </nav>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="w-full bg-red-800 py-2 rounded hover:bg-red-900">🚪 Logout</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>
