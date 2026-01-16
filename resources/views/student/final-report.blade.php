@extends('layouts.my_app')

@section('title', 'Final Report Submission')

@section('content')
<div class="max-w-4xl mx-auto mt-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        {{-- Header --}}
        <div class="bg-gray-800 p-4">
            <h2 class="text-white font-bold text-xl uppercase tracking-wider">Final Attachment Report</h2>
        </div>

        <div class="p-6">
            {{-- State 1: Student has already submitted --}}
            @if(isset($final_report) && $final_report)
                <div class="text-center py-8">
                    <div class="mb-4 flex justify-center">
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-800">Report Successfully Submitted</h3>
                    <p class="text-gray-500 mt-2">You have already submitted your final report. No further action is required.</p>

                    <div class="mt-6 inline-block bg-gray-50 border rounded-lg p-4 text-left w-full max-w-md">
                        <div class="mb-4">
                            <span class="block text-xs font-semibold uppercase text-gray-400">Submitted Title</span>
                            <span class="text-sm font-bold text-gray-700">{{ $final_report->title }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-4">
                            <div>
                                <span class="block text-xs font-semibold uppercase text-gray-400">Submission Date</span>
                                <span class="text-sm font-medium text-gray-700">{{ $final_report->created_at->format('D, d M Y - H:i') }}</span>
                            </div>
                          <a href="{{ asset('storage/' . $final_report->file_path) }}" 
                               target="_blank" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold flex items-center transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View My Report
                            </a>
                        </div>
                    </div>
                </div>

            {{-- State 2: Student has NOT submitted yet --}}
            @else
                <div class="mb-6">
                    <p class="text-gray-600 bg-blue-50 border-l-4 border-blue-500 p-4 italic text-sm">
                        <strong>Note:</strong> You can only submit your final report **once**. Please ensure you are uploading the final, corrected version of your PDF.
                    </p>
                </div>

                <form action="{{ route('student.final-report.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Report Title</label>
                        <input type="text" name="title" required class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Industrial Attachment Report at XYZ Company">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Executive Summary / Content</label>
                        <textarea name="content" rows="4" required class="w-full border border-gray-300 p-2 rounded focus:ring-blue-500 focus:border-blue-500" placeholder="Briefly describe the contents of your report..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Upload Report (PDF format only)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition cursor-pointer">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <input id="file-upload" name="final_report_file" type="file" required accept=".pdf" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <p class="text-xs text-gray-500">Max file size: 10MB</p>
                            </div>
                        </div>
                        @error('final_report_file')
                            <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                onclick="return confirm('Are you sure? You cannot edit this report after submission.')"
                                class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded shadow hover:bg-blue-700 focus:outline-none transition duration-150">
                            Upload and Submit Final Report
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection