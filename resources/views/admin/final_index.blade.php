@extends('layouts.my_app')

@section('content')
{{-- Increased container width to 98% for maximum visibility --}}
<div class="max-w-[98%] mx-auto mt-8 px-2 pb-12">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200">
        
        {{-- Header Section: Larger Padding and Title --}}
        <div class="bg-indigo-900 p-8 flex justify-between items-center">
            <div>
                <h2 class="text-white font-extrabold text-2xl uppercase tracking-widest">
                    <i class="fas fa-file-alt mr-3"></i> Master Final Reports Archive
                </h2>
                <p class="text-indigo-200 text-sm mt-1 uppercase tracking-tighter">Database of all submitted industrial attachment final reports</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <input type="text" id="adminSearch" placeholder="Search Name, Reg No, or Title..." 
                       class="rounded-xl px-6 py-3 text-base w-96 shadow-inner focus:ring-4 focus:ring-indigo-500/50 outline-none transition-all border-none">
            </div>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto" id="adminTable">
                {{-- Increased Header Size --}}
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Student & Reg No</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Report Title</th>
                        <th class="p-6 text-sm font-bold uppercase text-gray-600 tracking-wider">Submission Date</th>
                        <th class="p-6 text-center text-sm font-bold uppercase text-gray-600 tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                    <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                        {{-- Student Info: Larger Text --}}
                        <td class="p-6">
                            <div class="text-lg font-bold text-gray-900 leading-tight">
                                {{ $report->attachmentStudent->student->user->name ?? 'Unknown Student' }}
                            </div>
                            <div class="mt-1">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 font-mono text-sm rounded-md border border-indigo-200 font-bold uppercase">
                                    {{ $report->attachmentStudent->student->reg_no ?? 'N/A' }}
                                </span>
                            </div>
                        </td>

                        {{-- Title: Better Spacing --}}
                        <td class="p-6">
                            <div class="text-base text-gray-700 font-medium italic">
                                "{{ $report->title }}"
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="p-6 text-sm font-semibold text-gray-500">
                            <i class="far fa-calendar-check mr-2"></i>
                            {{ $report->created_at->format('M d, Y') }}
                        </td>

                        {{-- Action Button --}}
                        <td class="p-6 text-center">
                            <a href="{{ asset('storage/'.$report->file_path) }}" 
                               class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-800 text-white rounded-lg text-sm font-bold shadow transition-all transform hover:scale-105" 
                               target="_blank">
                                <i class="fas fa-download mr-2"></i> OPEN REPORT
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-6xl mb-4"></i>
                                <span class="text-xl font-medium">No Final Reports have been uploaded yet.</span>
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
    document.getElementById('adminSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#adminTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection