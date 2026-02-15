
@extends('layouts.my_app')
@section('title')
    Attachment Form
@endsection
@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
        <h1 class="text-2xl font-bold text-center mb-4">DEDAN KIMATHI UNIVERSITY OF TECHNOLOGY </h1>
        <h2 class="text-xl font-semibold text-center mb-8 underline text-blue-700">EXTERNAL ATTACHMENT INFORMATION FORM</h2>

        <form action="{{route('student.attachment-form.store')}}" method="POST" class="space-y-6">
            @csrf
            <fieldset class="border border-gray-300 p-4 rounded">
                <legend class="font-semibold text-lg">Student Details</legend>
                <div class="grid grid-cols-1 gap-4 mt-4">
                    <input type="text" name="student_name"  value="{{$logged_user->name}}" placeholder="Full Name" class="p-2 border rounded w-full" disabled>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <input type="text" name="reg_no" value="{{$my_student_details->reg_no}}" placeholder="Registration Number" class="p-2 border rounded w-full bg-gray-50" readonly>
    
    <input type="text" name="course" value="{{$my_student_details->program->name}}" placeholder="Course" class="p-2 border rounded w-full bg-gray-50" readonly>
    
    <input type="tel" 
           name="student_phone" 
           placeholder="Phone Number" 
           value="{{ old('student_phone', $my_student_details->phone_number) }}" 
           class="p-2 border rounded w-full @error('student_phone') border-red-500 @enderror" 
           required>
    
    <input type="email" name="student_email" placeholder="Email" value="{{$logged_user->email}}" class="p-2 border rounded w-full bg-gray-50" readonly>
</div>
                </div>
               
            </fieldset>

        <fieldset class="border border-gray-300 p-6 rounded mb-6">
    <legend class="font-semibold text-lg">Company Details</legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <label for="name" class="block font-semibold">Company Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
        </div>

        <div>
            <label for="alias" class="block font-semibold">Company Alias <span class="text-red-600">*</span></label>
            <input type="text" name="alias" id="alias" value="{{ old('alias') }}" class="w-full border rounded p-2" required>
        </div>

        <div>
            <label for="contact" class="block font-semibold">Contact <span class="text-red-600">*</span></label>
            <input type="text" name="contact" id="contact" value="{{ old('contact') }}" class="w-full border rounded p-2" required>
        </div>

        <div>
            <label for="email" class="block font-semibold">Email <span class="text-red-600">*</span></label>
            <input type="text" name="email" id="email" value="{{ old('email') }}" class="w-full border rounded p-2" required>
        </div>

        <div class="md:col-span-2">
            <label for="address" class="block font-semibold">Address <span class="text-red-600">*</span></label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full border rounded p-2" required>
        </div>

        <div>
            <label for="county" class="block font-semibold">County <span class="text-red-600">*</span></label>
            <select name="county_id" id="county" class="w-full border rounded p-2 select2" required>
                <option value="">-- Select County --</option>
                @foreach($counties as $county)
                    <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>
                        {{ $county->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="town" class="block font-semibold">Town <span class="text-red-600">*</span></label>
            <select name="town_id" id="town" class="w-full border rounded p-2 select2" required>
                <option value="">-- Select Town --</option>
                @foreach($towns as $town)
                    <option value="{{ $town->id }}" {{ old('town_id') == $town->id ? 'selected' : '' }}>
                        {{ $town->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
                        <label for="street" class="block font-semibold">Street/Road <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <input type="text" name="street" id="street" value="{{ old('street') }}" placeholder="e.g. Kimathi Way" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label for="building" class="block font-semibold">Building <span class="text-gray-400 text-xs">(Optional)</span></label>
                        <input type="text" name="building" id="building" value="{{ old('building') }}" placeholder="e.g. Resource Centre" class="w-full border rounded p-2">
                    </div>
    </div>
</fieldset>

        <div>

            <label for="date_commenced" class="block text-sm font-medium mb-1 required">
                Date Commenced
            </label>

            <input
                required
                type="date"
                id="date_commenced"
                name="start_date"
                value="{{ old('start_date', $attachment_student->start_date) }}"
                class="p-2 border rounded w-full @error('start_date') border-red-500 @enderror"
            >

            @error('start_date')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_finished" class="block text-sm font-medium mb-1 required">
                Expected Finishing Date
            </label>

            <input
                required
                type="date"
                id="date_finished"
                name="end_date"
                value="{{ old('end_date', $attachment_student->end_date) }}"
                class="p-2 border rounded w-full @error('end_date') border-red-500 @enderror"
            >

            @error('end_date')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
        <fieldset class="border border-gray-300 p-6 rounded mb-6">
    <legend class="font-semibold text-lg">Supervisor Details</legend>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Supervisor Name -->
        <div>
            <label for="supervisor_name" class="block font-semibold mb-1">Supervisor Name <span class="text-red-600">*</span></label>
            <input type="text" name="supervisor_name" id="supervisor_name" placeholder="Supervisor Name"
                   value="{{ old('supervisor_name') }}"
                   class="w-full border rounded p-2" required>
            @error('supervisor_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Supervisor Email -->
        <div>
            <label for="supervisor_email" class="block font-semibold mb-1">Supervisor Email <span class="text-red-600">*</span></label>
            <input type="email" name="supervisor_email" id="supervisor_email" placeholder="Supervisor Email"
                   value="{{ old('supervisor_email') }}"
                   class="w-full border rounded p-2" required>
            @error('supervisor_email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Supervisor Phone -->
        <div>
            <label for="supervisor_phone" class="block font-semibold mb-1">Supervisor Phone <span class="text-red-600">*</span></label>
            <input type="tel" name="supervisor_phone" id="supervisor_phone" placeholder="Supervisor Phone"
                   value="{{ old('supervisor_phone') }}"
                   class="w-full border rounded p-2" required>
            @error('supervisor_phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Supervisor Position -->
        <div>
            <label for="supervisor_position" class="block font-semibold mb-1">Position <span class="text-red-600">*</span></label>
            <input type="text" name="supervisor_position" id="supervisor_position" placeholder="Supervisor Position"
                   value="{{ old('supervisor_position') }}"
                   class="w-full border rounded p-2" required>
            @error('supervisor_position') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</fieldset>


<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
    
        Submit
    </button>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            if ($('#organization').val()) {
                handleOrganizationChange();
            }

            function handleOrganizationChange() {
                let selected = $('#organization').find('option:selected');

                $('#town').val(selected.data('town') || '');
                $('#street').val(selected.data('street') || '');
                $('#building').val(selected.data('building') || '');

                loadCompanySupervisors(selected.val());
            }

            $("#organization").on('change', handleOrganizationChange);

            function loadCompanySupervisors(companyId) {
                const url = `/get-company-industrial-supervisors/${companyId}`;

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let $select = $('#industrial_supervisor');
                        $select.empty().append('<option value="">-- select Supervisor --</option>');

                        $.each(response, function (i, supervisor) {
                            $select.append(`<option value="${supervisor.id}" data-phone="${supervisor.user.phone_number}" data-email="${supervisor.user.email}">
                                                ${supervisor.user.name}
                                            </option>`);
                        });
                        let oldCompanyId  = @json(old('company_id', $attachment_student->company_id));
                        let oldSupervisor = @json(old('industrial_supervisor_id', $attachment_student->industrial_supervisor_id));

                        if (companyId === oldCompanyId && oldSupervisor) {
                            $select.val(oldSupervisor).trigger('change');
                        } else {
                            $select.val('').trigger('change');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error fetching supervisors:', xhr.responseText);
                    }
                });
            }


            $('#industrial_supervisor').on('change', function () {
                const selected = $(this).find(':selected');
                $('#supervisor_phone').val(selected.data('phone') || '');
                $('#supervisor_email').val(selected.data('email') || '');
            });
 $('#add_new_company').on('change', function() {
        if ($(this).is(':checked')) {
            $('#existing_company_section').hide();
            $('#new_company_section').show();
        } else {
            $('#existing_company_section').show();
            $('#new_company_section').hide();
        }
    });

    // fill Town/Street/Building when existing company is selected
    $('#organization').on('change', function() {
        let selected = $(this).find('option:selected');
        $('#town').val(selected.data('town') || '');
        $('#street').val(selected.data('street') || '');
        $('#building').val(selected.data('building') || '');
    });
    $('#add_new_supervisor').on('change', function() {
            if ($(this).is(':checked')) {
                $('#existing_supervisor_section').hide();
                $('#new_supervisor_section').show();
            } else {
                $('#existing_supervisor_section').show();
                $('#new_supervisor_section').hide();
            }
        });

        // Fill phone/email when selecting existing supervisor
        $('#industrial_supervisor').on('change', function() {
            let selected = $(this).find(':selected');
            $('#supervisor_phone').val(selected.data('phone') || '');
            $('#supervisor_email').val(selected.data('email') || '');
        });
    });
    </script>
@endsection
