jQuery(document).ready(function ($) {
    $('.input-cpf').mask('000.000.000-00', {reverse: true});

    const $inputNumbers = $('#woo_raffles_numbers');
    const str_pad_left = 5;

    if ($('#woo-raffles-open-quotes').length) {
        $('#woo-raffles-open-quotes').pagination({
            dataSource: generateObjData($inputNumbers.attr('data-qty')),
            pageSize: 100,
            showPrevious: false,
            showNext: false,
            callback: function (data, pagination) {
                const dataContainer = $('#woo-raffles-open-quotes .data-container');

                let html = `
                <div class="bootstrap">
                    <div class="row mb-20">`;

                $.each(data, function (index, item) {
                    html += `  
                        <div class="col-lg-1 col-md-2 col-3">
                            <div class="content">
                                <div class="orders-raffles-numbers ${$.inArray(item.a.toString(), numbersSelected) !== -1 ? 'selected' : ''} ${$.inArray(item.a, numbersDisabled) !== -1 ? 'disabled' : ''}">
                                    ${
                        $.inArray(item.a, numbersDisabled) !== -1
                            ? item.a.toString().padStart(str_pad_left, '0')
                            : `
                                                <a href="#" data-value="${item.a}">
                                                    ${item.a.toString().padStart(str_pad_left, '0')}
                                                </a>
                                               `
                    }
                                   
                                </div>
                            </div>
                        </div>
                        `;
                });

                html += `
                    </div>
                </div>
                `;

                dataContainer.html(html);
            }
        });
    }

    if ($('#woo-raffles-quotes-selected').length) {
        generateNumbersSelected();
    }

    $('body').on('click', '.orders-raffles-numbers a', function (e) {
        e.preventDefault();

        const numberSelected = $(this).attr('data-value');
        const $parent = $(this).parent();

        if ($parent.hasClass('selected')) {
            $parent.removeClass('selected');
        } else {
            $parent.addClass('selected');
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
                    if (response?.error === '1') {
                        $msg.addClass('woocommerce-error');
                    } else {
                        $msg.addClass('woocommerce-message');
                    }

                    $msg
                        .removeClass('hidden')
                        .html(response?.msg);

                    $button.removeAttr('disabled');

                    scrollToTop();

                    redirect(response);
                },
                error: function (err) {
                    console.error(err);
                    $button.removeAttr('disabled');

                    scrollToTop();
                }
            });
        }

        return false;
    });

    $('#quotes-selected-form').submit(function (e) {
        e.preventDefault();

        const $button = $(this).find('button');
        const $msg = $('#woo_raffles_notice p');

        const product_id = $('#woo_raffles_product_id').val();

        $button.attr('disabled', 'disabled');

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

                    $button.removeAttr('disabled');

                    scrollToTop();

                    redirect(response);
                },
                error: function (err) {
                    console.error(err);
                    $button.removeAttr('disabled');

                    scrollToTop();
                }
            });
        }

        return false;
    });

    function generateNumbersSelected() {
        if (numbersSelected.length > 0) {
            $('#quotes-selected-submit').removeClass('hidden');
            $('#quotes-selected-title').removeClass('hidden');
        } else {
            $('#quotes-selected-submit').addClass('hidden');
            $('#quotes-selected-title').addClass('hidden');
        }

        numbersSelected.sort(function (a, b) {
            return a - b;
        });

        $inputNumbers.val(numbersSelected.join(','));

        const dataContainer = $('#woo-raffles-quotes-selected #quotes-selected');

        let html = `
        <div class="bootstrap">
            <div class="row mb-4">
        `;

        numbersSelected.forEach(function (index) {
            html += `  
                    <div class="col-lg-1 col-md-2 col-3">
                        <div class="content">
                            <div class="orders-raffles-numbers">
                                ${index.toString().padStart(str_pad_left, '0')}
                            </div>
                        </div>
                    </div>
                    `;
        });

        html += `
            </div>
        </div>
        `;

        dataContainer.html(html);
    }

    function generateObjData(number) {
        let result = [];

        for (let i = 1; i < number + 1; i++) {
            result.push({a: i});
        }

        return result;
    }

    function redirect(response) {
        if (response?.redirect !== '') {
            window.setTimeout(function () {
                window.location.href = response?.redirect;
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
});