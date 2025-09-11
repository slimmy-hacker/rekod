<aside class="w-64 bg-[#012970] text-white min-h-screen">
    <div class="p-4 text-center font-bold text-xl border-b border-blue-800">
        Admin Panel
    </div>
    <nav class="mt-6">
        <ul class="space-y-2">


            <!-- Students -->
            <li>
                <a href="{{ route('students') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    🎓 Students
                </a>
            </li>

            <!-- Supervisors -->
            <li>
                <a href="{{ route('supervisors') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    👨‍🏫 Supervisors
                </a>
            </li>

            <!-- Industry -->
            <li>
                <a href="{{ route('admin.industry') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    🏢 Industry
                </a>
            </li>

            <!-- Attachments -->
            <li>
                <a href="{{ route('attachments') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    📑 Attachments
                </a>
            </li>

            <!-- Budgets -->
            <li>
                <a href="{{ route('budgets') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    💰 Budgets
                </a>
            </li>

            <!-- Reports -->
            <li>
                <a href="{{ route('reports') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    📄 Reports
                </a>
            </li>

            <!-- Settings -->
            <li>
                <a href="{{ route('settings') }}" class="block py-2.5 px-4 hover:bg-blue-900 rounded">
                    ⚙️ Settings
                </a>
            </li>

            <!-- Logout -->
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left py-2.5 px-4 hover:bg-red-600 rounded">
                        🚪 Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>
