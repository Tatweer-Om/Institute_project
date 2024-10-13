<script type="text/javascript">

    $(document).ready(function() {
        $('#add_winlos_modal').on('hidden.bs.modal', function() {
            $(".add_winlos")[0].reset();
            $('.winlos_id').val('');


        });
        $('#all_winlos').DataTable({
             "sAjaxSource": "{{ url('show_winlos') }}",
             "bFilter": true,
             'pagingType': 'numbers',
             "ordering": true,
         });

         $('#all_course_winlos').DataTable({
            destroy: true,
            "processing": true,
            "serverSide": true,
            "pagingType": 'numbers',
            "ordering": true,
            "bFilter": true,
            "ajax": {
                "url": "{{ url('show_winlos_courses') }}",
                "type": "GET",
                "data": function(d) {
                    d.winlos_id = $('.winlos_id').val();
                }
            }
        });

        $('.add_winlos').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_winlos')[0]);
            var title=$('.winlos_name').val();
            var number=$('.winlos_number').val();
            var id=$('.winlos_id').val();


            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_winlos_name_lang',[],session('locale')); ?>'); return false;
                }

                if(number=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_winlos_phone_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_winlos").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_winlos') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        if(data.status==1)
                        {
                            show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                            $('#add_winlos_modal').modal('hide');
                            $('#all_winlos').DataTable().ajax.reload();
                            return false;
                        }

                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                        $('#all_winlos').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_winlos_name_lang',[],session('locale')); ?>'); return false;

                }

                if(number=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_winlos_phone_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_winlos").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_winlos') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        if (data.status == 3) {
                        show_notification('error', '<?php echo trans('messages.winlos_number_or_contact_exist_lang', [], session('locale')); ?>');
                        return false;
                        }

                        else if(data.status==1)
                        {
                            $('#all_winlos').DataTable().ajax.reload();
                            show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                            $('#add_winlos_modal').modal('hide');
                            $(".add_winlos")[0].reset();

                            return false;
                        }
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                        $('#all_winlos').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });

            }

        });
    });


    function loadSignature(signatureData) {
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');

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

    function edit(id){
        $('#global-loader').show();
        before_submit();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax ({
            dataType:'JSON',
            url : "{{ url('edit_winlos') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){
                    // Define a variable for the image path

                    $(".winlos_id").val(fetch.winlos_id);
                    $(".total_trade").val(fetch.total_trade);
                    $(".win").val(fetch.win);
                    $(".loss").val(fetch.loss);
                    $(".percentage_win ").val(fetch.percentage_win );

                    $(".notes").val(fetch.notes);
                    $(".percentage_los").val(fetch.percentage_los);




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
                    url: "{{ url('delete_winlos') }}",
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
                        $('#all_winlos').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
            }
        });
    }






    document.addEventListener('DOMContentLoaded', function() {
        const totalTradeInput = document.getElementById('total_trade');
        const winInput = document.getElementById('win');
        const lossInput = document.getElementById('loss');
        const percentageWinInput = document.getElementById('percentage_win');
        const percentageLosInput = document.getElementById('percentage_los');
        const percentageWinContainer = document.getElementById('percentage_win_container');
        const percentageLosContainer = document.getElementById('percentage_los_container');

        function calculateWinPercentage() {
            const totalTrade = parseFloat(totalTradeInput.value) || 0;
            const win = parseFloat(winInput.value) || 0;

            if (totalTrade > 0 && win > 0) {
                const winPercentage = (win / totalTrade) * 100;
                percentageWinInput.value = winPercentage.toFixed(2) + '%';
                percentageWinContainer.style.display = 'block'; // Show the win percentage field
            } else {
                percentageWinContainer.style.display = 'none'; // Hide if no valid win amount
            }
        }

        function calculateLossPercentage() {
            const totalTrade = parseFloat(totalTradeInput.value) || 0;
            const loss = parseFloat(lossInput.value) || 0;

            if (totalTrade > 0 && loss > 0) {
                const lossPercentage = (loss / totalTrade) * 100;
                percentageLosInput.value = lossPercentage.toFixed(2) + '%';
                percentageLosContainer.style.display = 'block'; // Show the loss percentage field
            } else {
                percentageLosContainer.style.display = 'none'; // Hide if no valid loss amount
            }
        }

        // Add event listeners to trigger calculation and visibility changes
        totalTradeInput.addEventListener('input', function() {
            calculateWinPercentage();
            calculateLossPercentage();
        });

        winInput.addEventListener('input', calculateWinPercentage);
        lossInput.addEventListener('input', calculateLossPercentage);
    });
 </script>
