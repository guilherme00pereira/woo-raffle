jQuery(document).ready(function ($) {
    const $is_raffle = $('input#_woo_raffle');
    const $is_show = $('.show_if_woo_raffle');
    $is_show.hide();

    $is_raffle.on('change', function () {
        const $is_virtual = $('input#_virtual');
        const $manage_stock = $('input#_manage_stock');

        if ($('input#_woo_raffle:checked').length > 0) {
            $is_show.show();
            $is_virtual.prop('checked', true);
            $manage_stock.prop('checked', true);
        } else {
            $is_show.hide();
            $is_virtual.prop('checked', false);
            $manage_stock.prop('checked', false);
        }

        $is_virtual.trigger('change');
        $manage_stock.trigger('change');
    })

    $is_raffle.trigger('change');

    $('#woo_raffles_export_numbers').on('click', function (e) {
        e.preventDefault();

        const product_id = $('input#post_ID').val();

        window.open(`/wp-admin/admin.php?page=woo-raffles-export&post=${product_id}`, '_blank');

        return false;
    });

    $('#woo_raffles_raffle_number').on('click', function (e) {
        e.preventDefault();

        $this = $(this);
        $this.attr('disabled', 'disabled');

        const product_id = $('input#post_ID').val();
        const $msg = $('#woo_raffles_drawn_number p');

        removeClassNotices($msg);

        if (parseInt(product_id) > 0) {
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                dataType: 'json',
                data: {
                    action: 'woo_drawn_number',
                    product_id: product_id,
                },
                success: function (response) {
                    $msg
                        .removeClass('hidden')
                        .addClass('notice-success')
                        .html(response?.msg);
                    $this.removeAttr('disabled');
                },
                error: function (err) {
                    console.error(err);
                    $this.removeAttr('disabled');
                }
            });
        }

        return false;
    });

    $('.repeater').repeater({
        initEmpty: true,
        defaultValues: {
            'text-input': 'foo'
        },
        show: function () {
            $(this).slideDown();
        },
        hide: function (deleteElement) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(deleteElement);
            }
        },
        ready: function (setIndexes) {
            $dragAndDrop.on('drop', setIndexes);
        },
        isFirstItemUndeletable: true
    });

    function removeClassNotices($msg) {
        $msg
            .addClass('hidden')
            .removeClass('notice-error')
            .removeClass('notice-success')
            .removeClass('notice-warning');
    }

    $('#turnQuotesOpen').on('click', function (e) {
        $.post('/wp-admin/admin-ajax.php', {
            action: 'turnQuotesOpen',
            product_id: $('input#post_ID').val()
        }, function (response) {
            if (response.success) {
                $('.quotes-open-status').show();
                $('#quotesOpenStatus').html('<span class="quotes-open-message">Este sorteio é por cotas abertas</span>');
            }
        }, 'json');
    });

    /* $('#shortcodeQuotesOpen').on('click', function (e) {
       const shortcode = document.getElementById('cotas-abertas-shortcode');
        shortcode.select();
        navigator.clipboard.writeText(shortcode.value);
        alert('Shortcode copiado para a área de transferência');
    }); */

    $('.add-open-numbers-order-item').click(function (e) {
        $('message-add-open-numbers').hide();
        $('#loading-add-open-numbers').show();
        const input = $('#add-open-numbers-to-order-item');
        const numbers = input.val();
        const item_id = input.data('item');
        $.post('/wp-admin/admin-ajax.php', {
            action: 'ajaxSaveOrderItemOpenNumbers',
            numbers: numbers,
            item_id: item_id
        }, function (response) {
            $('#loading-add-open-numbers').hide();
            $('message-add-open-numbers').show();
            if(response.success){
                window.location.reload();
            }else{
                $('#message-add-open-numbers').html(response.data);
            }
        }, 'json');
    });

});