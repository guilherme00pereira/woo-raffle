<?php
$generated_numbers = $args['numbers'] ?? [];
$str_pad_left = $args['str_pad_left'] ?? 5;
$description = $args['description'] ?? '';
$order = $args['order'] ?? null;
$random = $args['random'] ?? 'no';

if($order && $random === 'no' &&
    ($order->get_status() === 'pending' || $order->get_status() === 'on-hold')):
?>
<tr>
    <td colspan="2">
        <div class="form-field" style="display: inline-block;">
            <label for="add-open-numbers-to-order-item">Adicionar números abertos</label>
            <input id="add-open-numbers-to-order-item" name="add-open-numbersto-order-item" type="text" 
                placeholder="Ex: 1,2,3,4,5" style="line-height: 1;" data-item="<?= $args['item_id'] ?? 0 ?>" />
        </div>
        <button type="button" class="button button-secondary add-open-numbers-order-item" style="vertical-align: baseline;">
            <span class="dashicons dashicons-plus" style="vertical-align: middle;"></span>
        </button>
        <span id="loading-add-open-numbers" class="spinner is-active" style="display: none; margin-top: 20px;"></span>
    </td>
</tr>
<tr>
    <td colspan="2">
        <div id="message-add-open-numbers" style="display: none; color: #b81c23;"></div>
    </td>
</tr>

<?php
endif;


if ($generated_numbers) {
    ?>
    <tr>
        <td colspan="6">
            <h3>
                <i class="fa-solid fa-clover"></i> &nbsp;
                <?php esc_html_e('Números da Sorte', 'woo-raffles'); ?>
            </h3>
            <div>
                <?= $description ?>
            </div>
            <div class="bootstrap">
                <div class="row">
                    <?php
                    foreach ($generated_numbers as $item) {
                        ?>
                        <div class="col-lg-1 col-md-2 col-2">
                            <div class="content">
                                <div class="orders-raffles-numbers">
                                    <?php echo esc_html(str_pad($item->generated_number, $str_pad_left, '0', STR_PAD_LEFT)); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
    <?php
}
?>
