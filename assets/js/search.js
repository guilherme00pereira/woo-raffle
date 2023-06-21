(function ($) {
    $('#btn-search-cpf-numbers').on('click', function (e) {
        e.preventDefault();
        $.get('/wp-admin/admin-ajax.php', {
            action: 'getProductNumbersByCPF',
            cpf: $('#search-cpf-val').val()
        }, function (res) {
            console.log(res.data.html)
            $('#cpf-numbers-search-result').html(res.data.html);
        });

    });
}(jQuery))