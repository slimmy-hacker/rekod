@extends('layouts.my_app')

@section('content')
{{-- Near full-screen container --}}
<div class="max-w-[98%] mx-auto mt-8 px-2 pb-10">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        
        {{-- Header Section --}}
        <div class="bg-emerald-900 p-8 flex justify-between items-center">
            <div>
                <h2 class="text-white font-extrabold text-2xl uppercase tracking-widest">
                    <i class="fas fa-book-open mr-3"></i> Master Daily Logbook Archive
                </h2>
                <p class="text-emerald-200 text-sm mt-1">Viewing all recorded daily tasks across all departments</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white text-sm font-medium">Search:</span>
                <input type="text" id="logSearch" placeholder="Search by name, task, or reg no..." 
                       class="rounded-xl px-6 py-3 text-base w-80 shadow-inner outline-none focus:ring-4 focus:ring-emerald-500/50 transition-all">
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto border-collapse" id="logTable">
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Student & ID</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Task Title</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Activities & Tasks</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Skills Learned</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Date Period</th>
                        <th class="p-6 text-center text-sm font-bold uppercase text-gray-600 tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logbooks as $log)
                        @php
                            $weekly = $log->weeklyReport;
                            $attachment = $weekly ? $weekly->attachmentStudent : null;
                            $student = $attachment ? $attachment->student : null;
                            $user = $student ? $student->user : null;
                        @endphp
                        <tr class="hover:bg-emerald-50/50 transition-colors duration-150">
                            <td class="p-6">
                                <div class="text-lg font-bold text-gray-900 leading-tight">
                                    {{ $user->name ?? 'Unknown Student' }}
                                </div>
                                <div class="mt-1">
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 font-mono text-sm rounded-md border border-emerald-200 font-bold uppercase">
                                        {{ $student->reg_no ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>

                            <td class="p-6 font-semibold text-gray-800 text-base">
                                {{ $log->task_title }}
                            </td>

                            <td class="p-6 text-sm text-gray-600 max-w-xs">
                                <div class="line-clamp-2">{{ $log->tasks }}</div>
                            </td>

                            <td class="p-6 text-sm text-gray-600">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-100">
                                    {{ Str::limit($log->skills_learned, 35) }}
                                </span>
                            </td>

                            <td class="p-6 text-sm font-medium text-gray-500 whitespace-nowrap">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ \Carbon\Carbon::parse($log->start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($log->end_date)->format('M d, Y') }}
                            </td>

                            <td class="p-6 text-center">
                                {{-- PASSING THE ENTIRE OBJECT AS JSON TO PREVENT QUOTE ERRORS --}}
                                <button onclick='viewLogModal(@json($log))' 
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all">
                                    FULL VIEW
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-6xl mb-4 text-gray-200"></i>
                                    <span class="text-xl font-medium">No logbook entries found.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL STRUCTURE --}}
<div id="logModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transition-all transform">
            {{-- Modal Header --}}
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold uppercase tracking-wider" id="modalTitle">Task Details</h3>
                <button onclick="closeModal()" class="text-white hover:text-emerald-200 text-3xl leading-none">&times;</button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-8 space-y-6">
                <div>
                    <h4 class="text-emerald-800 font-black uppercase text-xs tracking-widest mb-2">Activities & Tasks Performed</h4>
                    <p id="modalTasks" class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line"></p>
                </div>
                
                <div>
                    <h4 class="text-blue-800 font-black uppercase text-xs tracking-widest mb-2">Skills Learned</h4>
                    <p id="modalSkills" class="text-gray-700 leading-relaxed bg-blue-50/30 p-4 rounded-xl border border-blue-100 whitespace-pre-line"></p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="bg-gray-50 p-4 flex justify-end">
                <button onclick="closeModal()" class="bg-gray-800 text-white px-8 py-2 rounded-lg font-bold hover:bg-black transition-all">
                    CLOSE
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    /**
     * Search Logic: Filters the table rows in real-time
     */
    document.getElementById('logSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#logTable tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    /**
     * Modal Controller: Fixes the 'undefined' error by mapping object keys
     */
    function viewLogModal(log) {
        // Map the object properties to the HTML elements
        // We use || 'N/A' as a fallback to prevent "undefined" showing on screen
        document.getElementById('modalTitle').innerText = log.task_title || 'Log Detail';
        document.getElementById('modalTasks').innerText = log.tasks || 'No activities recorded for this entry.';
        document.getElementById('modalSkills').innerText = log.skills_learned || 'No skills specified.';
        
        // Show the modal by removing the 'hidden' class
        const modal = document.getElementById('logModal');
        modal.classList.remove('hidden');
        
        // Disable body scroll while modal is open
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('logModal');
        modal.classList.add('hidden');
        
        // Re-enable body scroll
        document.body.style.overflow = 'auto';
    }

    // Close Modal on Escape Key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
</script>
@endsection