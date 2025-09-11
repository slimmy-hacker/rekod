<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - {{ ucfirst(Auth::user()->role) }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 220px; background: #2c3e50; color: white; padding: 20px; min-height: 100vh; }
        .sidebar h2 { font-size: 18px; margin-bottom: 20px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 10px 0; }
        .sidebar ul li a { color: white; text-decoration: none; }
        .content { flex-grow: 1; padding: 30px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>{{ ucfirst(Auth::user()->role) }} Menu</h2>
        <ul>
            @php $role = Auth::user()->role; @endphp

            @if($role === 'student')
                <li><a href="/student/profile">My Placement Info</a></li>
                <li><a href="/student/reports">Weekly Reports</a></li>
                <li><a href="/student/logbook">Logbook</a></li>
                <li><a href="/student/assessment">Assessment</a></li>
                <li><a href="/student/companies">Past Companies</a></li>

            @elseif($role === 'university_supervisor')
                <li><a href="/university/students">Assigned Students</a></li>
                <li><a href="/university/assessments">Conduct Assessments</a></li>

            @elseif($role === 'industry_supervisor')
                <li><a href="/industry/students">My Students</a></li>
                <li><a href="/industry/feedback">Provide Feedback</a></li>
                <li><a href="/industry/evaluations">Final Evaluations</a></li>

            @elseif($role === 'company')
                <li><a href="/company/opportunities">Post Opportunities</a></li>
                <li><a href="/company/history">Previous Attachments</a></li>
            @endif

            <li>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   Logout
                </a>
            </li>
        </ul>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="content">
        <h1>Welcome, {{ Auth::user()->name }}</h1>
        <p>This is your dashboard. Use the menu to navigate your features.</p>
    </div>
</body>
</html>
