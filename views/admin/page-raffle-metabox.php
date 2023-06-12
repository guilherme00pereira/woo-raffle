<?php
$product = wc_get_product(get_the_ID());
$cotas = $args['cotas'];
$estoque = $product->get_stock_quantity('');
$cotas_abertas = $args=['cotas_abertas'];
?>

<div class="raffle-meta-box">
    <div>
        <h3>Cotas vendidas</h3>
        <p><?= $cotas ?></p>
    </div>
    <div>
        <h3>Estoque</h3>
        <p><?= $estoque ?></p>
    </div>
    <div>
        <h3>Cotas Abertas</h3>
        <div id="quotesOpenStatus">
            <?php if($cotas_abertas): ?>
                <span class="quotes-open-message">Este sorteio é por cotas abertas</span>
            <?php else: ?>
                <button id="turnQuotesOpen" class="button button-primary">Tornar cotas abertas</button>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <h3>Shortcode</h3>
        <div>
        <?php if($cotas_abertas): ?>
                <!-- <input id="cotas-abertas-shortcode" type="hidden" value="[woo-raffles-cotas_abertas id='<?= get_the_ID() ?>']" /> -->
                <span>[woo-raffles-cotas_abertas id="<?= get_the_ID() ?>"]</span>
                <!-- <button type="button" class="button button-secondary" id="shortcodeQuotesOpen">
                    <span class="dashicons dashicons-admin-page"></span>
                </button> -->
            <?php else: ?>
                <span>[woo_raffles_raffle id="<?= get_the_ID() ?>"]</span>
            <?php endif; ?>
        </div>
    </div>
</div>
