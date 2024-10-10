<script>

    $('.login_user').on('submit', function(e) {
        e.preventDefault(); // منع الإرسال الافتراضي للنموذج

        // الحصول على بيانات النموذج
        var username = $('#username').val().trim();
        var password = $('#password').val().trim();

        // التحقق من صحة حقول النموذج
        if (username === '') {
            show_notification('error', 'اسم المستخدم غير صحيح');
            return; // إيقاف إرسال النموذج
        }

        if (password === '') {
            show_notification('error', 'كلمة المرور غير صحيحة');
            return; // إيقاف إرسال النموذج
        }

        $.ajax({
            url: "{{ route('login') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // رمز CSRF
                username: username,
                password: password
            },
            success: function(response) {
                if (response.status === 1) {
                    window.location.href = '/';
                    show_notification('success', 'تم تسجيل الدخول بنجاح');
                } else {
                    show_notification('error', 'خطأ في تسجيل الدخول');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                show_notification('error', 'خطأ في تسجيل الدخول');
            }
        });
    });

    $(document).ready(function() {
        // التحقق من وجود التنبيه
        var alert = $('#error-alert');
        if (alert.length) {
            // إخفاء التنبيه بعد 3 ثواني
            setTimeout(function() {
                alert.fadeOut('slow');
            }, 3000); // 3000 مللي ثانية = 3 ثواني
        }
    });

</script>
