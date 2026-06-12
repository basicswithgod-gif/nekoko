/* global nekokoData, jQuery */
jQuery(function ($) {

    // Safety popup: enable confirm button only when checkbox is checked
    $(document).on('change', '#nekoko-safety-check', function () {
        $('#nekoko-safety-confirm').prop('disabled', !this.checked);
    });

    $(document).on('click', '#nekoko-safety-confirm', function () {
        $.post(nekokoData.ajaxUrl, {
            action: 'nekoko_safety_ack',
            nonce:  nekokoData.nonce,
        }, function () {
            $('#nekoko-safety-overlay').fadeOut(300);
        });
    });

    // Booking form submission
    $(document).on('submit', '#nekoko-booking-form', function (e) {
        e.preventDefault();
        const $form   = $(this);
        const $result = $('#nekoko-booking-result');
        const $btn    = $form.find('button[type="submit"]');

        $btn.prop('disabled', true).text('Šaljem...');

        $.post(nekokoData.ajaxUrl, {
            action:     'nekoko_booking_request',
            nonce:      nekokoData.nonce,
            name:       $form.find('[name="name"]').val(),
            email:      $form.find('[name="email"]').val(),
            message:    $form.find('[name="message"]').val(),
            date:       $form.find('[name="date"]').val(),
            listing_id: $form.find('[name="listing_id"]').val(),
        }, function (res) {
            $btn.prop('disabled', false).text('Pošalji zahtev');
            if (res.success) {
                $result.html('<div class="nekoko-notice nekoko-notice--success">' + res.data.message + '</div>');
                $form[0].reset();
            } else {
                $result.html('<div class="nekoko-notice nekoko-notice--error">' + (res.data.message || 'Greška') + '</div>');
            }
        });
    });

    // Star rating form
    $(document).on('submit', '#nekoko-rating-form', function (e) {
        e.preventDefault();
        const $form   = $(this);
        const $result = $('#nekoko-rating-result');

        $.post(nekokoData.ajaxUrl, {
            action:      'nekoko_submit_rating',
            nonce:       nekokoData.nonce,
            provider_id: $form.find('[name="provider_id"]').val(),
            rating:      $form.find('[name="rating"]').val(),
            comment:     $form.find('[name="comment"]').val(),
        }, function (res) {
            if (res.success) {
                $result.html('<div class="nekoko-notice nekoko-notice--success">Hvala na oceni! Prosek: ' + res.data.avg + '/5</div>');
                $form.find('textarea').val('');
            } else {
                $result.html('<div class="nekoko-notice nekoko-notice--error">' + (res.data.message || 'Greška') + '</div>');
            }
        });
    });

});
