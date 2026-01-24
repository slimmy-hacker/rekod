@extends('layouts.my_app')

@section('content')
<div class="max-w-6xl mx-auto mt-8 px-4">
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6 flex justify-between items-center">
            <div>
                <h2 class="text-white font-extrabold text-2xl tracking-wide uppercase">
                    <i class="fas fa-file-alt mr-2"></i> Assigned Student Reports
                </h2>
                <p class="text-gray-400 text-sm mt-1">Review and download final project submissions</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 text-sm font-semibold px-4 py-2 rounded-lg">
                    {{ $reports->count() }} Submissions
                </span>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" id="reportSearch" placeholder="Search student name or report title..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-4 px-6 font-bold">Student Details</th>
                        <th class="py-4 px-6 font-bold">Report Information</th>
                        <th class="py-4 px-6 text-center font-bold">Submission Date</th>
                        <th class="py-4 px-6 text-center font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold">
                                        {{ strtoupper(substr($report->attachmentStudent->student->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $report->attachmentStudent->student->user->name }}
                                        </div>
                                       <div class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-100 inline-block mt-1">
                                       {{ $report->attachmentStudent->student->reg_no ?? 'No Reg No.' }}
                                          </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900 font-medium">{{ $report->title }}</div>
                                <div class="text-xs text-blue-600 flex items-center mt-1">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A1 1 0 0111.293 2.707l4 4a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                    PDF Document
                                </div>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $report->created_at->format('M d, Y') }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1 uppercase">{{ $report->created_at->format('h:i A') }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex justify-center items-center gap-3">
                                    @php $webPath = str_replace('\\', '/', $report->file_path); @endphp
                                    
                                    {{-- View Link --}}
                                    <a href="{{ asset('storage/' . $webPath) }}" target="_blank" 
                                       class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white rounded-md text-sm font-medium transition duration-150" title="View PDF">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        View
                                    </a>

                                    {{-- Download Link --}}
                                    <a href="{{ asset('storage/' . $webPath) }}" download 
                                       class="inline-flex items-center px-3 py-1.5 border border-green-600 text-green-600 hover:bg-green-600 hover:text-white rounded-md text-sm font-medium transition duration-150" title="Download File">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 p-4 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No student reports found.</p>
                                    <p class="text-gray-400 text-sm">Once students submit their final reports, they will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Simple client-side search filtering
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection