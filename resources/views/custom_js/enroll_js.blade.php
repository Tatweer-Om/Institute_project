<script>
    $(document).ready(function() {


        $('#all_enroll').DataTable({
            destroy: true, // Reset the table on initialization
            "processing": true, // Show a processing indicator
            "serverSide": true, // Enable server-side processing
            "pagingType": 'numbers', // Pagination style
            "ordering": true, // Enable column ordering
            "bFilter": true, // Enable filtering
            "ajax": {
                "url": "{{ url('show_enroll') }}", // Use Blade syntax for Laravel URL
                "type": "GET",
                "data": function(d) {
                    d.course_id = $('.course_id').val(); // Send the selected course_id in the request
                }
            }
        });






    $('.enroll_list').off().on('submit', function(e) {
    e.preventDefault();

    // Prepare form data
    var formdatas = new FormData($('.enroll_list')[0]);
    var title = $('.course_id').val();
    var number = $('.student_id').val();
    var id = $('.enroll_id').val(); // Get the enrollment ID

    // Check if the course or student is not selected
    if (title === "") {
        show_notification('error', '<?php echo trans('messages.add_course_name_lang', [], session('locale')); ?>');
        return false;
    }

    if (number === "") {
        show_notification('error', '<?php echo trans('messages.add_student_lang', [], session('locale')); ?>');
        return false;
    }

    // Show loader before submitting
    $('#global-loader').show();
    before_submit(); // Call any pre-submit actions if needed

    // Get CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Determine if this is an update or add operation
    var url = id ? "{{ url('update_enroll') }}" : "{{ url('add_enroll') }}"; // Update URL if ID is present
    var type = id ? "POST" : "POST"; // Use PUT for update

    $.ajax({
        type: type,
        url: url,
        data: formdatas,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
        },
        success: function(data) {
            // Hide loader and call post-submit actions
            $('#global-loader').hide();
            after_submit();
            if (data.status == 3) {
                show_notification('error', '<?php echo trans('messages.student_already_added_lang', [], session('locale')); ?>');
                return;
            }
            if (data.status == 4) {
                show_notification('error', '<?php echo trans('messages.15_students_allowed_lang', [], session('locale')); ?>');
                return;
            }
            if (data.status == 5) {
                $('#all_enroll').DataTable().ajax.reload();
                show_notification('success', '<?php echo trans('messages.data_updated_lang', [], session('locale')); ?>');
                return;
            }

            if (data.status == 1) {
                $('#all_enroll').DataTable().ajax.reload();
                show_notification('success', '<?php echo trans('messages.data_add_success_lang', [], session('locale')); ?>');

                // Reset the form after successful submission
                $(".enroll_list")[0].reset();
            } else {
                // Handle other statuses or errors in the response
                show_notification('error', data.message || '<?php echo trans('messages.data_add_failed_lang', [], session('locale')); ?>');
            }
        },
        error: function(data) {
            // Hide loader and call post-submit actions
            $('#global-loader').hide();
            after_submit();

            // Show error message and reload DataTable
            show_notification('error', '<?php echo trans('messages.data_add_failed_lang', [], session('locale')); ?>');
            $('#all_enroll').DataTable().ajax.reload();

            console.log(data); // Debugging the error response
        }
    });

    $('.student_id').select2({
        placeholder: '{{ trans('messages.choose_lang', [], session('locale')) }}', // Placeholder text
        allowClear: true // Allow clearing the selection
    });
    $('.course_id').select2({
        placeholder: '{{ trans('messages.choose_lang', [], session('locale')) }}', // Placeholder text
        allowClear: true // Allow clearing the selection
    });
});




        // On course selection change
 $('select[name="course_id"]').first().change(function() {
            var courseId = $(this).val();
            // get_id(courseId);
            $('#all_enroll').DataTable().ajax.reload();
            if (courseId) {
                $.ajax({
                    url: '/course_details/' + courseId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        $('#start_date').text(data.start_date);
                        $('#end_date').text(data.end_date);
                        $('#start_time').text(data.start_time);
                        $('#end_time').text(data.end_time);
                        $('#course_price').text(data.course_price);
                        $('#discounted_price').text(data.discounted_price);
                        $('#duration_months').text(data.duration_months);
                        $('#duration_hours').text(data.duration_hours);
                        $('#teacher').text(data.teacher);

                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            }
        });

        // On student selection change
        $('select[name="student_id"]').last().change(function() {
            var studentId = $(this).val();
            if (studentId) {
                $.ajax({
                    url: '/student_details/' + studentId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        $('#email').text(data.email);
                        $('#phone').text(data.phone);
                        $('#full_name').text(data.full_name);
                        $('#civil_number').text(data.civil_number);
                        $('#phone').text(data.phone);
                        $('#dob').text(data.dob);
                        // Update other student data as needed
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            }
        });
        $.ajax({
            url: '/current_offers', // Your API endpoint
            method: 'GET',
            success: function(data) {
                // Clear the existing items in the list
                $('.list-group').empty();

                // Loop through the offers and append them to the list
                data.forEach(function(offer) {
                    const courses = offer.courses.join(', ');
                    $('.list-group').append(`
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0 me-3">
                                <img src="{{ asset('images/favicon.ico') }}" alt="" class="img-thumbnail rounded-circle"> <!-- Adjust based on your image field -->
                            </div>
                            <div class="flex-grow-1">
                                <div>
                                    <h5 class="font-size-14 mb-1">${offer.offer_name}</h5> <!-- Assuming the offer has a 'name' field -->
                                     <p class="font-size-13 text-muted mb-0">{{ trans('messages.courses', [], session('locale')) }}: ${courses}</p>
                                    <p class="font-size-13 text-muted mb-0">{{ trans('messages.discount', [], session('locale')) }}: ${offer.offer_discount}%</p> <!-- Assuming the offer has a 'discount' field -->
                                </div>
                            </div>
                        </div>
                    </a>
                `);
                });
            },
            error: function(xhr) {
                console.error('Error fetching offers:', xhr);
            }
        });



        $('.add_student').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_student')[0]);


            var id=$('.student_id').val();


                $('#global-loader').show();
                before_submit();
                var str = $(".add_student").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_student2') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        if (data.status == 3) {
                        show_notification('error', '<?php echo trans('messages.student_number_or_contact_exist_lang', [], session('locale')); ?>');
                        return false;
                        }

                        else if(data.status==1)
                        {
                            $('.student_id').html(data.select_option).trigger('change');
                            show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                            $('#add_student_modal').modal('hide');
                            $(".add_student")[0].reset();

                            return false;
                        }
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');

                        console.log(data);
                        return false;
                    }
                });



        });



    //     function get_id(courseId) {
    //     initializeDataTable(courseId);
    // }


    });


    function edit(id){
        $('#global-loader').show();
        before_submit();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax ({
            dataType:'JSON',
            url : "{{ url('edit_enroll') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){
                    // Define a variable for the image path
                    $(".enroll_id").val(fetch.enroll_id);
                    $(".student_id").val(fetch.student_id);
                    $(".course_id").val(fetch.course_id);
                    $(".discount").val(fetch.new_discount);

                }
            },
            error: function(html)
            {
                $('#global-loader').hide();
                after_submit();
                show_notification('error','<?php echo trans('messages.edit_failed_lang',[],session('locale')); ?>');
                console.log(html);
                return false;
            }
        });
    }

    function del(id) {
        Swal.fire({
            title:  '<?php echo trans('messages.sure_lang',[],session('locale')); ?>',
            text:  '<?php echo trans('messages.delete_lang',[],session('locale')); ?>',
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: '<?php echo trans('messages.delete_it_lang',[],session('locale')); ?>',
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: !1
        }).then(function (result) {
            if (result.value) {
                $('#global-loader').show();
                before_submit();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ url('delete_enroll') }}",
                    type: 'POST',
                    data: {id: id,_token: csrfToken},
                    error: function () {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error', '<?php echo trans('messages.delete_failed_lang',[],session('locale')); ?>');
                    },
                    success: function (data) {
                        $('#global-loader').hide();
                        after_submit();
                        $('#all_enroll').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
            }
        });
    }





</script>
