<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($type) }} Login / Register</title>
</head>
<body>
    <h2>{{ ucfirst($type) }} Portal</h2>

    @if ($errors->any())
        <div style="color:red;">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/portal/' . $type) }}">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Name (for new users):</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>
        <button type="submit">Login / Register</button>
    </form>
</body>
</html>
