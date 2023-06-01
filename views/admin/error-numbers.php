<?php $numbers = $args['numbers'] ?? 0; ?>

<div class="bootstrap">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">

                <h4 class="woocommerce-layout__header-heading">
                    <?php echo esc_html($args['title'] ?? __('Pedidos com erros nos números', 'woo-raffles')); ?>
                </h4>

                <table class="table table-hover" id="woo_raffle_error_numbers_table">
                    <thead>
                    <tr>
                        <th scope="col">
                            <?php esc_html_e('Pedido', 'woocommerce'); ?>
                        </th>
                        <th scope="col">
                            <?php esc_html_e('Problema', 'woocommerce'); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($numbers): ?>
                        <?php foreach ($numbers as $number): ?>
                            <tr>
                                <th scope="row">
                                    <a href="<?php echo esc_url(admin_url("post.php?post={$number->order_id}&action=edit")); ?>" target="_blank">
                                        <?php echo esc_html($number->order_id); ?>
                                    </a>
                                </th>
                                <td>
                                    <?php echo esc_html(apply_filters('woo_raffles_error_numbers', $number->error, $number->sum_quotes, $number->qty)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                <?php esc_html_e('Nenhum número localizado'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>