
<div id="student_attachment_details-modal" tabindex="-1" aria-hidden="true"  data-modal-backdrop="static" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-4/5 max-h-full">
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm ">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                <h3 class="text-lg font-semibold text-gray-">
                   Student Attachment Details
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center close-student_attachment_details_modal-btn">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <table class="min-w-full border border-gray-300" id="student_attachment_detailsTable">
                <tbody>
                <!-- jQuery will fill here -->
                </tbody>
            </table>
            <button  type="button" class="text-white inline-flex items-center bg-gray-300 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center close-student_attachment_details_modal-btn ">
                <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                close
            </button>
        </div>
    </div>
</div>
@push('scripts')
   <script>
    $(document).ready(function() {
        const student_attachment_details_modal = new Modal($('#student_attachment_details-modal')[0], {
            backdrop: 'static',
            closable: false
        });

        $(document).on('click', '.close-student_attachment_details_modal-btn', function () {
            student_attachment_details_modal.hide();
        });

        $(document).on('click', '.open-student_attachment_details_modal-btn', function () {
            const student_attachment_details_id = $(this).data('id');
            openAttachmentModal(student_attachment_details_id)
        });

        function openAttachmentModal(id) {
            student_attachment_details_modal.show();
            
            // Clear & show loading message
            $("#student_attachment_detailsTable tbody").html(`
                <tr><td colspan="4" class="p-4 text-center">
                    <div class="flex justify-center items-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </td></tr>
            `);

            // FIXED: Use hardcoded URL path
            $.ajax({
                url: "/attachment-details/" + id,
                method: 'GET',
                success: function (data) {
                    console.log('Data received:', data); // Debug
                    fillAttachmentTable(data);
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    
                    $("#student_attachment_detailsTable tbody").html(`
                        <tr><td colspan="4" class="p-4 text-center text-red-600">
                            Error loading data (${xhr.status})
                            <br><small>${xhr.responseText}</small>
                        </td></tr>
                    `);
                }
            });
        }
function fillAttachmentTable(data) {
    console.log('Data received:', data); // Debug to see structure
    
    let html = '';

    // Student Info
    html += `<tr>
        <th class="p-3 bg-gray-100 w-1/4">Student Name</th>
        <td class="p-3 w-1/4">${data?.student?.user?.name || 'N/A'}</td>
        <th class="p-3 bg-gray-100 w-1/4">Student Phone No</th>
        <td class="p-3 w-1/4">${data?.student?.user?.phone_number || 'N/A'}</td>
    </tr>`;
    
    html += `<tr>
        <th class="p-3 bg-gray-100">Registration No</th>
        <td class="p-3">${data?.student?.reg_no || 'N/A'}</td>
        <th class="p-3 bg-gray-100">Email</th>
        <td class="p-3">${data?.student?.user?.email || 'N/A'}</td>
    </tr>`;
    
    html += `<tr>
        <th class="p-3 bg-gray-100">Course</th>
        <td class="p-3" colspan="3">${data?.student?.program?.name || 'N/A'}</td>
    </tr>`;

    // Company Info
    const townName = data?.company?.town?.name || data?.town?.name || 'Not Assigned';
    
    html += `<tr>
        <th class="p-3 bg-gray-100">Organization</th>
        <td class="p-3">${data?.company?.name || 'N/A'}</td>
        <th class="p-3 bg-gray-100">Street</th>
        <td class="p-3">${data?.company?.street || 'N/A'}</td>
    </tr>`;
    
    html += `<tr>
        <th class="p-3 bg-gray-100">Town</th>
        <td class="p-3">${townName}</td>
        <th class="p-3 bg-gray-100">Building</th>
        <td class="p-3">${data?.company?.building || 'N/A'}</td>
    </tr>`;

    // Attachment Info
    html += `<tr>
        <th class="p-3 bg-gray-100">Attachment Name</th>
        <td class="p-3" colspan="3">${data?.attachment?.name || 'N/A'}</td>
    </tr>`;
    
    html += `<tr>
        <th class="p-3 bg-gray-100">Start Date</th>
        <td class="p-3">${data?.start_date || 'N/A'}</td>
        <th class="p-3 bg-gray-100">End Date</th>
        <td class="p-3">${data?.end_date || 'N/A'}</td>
    </tr>`;

    // Get values from data (handle both snake_case and camelCase)
    const supervisor = data?.industrial_supervisor || data?.industrialSupervisor || {};
    const supervisorUser = supervisor?.user || {};
    
    const attachmentLecturer = data?.attachment_lecturer || data?.attachmentLecturer || {};
    const lecturer = attachmentLecturer?.lecturer || {};
    const lecturerUser = lecturer?.user || {};

    // Supervisor Info
    html += `<tr>
        <th class="p-3 bg-gray-100">Industrial Supervisor</th>
        <td class="p-3">${supervisorUser?.name || 'N/A'}</td>
        <th class="p-3 bg-gray-100">Supervisor Email</th>
        <td class="p-3">${supervisorUser?.email || 'N/A'}</td>
    </tr>`;
    html += `<tr>
        <th class="p-3 bg-gray-100">Supervisor Phone</th>
        <td class="p-3" colspan="3">${supervisorUser?.phone_number || 'N/A'}</td>
    </tr>`;

    // Lecturer Info - Phone is in users.phone_number
    const lecturerPhone = lecturerUser?.phone_number ||  // From users table
                         lecturer?.office_phone ||       // From lecturers table (fallback)
                         'N/A';
    const lecturerName = lecturerUser?.name || 'Not Assigned';
    const lecturerEmail = lecturerUser?.email || '';

    html += `<tr>
        <th class="p-3 bg-gray-100">Lecturer</th>
        <td class="p-3">${lecturerName}</td>
        <th class="p-3 bg-gray-100">Lecturer Email</th>
        <td class="p-3">${lecturerEmail}</td>
    </tr>`;
    html += `<tr>
        <th class="p-3 bg-gray-100">Lecturer Phone</th>
        <td class="p-3" colspan="3">${lecturerPhone}</td>
    </tr>`;

    $("#student_attachment_detailsTable tbody").html(html);
}
    });
</script>
@endpush
