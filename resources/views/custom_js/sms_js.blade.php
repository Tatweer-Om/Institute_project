<script type="text/javascript">

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('.sms_status').on('change', function () {
        const smsStatus = $(this).val();
        $('#global-loader').show();
        $.ajax({
            url:  "{{ url('get_sms_status') }}",
            method: "POST",
            data: { sms_status: smsStatus,_token: csrfToken },
            success: function (data) {
                $('#global-loader').hide();
                if (data.status === 1) {
                    $(".sms_area").val(data.sms);
                } else {
                    $(".sms_area").val('');
                }
            },
            error: function (data) {
                $('#global-loader').hide();
                show_notification('error',  '<?php echo trans('messages.get_data_failed_lang',[],session('locale')); ?>');
                console.log(data);
            }
        });
    });

    $(".customer_name").click(function () {
        $(".sms_area").val((index, value) => value + '{student_name}');
    });

    $(".student_number").click(function () {
        $(".sms_area").val((index, value) => value + '{student_number}');
    });

    $(".invoice_link").click(function () {
        $(".sms_area").val((index, value) => value + '{invoice_link}');
    });


    $(".teacher_name").click(function () {
        $(".sms_area").val((index, value) => value + '{teacher_name}');
    });

    $(".product_name").click(function () {
        $(".sms_area").val((index, value) => value + '{product_name}');
    });

    $(".teacher_number").click(function () {
        $(".sms_area").val((index, value) => value + '{teacher_number}');
    });

    $(".course_name").click(function () {
        $(".sms_area").val((index, value) => value + '{course_name}');
    });

    $(".status").click(function () {
        $(".sms_area").val((index, value) => value + '{status}');
    });

    $(".serial_no").click(function () {
        $(".sms_area").val((index, value) => value + '{serial_no}');
    });

    $(".receipt_date").click(function () {
        $(".sms_area").val((index, value) => value + '{receipt_date}');
    });
    $(".notes").click(function () {
        $(".sms_area").val((index, value) => value + '{notes}');
    });
    $(".offer_name").click(function () {
        $(".sms_area").val((index, value) => value + '{offer_name}');
    });
    $(".start_date").click(function () {
        $(".sms_area").val((index, value) => value + '{start_date}');
    });
    $(".end_date").click(function () {
        $(".sms_area").val((index, value) => value + '{end_date}');
    });



    $(document).ready(function(){
        // Set timeout to hide the alert after 5 seconds
        setTimeout(function() {
            $("#success-alert").fadeOut("slow", function(){
                $(this).remove(); // Optional: Remove the alert from the DOM after fading out
            });
        }, 3000); // 5000 milliseconds = 5 seconds
    });

</script>
