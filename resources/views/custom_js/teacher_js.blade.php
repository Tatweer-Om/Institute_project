<script type="text/javascript">

    $(document).ready(function() {
        $('#add_teacher_modal').on('hidden.bs.modal', function() {
            $(".add_teacher")[0].reset();
            $('.teacher_id').val('');


        });
        $('#all_teacher').DataTable({
             "sAjaxSource": "{{ url('show_teacher') }}",
             "bFilter": true,
             'pagingType': 'numbers',
             "ordering": true,
         });

         $('#all_course_teacher').DataTable({
            destroy: true, // Reset the table on initialization
            "processing": true, // Show a processing indicator
            "serverSide": true, // Enable server-side processing
            "pagingType": 'numbers', // Pagination style
            "ordering": true, // Enable column ordering
            "bFilter": true, // Enable filtering
            "ajax": {
                "url": "{{ url('show_teacher_courses') }}", // Use Blade syntax for Laravel URL
                "type": "GET",
                "data": function(d) {
                    d.teacher_id = $('.teacher_id').val(); // Send the selected course_id in the request
                }
            }
        });

        $('.add_teacher').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_teacher')[0]);
            var title=$('.teacher_name').val();
            var number=$('.teacher_number').val();
            var id=$('.teacher_id').val();


            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_teacher_name_lang',[],session('locale')); ?>'); return false;
                }

                if(number=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_teacher_phone_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_teacher").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_teacher') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        if(data.status==1)
                        {
                            show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                            $('#add_teacher_modal').modal('hide');
                            $('#all_teacher').DataTable().ajax.reload();
                            return false;
                        }

                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                        $('#all_teacher').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_teacher_name_lang',[],session('locale')); ?>'); return false;

                }

                if(number=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_teacher_phone_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_teacher").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_teacher') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        if (data.status == 3) {
                        show_notification('error', '<?php echo trans('messages.teacher_number_or_contact_exist_lang', [], session('locale')); ?>');
                        return false;
                        }

                        else if(data.status==1)
                        {
                            $('#all_teacher').DataTable().ajax.reload();
                            show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                            $('#add_teacher_modal').modal('hide');
                            $(".add_teacher")[0].reset();

                            return false;
                        }
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                        $('#all_teacher').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });

            }

        });
    });


    let isDrawing = false;
let lastX = 0;
let lastY = 0;

const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');

// Set up canvas for drawing
canvas.addEventListener('mousedown', (e) => {
    isDrawing = true;
    [lastX, lastY] = [e.offsetX, e.offsetY];
});

canvas.addEventListener('mousemove', (e) => {
    if (!isDrawing) return;
    ctx.strokeStyle = '#000'; // Black color for signature
    ctx.lineWidth = 2; // Adjust line width as needed
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
    [lastX, lastY] = [e.offsetX, e.offsetY];
});

canvas.addEventListener('mouseup', () => {
    isDrawing = false;
    ctx.closePath();
});

canvas.addEventListener('mouseout', () => {
    isDrawing = false;
});

// Clear signature
document.getElementById('clear-signature').addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('signature').value = ''; // Clear the hidden input
});

// Function to load the signature image
function loadSignature(signatureData) {
    // Clear the canvas before drawing
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Create a new image
    const img = new Image();
    img.onload = function() {
        // Draw the image onto the canvas
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    };

    // Set the source of the image to the signature data
    img.src = signatureData; // This should be the path or base64 data of the signature
}

// Optionally, you can add a function to get the signature data in base64
function saveSignature() {
    const signatureDataUrl = canvas.toDataURL(); // Gets the signature data as base64
    document.getElementById('signature').value = signatureDataUrl; // Save to hidden input
}


    function edit(id){
        $('#global-loader').show();
        before_submit();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax ({
            dataType:'JSON',
            url : "{{ url('edit_teacher') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){
                    // Define a variable for the image path

                    $(".teacher_id").val(fetch.teacher_id);
                    $(".first_name").val(fetch.first_name);
                    $(".second_name").val(fetch.second_name);
                    $(".last_name").val(fetch.last_name);
                    $(".teacher_email").val(fetch.teacher_email);

                    $(".notes").val(fetch.notes);
                    $(".civil_number").val(fetch.civil_number);
                    $(".teacher_number").val(fetch.teacher_number);

                    if (fetch.signature) {
                loadSignature(fetch.signature); // Call the function to load the signature
            }

                    $(".modal-title").html('<?php echo trans('messages.update_lang',[],session('locale')); ?>');

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
                    url: "{{ url('delete_teacher') }}",
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
                        $('#all_teacher').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
            }
        });
    }




    function del_t(id) {
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
                    url: "{{ url('delete_teacher_course') }}",
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
                        $('#all_course_teacher').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
            }
        });
    }



 </script>
