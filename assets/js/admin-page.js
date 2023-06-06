(function ($) {
    $('#selectProduct').change(function (e) {
        const loading = $('#loading');
        loading.addClass('is-active');

        const params = {
            action: ajaxobj.action_ajaxRaffleData,
            nonce: ajaxobj.nonce,
        };
        $.get(ajaxobj.ajax_url, params, function (res) {
            displayQuotaSection();
            $('#raffle-data .notice-warning').html(res.data);
            loading.removeClass('is-active');
        });
    });

    $('#searchRaffle').click(function (e) {
        
    });

    function displayQuotaSection() {
        $('#quota-search').css('display', '');
        $('#search-button').css('display', '');
    }
}(jQuery))