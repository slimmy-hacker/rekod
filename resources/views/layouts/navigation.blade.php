

<div style="display: flex; min-height: 100vh; font-family: 'Inter', sans-serif;">

    <aside style="width: 250px; background-color: #012970; color: white; padding: 2rem 1rem;">
        <h2 style="font-weight: bold; font-size: 1.2rem; margin-bottom: 1.5rem;">DeKUT External Attachment Management System</h2>

        <nav style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="/" style="color: white; text-decoration: none;">Home</a>

            @php
                $portal = session('portal');
            @endphp

            @if ($portal === 'student')              
              <a href="{{ route('student.attachment.form') }}" style="color: white; text-decoration: none;">Attachment Details</a>
                <a href="{{ route('student.reports') }}" style="color: white; text-decoration: none;">Reports</a>
                <a href="{{ route('student.logbook') }}" style="color: white; text-decoration: none;">Logbook</a>
                <a href="{{ route('student.results') }}" style="color: white; text-decoration: none;">Assessment Results</a>
            @elseif ($portal === 'supervisor')
                <a href="/supervisor/students assigned" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">My students</a>
                <a href="/supervisor/students logbook" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded">Logbooks</a>
                <a href="/supervisor/weekly reports" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded">Reports</a>
                <a href="/supervisor/evaluate" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">Evaluate students</a>
           
            @elseif ($portal === 'industry')
                <a href="{{ route('industry.portal') }}" style="color: white; text-decoration: none;">Industry Dashboard</a>
                <a href="{{ route('industry.feedback') }}" style="color: white; text-decoration: none;">Feedback</a>
                <a href="{{ route('industry.opportunities') }}" style="color: white; text-decoration: none;">Opportunities</a>
                @elseif ($portal === 'industry')
                <a href="{{ route('users.index') }}" style="color: white; text-decoration: none;">Users Management</a>
                <a href="{{ route('student.attachment.form') }}" style="color: white; text-decoration: none;">Attachment Details</a>
                <a href="{{ route('supervisor.index') }}" style="color: white; text-decoration: none;">supervisors</a>
            @endif

            
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               style="color: #ffdddd; text-decoration: none; margin-top: 2rem;">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

  
    <div style="flex: 1; padding: 2rem;">
        @yield('header')

        <main>
            {{ $slot ?? '' }}
        </main>
    </div>
</div>
