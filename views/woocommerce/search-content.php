<?php

$products = $args['data'] ?? [];
$total = $args['total'] ?? 0;

if (count($products) > 0) {
    ?>
    <div class="bootstrap">
        <div class="grid grid-pad pt-4">
            <div class="container">
                <div class="row text-center my-2">
                    <div class="col-12">
                        <h4>Total de números: <?= $total ?></h4>
                    </div>
                </div>
                <?php
                foreach ($products as $product => $items): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <i class="fa-solid fa-clover"></i> &nbsp;
                        <b><?php echo esc_html($items[0]['product']); ?></b>
                    </div>
                </div>
                    <?php foreach ($items as $item):?>

                    <div class="row mb-5">
                        <div class="col-2">
                            Pedido
                            <?php echo esc_html($item['order_id']); ?>
                        </div>
                        <div class="col-10 d-flex flex-row justify-content-start flex-wrap">
                            <?php foreach ($item['generated_numbers'] as $number): ?>
                                <div class="orders-raffles-numbers">
                                    <?php echo esc_html(str_pad($number, $item['globos'], '0', STR_PAD_LEFT)); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                    endforeach;
                endforeach;
                ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="bootstrap">
        <p class="mt-3">
            <i>
                <?php esc_html_e('Nenhum número localizado.', 'woo-raffles'); ?>
            </i>
        </p>
    </div>
<?php } ?>