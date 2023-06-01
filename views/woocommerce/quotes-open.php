<?php $qty = $args['qty'] ?? 0; ?>
<?php $numbers_selected = $args['numbers_selected'] ?? []; ?>
<?php $numbers_disabled = $args['numbers_disabled'] ?? []; ?>
<?php $product_id = $args['product_id'] ?? ''; ?>

<?php $numbers_open = get_post_meta($product_id, '_woo_raffles_numbers_open', true); ?>
<?php if ($numbers_open !== 'yes') return; ?>

<div class="grid">
    <div class="col-1-1">
        <div id="woo_raffles_notice">
            <p class="hidden"></p>
        </div>

        <form method="post" action="" id="quotes-selected-form">
            <div id="woo-raffles-open-quotes">
                <div class="data-container"></div>
                <input type="hidden" id="woo_raffles_product_id" value="<?php echo esc_html($product_id); ?>"/>
                <input type="hidden" id="woo_raffles_numbers" data-qty="<?php echo esc_html($qty); ?>"/>
            </div>
            <div id="woo-raffles-quotes-selected" class="bootstrap">
                <h3 id="quotes-selected-title" class="mt-5 hidden">
                    <i class="fa-solid fa-clover"></i> &nbsp;
                    <?php esc_html_e('Números selecionados', 'woo-raffles'); ?>
                </h3>
                <div id="quotes-selected"></div>

                <button class="btn btn-primary hidden" id="quotes-selected-submit">
                    <?php esc_html_e('Add to cart', 'woocommerce'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    let numbersDisabled = [<?php echo esc_html(implode(',', $numbers_disabled)); ?>];
    let numbersSelected = [<?php echo esc_html(implode(',', $numbers_selected)); ?>];
</script>