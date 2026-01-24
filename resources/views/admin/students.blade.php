@extends('layouts.my_app')

@section('title', 'Students List')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5">
    <div class="mb-1 w-full">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href=# class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-gray-700 ml-1 md:ml-2 text-sm font-medium">Students</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="flex justify-between items-center">
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">All Registered Students</h1>
                <a href="{{ route('register') }}" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                    Add New Student
                </a>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="align-middle inline-block min-w-full">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white p-4">
                <table class="table-fixed min-w-full divide-y divide-gray-200" id="students_table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Reg No</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                            <th class="p-4 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $("#students_table").DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.student.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' }, 
            { data: 'email', name: 'email' },
            { data: 'reg_no', name: 'reg_no' },
            { data: 'department', name: 'department' },
            { data: 'program', name: 'program' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endsection