(function ($) {
    $('#searchWinner').click(function (e) {
        const loading = $('#loading');

        loading.addClass('is-active');

        const params = {
            action: ajaxobj.action_ajaxGetWinner,
            nonce: ajaxobj.nonce,
        };
        $.get(ajaxobj.ajax_url, params, function (res) {
            console.log(res);
            loading.removeClass('is-active');
        });
    });
}(jQuery))