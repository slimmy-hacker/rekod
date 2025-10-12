
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tailwind test</title>

  <!-- CDN: temporary test to verify layout/look -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700">
  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Student Registration</h2>

    <div class="mb-4">
      <label class="block text-gray-600">Full Name</label>
      <input class="w-full border rounded px-3 py-2 mt-1" />
    </div>

    <div class="mb-4">
      <label class="block text-gray-600">Registration Number</label>
      <input class="w-full border rounded px-3 py-2 mt-1" placeholder="CIT/0001/20"/>
    </div>

    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg mt-4">Register</button>
  </div>
</body>
</html>
