<!-- resources/views/student/logbook.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Logbook</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-center mb-6">Student Logbook</h2>

        <!-- Logbook Form -->
        <form action="{{ route('logbook.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Week</label>
                <input type="text" name="week" required class="w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Activities</label>
                <textarea name="activities" rows="4" required class="w-full p-2 border rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Challenges</label>
                <textarea name="challenges" rows="3" class="w-full p-2 border rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Skills Gained</label>
                <textarea name="skills" rows="3" class="w-full p-2 border rounded"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Save Entry
                </button>
            </div>
        </form>
    </div>
</body>
</html>
