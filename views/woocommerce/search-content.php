<?php
$columns = $args['columns'] ?? 1;
$data = $args['data'] ?? [];
if ($data) {
    ?>
    <div class="grid grid-pad">
        <?php
        if (count($data) > 0) {
            foreach ($data as $item) {
                $generated_numbers = explode(',', $item->quotes);
                $str_pad_left = get_post_meta($item->product_id, '_woo_raffles_str_pad_left', true) ?? 5;
                $product = wc_get_product($item->product_id);
                if ($product) {
                    ?>
                    <div class="bootstrap">
                        <h5 class="orders-raffles-numbers-title mt-3">
                            <i class="fa-solid fa-clover"></i> &nbsp;
                            <?php echo esc_html($product->get_name()); ?>
                        </h5>
                        <div class="row">
                            <?php foreach ($generated_numbers as $number): ?>
                                <div class="col-lg-<?php echo esc_attr($columns); ?> col-md-2 col-3 mt-32">
                                    <div class="content">
                                        <div class="orders-raffles-numbers">
                                            <?php echo esc_html(str_pad($number, $str_pad_left, '0', STR_PAD_LEFT)); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
    <?php
} else {
    ?>
    <div class="bootstrap">
        <p class="mt-3">
            <i><?php esc_html_e('Nenhum número localizado.', 'woo-raffles'); ?></i>
        </p>
    </div>
    <?php
}
?>