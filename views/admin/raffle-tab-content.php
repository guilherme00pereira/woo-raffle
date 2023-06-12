<div id="woo_raffle_options" class="panel woocommerce_options_panel">
    <div class="bootstrap grid-pad">
        <div class="container pt-3 pb-5">
            <div class="row">
                <div class="col-lg-6">
                    <?php
                    woocommerce_wp_checkbox([
                        'id' => '_woo_raffles_numbers_random',
                        'wrapper_class' => 'show_if_woo_raffle',
                        'label' => __('Números Aleatórios', 'woo-raffles'),
                        'description' => __('Permitir que os números sejam gerados de forma aleatória', 'woo-raffles'),
                        'default' => 'yes',
                        'desc_tip' => false,
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_woo_raffles_str_pad_left',
                        'wrapper_class' => 'show_if_woo_raffle',
                        'label' => __('Zero à esquerda', 'woo-raffles'),
                        'description' => __('Definir a quantidade de zeros à esquerda', 'woo-raffles'),
                        'default' => '5',
                        'desc_tip' => true,
                        'type' => 'number',
                    ]);

                    woocommerce_wp_text_input([
                        'id' => '_woo_raffles_max_stock',
                        'wrapper_class' => 'show_if_woo_raffle',
                        'label' => __('Estoque máximo', 'woo-raffles'),
                        'description' => __('Definir a quantidade máxima no estoque', 'woo-raffles'),
                        'default' => '5',
                        'desc_tip' => true,
                        'type' => 'number',
                    ]);
                    ?>
                    <?php $product = wc_get_product(get_the_ID()); ?>
                    <div class="mt-5">
                        <span>
                            <strong><?php esc_html_e('Qtd. números vendidos:', 'woo-raffles'); ?></strong>
                            <?php echo esc_html($product->get_total_sales('') ?? 0); ?>
                        </span>
                    </div>
                    <div class="mt-2">
                        <span>
                            <strong><?php esc_html_e('Qtd. números vendidos + estoque:', 'woo-raffles'); ?></strong>
                            <?php echo esc_html(($product->get_stock_quantity('') + $product->get_total_sales('')) ?? 0); ?>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="content">
                        <h3>
                            <?php esc_html_e('Exportar números para Excel', 'woo-raffles'); ?>
                        </h3>
                        <p>
                            <?php esc_html_e('Clique no botão abaixo para gerar uma planilha com as informações sobre os números vendidos.'); ?>
                        </p>

                        <div id="woo_raffles_export_msg">
                            <p class="notice hidden"></p>
                        </div>

                        <?php $export_button_label = __('Exportar', 'woo-raffles'); ?>
                        <a class="button button-primary button-large" id="woo_raffles_export_numbers"
                           aria-label="<?php echo esc_attr($export_button_label); ?>" href="#">
                            <?php echo esc_html($export_button_label); ?>
                        </a>
                    </div>
                    <div class="content pt-3">
                        <h3>
                            <?php esc_html_e('Sortear vencedor', 'woo-raffles'); ?>
                        </h3>
                        <p>
                            <?php esc_html_e('Clique no botão abaixo para sortear o vencedor do sorteio.'); ?>
                        </p>

                        <div id="woo_raffles_drawn_number">
                            <?php
                            $product_id = $product->get_id();
                            $number_raffled = get_post_meta($product_id, '_woo_raffles_raffled_number', true);
                            $order_raffled = get_post_meta($product_id, '_woo_raffles_raffled_order', true);
                            $str_pad_left = get_post_meta($product_id, '_woo_raffles_str_pad_left', true) ?? 5;
                            ?>
                            <p class="notice <?php echo esc_attr($number_raffled ? 'notice-success' : 'hidden'); ?>">
                                <?php
                                if ($number_raffled) {
                                    echo __(
                                        sprintf('O número sorteado foi: %d <a href="%s" target="_blank">[Pedido %d]</a>.',
                                            str_pad($number_raffled, $str_pad_left, '0'),
                                            admin_url("/post.php?post={$order_raffled}&action=edit"),
                                            $order_raffled
                                        ),
                                        'woo-raffles'
                                    );
                                }
                                ?>
                            </p>
                        </div>

                        <?php $export_button_label = __('Sortear', 'woo-raffles'); ?>
                        <a class="button button-primary button-large" id="woo_raffles_raffle_number"
                           aria-label="<?php echo esc_attr($export_button_label); ?>" href="#">
                            <?php echo esc_html($export_button_label); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>