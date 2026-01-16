@extends('layouts.my_app')

@section('content')
<div class="max-w-6xl mx-auto mt-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-800 p-4 flex justify-between items-center">
            <h2 class="text-white font-bold text-xl uppercase italic">Assigned Student Reports</h2>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">{{ $reports->count() }} Total</span>
        </div>

        <div class="p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200 text-gray-600 text-sm uppercase">
                        <th class="py-3 px-4">Student Name</th>
                        <th class="py-3 px-4">Report Title</th>
                        <th class="py-3 px-4 text-center">Date Submitted</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($reports as $report)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-medium">
                                {{ $report->attachmentStudent->student->user->name }}
                            </td>
                            <td class="py-4 px-4">{{ $report->title }}</td>
                            <td class="py-4 px-4 text-center text-sm">
                                {{ $report->created_at->format('d M Y') }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex justify-center gap-2">
                                    @php $webPath = str_replace('\\', '/', $report->file_path); @endphp
                                    
                                    {{-- View Button --}}
                                    <a href="{{ asset('storage/' . $webPath) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 p-1 border border-blue-600 rounded" title="View PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Download Button --}}
                                    <a href="{{ asset('storage/' . $webPath) }}" download 
                                       class="text-green-600 hover:text-green-800 p-1 border border-green-600 rounded" title="Download">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 italic">No reports have been submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection