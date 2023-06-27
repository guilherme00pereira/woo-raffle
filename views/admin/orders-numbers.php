<?php
$generated_numbers = $args['numbers'] ?? [];
$str_pad_left = $args['str_pad_left'] ?? 5;
$description = $args['description'] ?? '';
?>
<script>
    const orderItemId = <?= $args['item_id'] ?? 0 ?>;
</script>
<tr>
    <td colspan="2">
        <span>Adicionar números abertos</span>
        <input id="add-open-numbersto-order-item" name="add-open-numbersto-order-item" type="text" placeholder="Ex: 1,2,3,4,5" />
        <button type="button" class="button button-primary add-open-numbers-order-item" style="padding-top: 5px;">
            <span class="dashicons dashicons-plus-alt"></span>
        </a>
    </td>
    
</tr>

<?php
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
