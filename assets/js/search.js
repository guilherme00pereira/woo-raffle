(function ($) {
    $('#btn-search-cpf-numbers').on('click', function (e) {
        const loading = $('#loading-search-cpf-numbers');
        loading.show();
        e.preventDefault();
        $.get('/wp-admin/admin-ajax.php', {
            action: 'getProductNumbersByCPF',
            cpf: $('#search-cpf-val').val()
        }, function (res) {
            console.log(res.data)
            $('#cpf-numbers-search-result').html(res.data.html);
            loading.hide();
        });

    });
}(jQuery))