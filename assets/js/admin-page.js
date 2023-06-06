(function ($) {

    $('#raffle-tabs a').click(function(event){
        event.preventDefault();
        $('#raffle-tabs a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tabs-content div').hide();
        $('#' + $(this).data('tab') ).show();
    });

    $('#searchRaffle').click(function (e) {
        const loading = $('#loading');
        loading.addClass('is-active');

        const params = {
            action: ajaxobj.action_ajaxGetRaffleData,
            nonce: ajaxobj.nonce,
            pid: $('#selectProduct').val(),
            cota: $('#quotaNumber').val()
        };
        $.get(ajaxobj.ajax_url, params, function (res) {

            if(res.data.raffled) {
                $('#raffle-data').html(res.data.customerData);
            } else {
                $('#raffle-data').html(
                    `<div class="notice notice-error">erro</div>`
                );
            }

            loading.removeClass('is-active');
        });
    });
}(jQuery))