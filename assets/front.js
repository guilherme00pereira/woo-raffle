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
        e.preventDefault();
        const numberSelected = $(this).attr('data-number');

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            $(this).addClass('selected');
        }

        if ($.inArray(numberSelected, numbersSelected) !== -1) {
            numbersSelected.splice(numbersSelected.indexOf(numberSelected), 1);
        } else {
            numbersSelected.push(numberSelected);
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
                    console.log(response)
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
        const to_render = parseInt(rendered) + limit;
        
        for(let i = rendered; i < to_render; i++) {
            
            let btn_class = allowDuplicate ? 'todos' : 'livres';
            if( $.inArray(i, numbersPayed) !== -1 ) btn_class = 'pagas';
            if( $.inArray(i, numbersReserved) !== -1 ) btn_class = 'reservadas';
            

            html += `
                <button type="button" class="btn btn-number ${btn_class}" data-number="${i}"
                    ${$.inArray(i, numbersPayed) > -1 ? 'disabled' : ''}
                >
                    ${i.toString().padStart(str_pad_left, '0')}
                </button>
            `
        }
        $('#woo_raffles_qty_rendered').val(rendered)

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
                    if (response?.error === '1') {
                        $msg.addClass('woocommerce-error');
                    } else {
                        $msg.addClass('woocommerce-message');
                    }

                    $msg
                        .removeClass('hidden')
                        .html(response?.msg);

                        $(this).removeAttr('disabled');

                    scrollToTop();

                    redirect(response);
                },
                error: function (err) {
                    console.error(err);
                    $(this).removeAttr('disabled');

                    scrollToTop();
                }
            });
        }

        return false;
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