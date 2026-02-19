@extends('layouts.my_app')

@section('content')
{{-- Near full-screen container --}}
<div class="max-w-[98%] mx-auto mt-8 px-2 pb-10">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        
        {{-- Header Section --}}
        <div class="bg-emerald-900 p-8 flex justify-between items-center">
            <div>
                <h2 class="text-white font-extrabold text-2xl uppercase tracking-widest">
                    <i class="fas fa-users mr-3"></i> Students with Logbooks
                </h2>
                <p class="text-emerald-200 text-sm mt-1">
                    {{ $students->count() }} students have submitted logbook entries
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white text-sm font-medium">Search:</span>
                <input type="text" id="studentSearch" placeholder="Search by name, reg no, or company..." 
                       class="rounded-xl px-6 py-3 text-base w-80 shadow-inner outline-none focus:ring-4 focus:ring-emerald-500/50 transition-all">
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto border-collapse" id="studentsTable">
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">#</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Student</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Registration No</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Department</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Company</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider text-center">Entries</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider text-center">Latest Entry</th>
                        <th class="p-6 text-center text-sm font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-emerald-50/50 transition-colors duration-150">
                            <td class="p-6">{{ $index + 1 }}</td>
                            
                            <td class="p-6">
                                <div class="text-lg font-bold text-gray-900">
                                    <i class="fas fa-user-graduate text-emerald-600 mr-2"></i>
                                    {{ $student['name'] }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-envelope mr-1"></i> {{ $student['email'] }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-phone mr-1"></i> {{ $student['phone'] }}
                                </div>
                            </td>
                            
                            <td class="p-6">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-mono text-sm rounded-md border border-emerald-200 font-bold uppercase">
                                    <i class="fas fa-id-card mr-1"></i> {{ $student['reg_no'] }}
                                </span>
                            </td>
                            
                            <td class="p-6">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-md">
                                    {{ $student['department'] }}
                                </span>
                            </td>
                            
                            <td class="p-6">
                                @foreach($student['companies'] as $company)
                                    <div class="mb-1">
                                        <span class="font-medium">{{ $company['name'] }}</span>
                                        <span class="text-sm text-gray-500">({{ $company['town'] }})</span>
                                    </div>
                                @endforeach
                            </td>
                            
                            <td class="p-6 text-center">
                                <span class="bg-emerald-600 text-white px-4 py-2 rounded-full text-lg font-bold">
                                    {{ $student['total_entries'] }}
                                </span>
                            </td>
                            
                            <td class="p-6 text-center">
                                @if($student['latest_entry'])
                                    <span class="bg-gray-100 px-3 py-1 rounded-lg text-sm">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($student['latest_entry'])->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            
                            <td class="p-6 text-center">
                                
                                <a href="{{ route('logbook', [$student['attachment_id']]) }}"
   class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all inline-flex items-center">
    <i class="fas fa-book-open mr-2"></i> VIEW LOGBOOK
</a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-6xl mb-4 text-gray-200"></i>
                                    <span class="text-xl font-medium">No students with logbooks found.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    /**
     * Search Logic: Filters the table rows in real-time
     */
    document.getElementById('studentSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#studentsTable tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection