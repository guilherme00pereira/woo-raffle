jQuery(document).ready(function ($) {
    $('.input-cpf').mask('000.000.000-00', {reverse: true});

    const $inputNumbers = $('#woo_raffles_numbers');

    //if ($('#woo-raffles-open-quotes').length) {
        // $('#woo-raffles-open-quotes').pagination({
        //     dataSource: generateObjData($inputNumbers.attr('data-qty')),
        //     pageSize: 100,
        //     showPrevious: false,
        //     showNext: false,
        //     callback: function (data, pagination) {
        //         const dataContainer = $('#woo-raffles-open-quotes .data-container');
        //
        //         let html = `
        //         <div class="bootstrap">
        //             <div class="row mb-20">`;
        //
        //         $.each(data, function (index, item) {
        //             html += `
        //                 <div class="col-lg-1 col-md-2 col-3">
        //                     <div class="content">
        //                         <div class="orders-raffles-numbers ${$.inArray(item.a.toString(), numbersSelected) !== -1 ? 'selected' : ''} ${$.inArray(item.a, numbersDisabled) !== -1 ? 'disabled' : ''}">
        //                             ${
        //                 $.inArray(item.a, numbersDisabled) !== -1
        //                     ? item.a.toString().padStart(str_pad_left, '0')
        //                     : `
        //                                         <a href="#" data-value="${item.a}">
        //                                             ${item.a.toString().padStart(str_pad_left, '0')}
        //                                         </a>
        //                                        `
        //             }
        //
        //                         </div>
        //                     </div>
        //                 </div>
        //                 `;
        //         });
        //
        //         html += `
        //             </div>
        //         </div>
        //         `;
        //
        //         dataContainer.html(html);
        //     }
        // });
    //}

    if ($('#woo-raffles-quotes-selected').length) {
        generateNumbersSelected();
    }

    $('body').on('click', '#open-quotes-tab-content button', function (e) {
        const numberSelected = parseInt( $(this).attr('data-number') );

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            $(this).addClass('selected');
        }

        if ($.inArray(numberSelected, numbersSelected) === -1) {
            numbersSelected.push(numberSelected);
        } else {
            numbersSelected.splice(numbersSelected.indexOf(numberSelected), 1);
        }

        generateNumbersSelected();

        return false;
    });

    $('#woo_raffles_discount_submit').click(function (e) {
        e.preventDefault();

        const $button = $(this);
        const $msg = $('#woo_raffles_discount_notice p');

        const $input = $('input[name="woo_raffles_discount_qty"]:checked');
        const qty = $input.val();
        const key = $input.attr('data-field');
        const product_id = $('#woo_raffles_product_id').val();

        $button.attr('disabled', 'disabled');

        removeClassNotices($msg);

        if (parseInt(qty) > 0 && parseInt(product_id) > 0) {
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                dataType: 'json',
                data: {
                    action: 'woo_discount_progressive',
                    key: key,
                    qty: qty,
                    product_id: product_id,
                },
                success: function (response) {

                    if (response.data.error) {
                        $msg.addClass('woocommerce-error');
                        $msg
                            .removeClass('hidden')
                            .html(response.data.msg);
                    } else {
                        $button.removeAttr('disabled');
                        scrollToTop();
                        redirect(response.data.redirect);
                    }
                },
                error: function (err) {
                    $msg.addClass('woocommerce-error');
                    $msg.removeClass('hidden').html(response.data.msg);
                    $button.removeAttr('disabled');

                    scrollToTop();
                }
            });
        }

        return false;
    });

    $('#load-more-numbers').click(function (e) {
        let html = ''
        const rendered = $('#woo_raffles_qty_rendered').val();
        let to_render = parseInt(rendered) + limit;

        if(to_render >= totalNumbers) {
            to_render = totalNumbers;
            $(this).hide();
        }

        for (let i = rendered; i < to_render; i++) {

            let btn_class = 'livres';
            if ($.inArray(i, numbersPayed) !== -1) btn_class = 'pagas';
            if ($.inArray(i, numbersReserved) !== -1) btn_class = 'reservadas';


            html += `
            <button type="button" class="btn btn-number ${btn_class}" data-number="${i}"
                ${$.inArray(i, numbersPayed) > -1 ? 'disabled' : ''}
            >
                ${i.toString().padStart(str_pad_left, '0')}
            </button>
        `
        }
        $('#woo_raffles_qty_rendered').val(to_render)

        $('#contentTodos .row').append(html);

    });

    $('#quotes-selected-submit').click(function (e) {
        e.preventDefault();
        const $msg = $('#woo_raffles_notice p');
        const product_id = $('#woo_raffles_product_id').val();
        $(this).attr('disabled', 'disabled');

        removeClassNotices($msg);

        if (numbersSelected.length > 0 && parseInt(product_id) > 0) {
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                dataType: 'json',
                data: {
                    action: 'woo_numbers_selected',
                    product_id: product_id,
                    numbers: numbersSelected.join(','),
                },
                success: function (response) {
                    if (response.data.error) {
                        $msg.addClass('woocommerce-error');
                        $msg
                            .removeClass('hidden')
                            .html(response.data.msg);
                    } else {
                        $(this).removeAttr('disabled');
                        scrollToTop();
                        redirect(response.data.route);
                    }
                },
                error: function (err) {
                    $msg.addClass('woocommerce-error');
                    $msg.removeClass('hidden').html(response.data.msg);
                    scrollToTop();
                }
            });
        }

        return false;
    });

    $('#open-quotes-tabs button').click(function (e) {
        const tab = $(this).attr('id');
        const buttons = $('#contentTodos button')
        switch (tab) {
            case 'tabLivres':
                buttons.hide();
                $('#contentTodos button.livres').show();
                break;
            case 'tabReservadas':
                buttons.hide();
                $('#contentTodos button.reservadas').show();
                break;
            case 'tabPagas':
                buttons.hide();
                $('#contentTodos button.pagas').show();
                break;
            default:
                buttons.show();
                break;
        }
    });

    function generateNumbersSelected() {
        
        if (numbersSelected.length > 0) {
            $('#woo-raffles-quotes-selected').removeClass('hidden');
        } else {
            $('#woo-raffles-quotes-selected').addClass('hidden');
        }
        numbersSelected.sort(function (a, b) {
            return a - b;
        });
        const dataContainer = $('#woo-raffles-quotes-selected #quotes-selected');
        let html = '<div class="row d-flex justify-content-center my-3">';
        numbersSelected.forEach(function (index) {
            html += `<div class="content"><span>${index.toString()}</span></div>
            `;
        });
        html += '</div>';
        dataContainer.html(html);
    }

    function generateObjData(number) {
        let result = [];

        for (let i = 1; i < number + 1; i++) {
            result.push({a: i});
        }

        return result;
    }

    function redirect(url) {
        if (url !== '') {
            window.setTimeout(function () {
                window.location.href = url;
            }, 1000);
        }
    }

    function removeClassNotices($msg) {
        $msg
            .addClass('hidden')
            .removeClass('woocommerce-error')
            .removeClass('woocommerce-message');
    }

    function scrollToTop() {
        return;

        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    }

    $('.itens__container__grid__item').click(function (e) {
        $('.itens__container__grid__item').removeClass('destacar_essa_opcao');
        $(this).addClass('destacar_essa_opcao');
    });
});