<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Portal - DeKUT External Attachment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-700 text-white min-h-screen p-4">
        <h1 class="text-xl font-bold mb-8">Supervisor Portal</h1>
        <nav class="space-y-2">
            <a href="{{ route('supervisor.portal') }}" class="block py-2 px-3 rounded hover:bg-blue-600">📊 Dashboard</a>
            
        </nav>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="w-full bg-blue-800 py-2 rounded hover:bg-blue-900">🚪 Logout</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>
