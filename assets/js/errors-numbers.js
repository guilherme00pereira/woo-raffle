(function ($){
    $(document).ready(function (){
        $('#loadingErrors').show();
        $('#loadingErrors span').html('Carregando problemas de <b>diferença de quantidade</b>...')
        $.get('/wp-admin/admin-ajax.php', {
            action: 'getAmountGeneratedErrors',
        }, function (res) {
            $('#woo_raffle_error_numbers_table tbody').append(res.data);
            $('#loadingErrors span').html('Carregando problemas de <b>pedidos sem números gerados</b>...')
            $.get('/wp-admin/admin-ajax.php', {
                action: 'getOrderWithNoNumbersError',
            }, function (res) {
                $('#woo_raffle_error_numbers_table tbody').append(res.data);
                $('#loadingErrors span').html('Carregando problemas de <b>vendido fora de estoque</b>...')
                $.get('/wp-admin/admin-ajax.php', {
                    action: 'getStockErrors',
                }, function (res) {
                    $('#loadingErrors').hide();
                    $('#woo_raffle_error_numbers_table tbody').append(res.data);
                });
            });

        });

    });
}(jQuery))