/* Public JavaScript */
jQuery(document).ready(function($) {
    // Bulk selection for APL 02
    $(document).on('click', '#btn-apl02-all-k', function(e) {
        e.preventDefault();
        $('.apl02-k').prop('checked', true).trigger('change');
    });

    $(document).on('click', '#btn-apl02-all-bk', function(e) {
        e.preventDefault();
        $('.apl02-bk').prop('checked', true).trigger('change');
    });

    // Form validation
    function validateForm($form) {
        var isValid = true;
        var $firstError = null;
        var checkedRadioGroups = {};

        // Reset previous highlights
        $form.find('.lsp-elemen-options').css('outline', '');
        $form.find('[required]').css('border-color', '');

        $form.find('[required]').each(function() {
            var $field = $(this);

            if ($field.is(':radio')) {
                var name = $field.attr('name');
                if (checkedRadioGroups[name]) {
                    return; // already checked this radio group
                }
                checkedRadioGroups[name] = true;

                if ($form.find('input[name="' + name + '"]:checked').length === 0) {
                    isValid = false;
                    $field.closest('.lsp-elemen-options').css('outline', '2px solid #dc3232');
                    if (!$firstError) {
                        $firstError = $field;
                    }
                }
                return;
            }

            var value = $field.val();
            if (!value || value.trim() === '') {
                isValid = false;
                $field.css('border-color', '#dc3232');
                if (!$firstError) {
                    $firstError = $field;
                }
            } else {
                $field.css('border-color', '');
            }
        });

        // Validate email
        var $email = $form.find('input[type="email"]');
        if ($email.length && $email.val()) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test($email.val())) {
                isValid = false;
                $email.css('border-color', '#dc3232');
                if (!$firstError) {
                    $firstError = $email;
                }
            }
        }

        // Scroll to first error
        if ($firstError) {
            $('html, body').animate({
                scrollTop: $firstError.offset().top - 120
            }, 500);
        }

        return isValid;
    }

    // Clear error on input / change
    $(document).on('input change', '[required]', function() {
        var $this = $(this);
        if ($this.is(':radio')) {
            $this.closest('.lsp-elemen-options').css('outline', '');
        } else {
            $this.css('border-color', '');
        }
    });

    // Form submission
    $(document).on('submit', '#lsp-pendaftaran-form', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var $message = $('#lsp-form-message');

        // Validate
        if (!validateForm($form)) {
            $message.removeClass('success').addClass('error').html('Silakan lengkapi semua isian bertanda * dan asesmen mandiri.').show();
            return;
        }

        // Disable button
        $submitBtn.prop('disabled', true).text('Mengirim pendaftaran...');

        // Prepare data
        var formData = $form.serialize();

        // Submit
        $.ajax({
            url: lspCertPublic.ajax_url,
            type: 'POST',
            data: formData + '&action=lsp_cert_submit_pendaftaran&nonce=' + lspCertPublic.nonce,
            success: function(response) {
                $submitBtn.prop('disabled', false).text('Kirim Pendaftaran & Asesmen Mandiri');

                if (response.success) {
                    $message.removeClass('error').addClass('success').html(response.data.message || 'Pendaftaran berhasil dikirim!').show();
                    $('html, body').animate({ scrollTop: $message.offset().top - 120 }, 500);

                    // Reset form
                    setTimeout(function() {
                        $form[0].reset();
                    }, 4000);
                } else {
                    $message.removeClass('success').addClass('error').html(response.data.message || 'Terjadi kesalahan saat memproses pendaftaran.').show();
                    $('html, body').animate({ scrollTop: $message.offset().top - 120 }, 500);
                }
            },
            error: function(xhr, status, error) {
                $submitBtn.prop('disabled', false).text('Kirim Pendaftaran & Asesmen Mandiri');
                $message.removeClass('success').addClass('error').html('Terjadi kesalahan koneksi (' + error + '). Silakan coba lagi.').show();
                $('html, body').animate({ scrollTop: $message.offset().top - 120 }, 500);
            }
        });
    });
});
