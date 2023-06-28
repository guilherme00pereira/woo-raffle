<?php
$all_numbers = $args['all_numbers'] ?? [];
$str_pad_left = $args['globos'] ?? 5;
$limit = $args['limit'] ?? 100;
$numbers_reserved = $args['numbers_reserved'] ?? [];
$numbers_payed = $args['numbers_payed'] ?? [];
$numbers_open = (count($all_numbers) -1)  - (count($numbers_reserved) + count($numbers_payed));
$numbers_selected = $args['numbers_selected'] ?? [];
$product_id = $args['product_id'] ?? '';
$price = $args['price'] ?? 0;
$is_open_quotes = get_post_meta($product_id, '_woo_raffles_numbers_open', true);
$allow_duplicate = get_field("cotas_duplicadas", $product_id);
$shortcode_style = $args['style_shortcode'] ?? [];

//if ($is_open_quotes !== 'yes') return;
?>
<style>
#open-quotes-tab-content button.selected {
    <?= $shortcode_style['btn_selected'] ?>
}

#open-quotes-tab-content button.todos {
    <?= $shortcode_style['aba_todas'] ?>
}

#open-quotes-tab-content button.livres {
    <?= $shortcode_style['aba_livres'] ?>
}

#open-quotes-tab-content button.reservadas {
    <?= $shortcode_style['aba_reservadas'] ?>
}

#open-quotes-tab-content button.pagas {
    <?= $shortcode_style['aba_pagas'] ?>
}
</style>

<div class="bootstrap">
    <div>
        <div id="woo_raffles_notice">
            <p class="hidden"></p>
        </div>
        <div id="woo-raffles-open-quotes">
            <div class="data-container">
                <ul id="open-quotes-tabs" class="list-unstyled">
                        <li class="list-inline-item">
                            <button type="button" id="tabTodos" style="<?= $shortcode_style['aba_todas'] ?>">
                                Todos<span>(<?= count($all_numbers) - 1 ?>)</span>
                            </button>
                        </li>
                        <li class="list-inline-item">
                            <button type="button" id="tabLivres" style="<?= $shortcode_style['aba_livres'] ?>">
                                Livres<span>(<?= $numbers_open ?>)</span>
                            </button>
                        </li>
                        <li class="list-inline-item">
                            <button type="button" id="tabReservadas" style="<?= $shortcode_style['aba_reservadas'] ?>">
                                Reservadas<span>(<?= count($numbers_reserved); ?>)</span>
                            </button>
                        </li>
                        <li class="list-inline-item">
                            <button type="button" id="tabPagas" style="<?= $shortcode_style['aba_pagas'] ?>">
                                Pagas<span>(<?= count($numbers_payed); ?>)</span>
                            </button>
                        </li>
                </ul>
                <div id="open-quotes-tab-content" class="tab-content">
                    <div id="contentTodos">
                        <div class="row">
                            <?php for ($i = 0; $i < $limit; $i++):
                                $btn_class = 'livres';
                                if( in_array($i, $numbers_payed) )$btn_class = 'pagas';
                                if( in_array($i, $numbers_reserved) )$btn_class = 'reservadas';
                                $selected = in_array($i, $numbers_selected) ? 'selected' : '';

                                ?>
                                <button type="button" class="btn btn-number <?= $btn_class . " " . $selected ?>"
                                        data-number="<?php echo esc_html($i); ?>" data-type="<?= $btn_class ?>"
                                        <?php echo in_array($i, $numbers_payed) ? 'disabled' : ''; ?>
                                >
                                    <?php echo esc_html(str_pad($i, $str_pad_left, '0', STR_PAD_LEFT)); ?>
                                </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-center">
                        <button id="load-more-numbers" style="<?= $shortcode_style['btn_carregar_mais_numeros'] ?>">
                            Carregar mais números
                        </button>
                    </div>
            </div>
            <input type="hidden" id="woo_raffles_product_id" value="<?php echo esc_html($product_id); ?>"/>
            <input type="hidden" id="woo_raffles_qty_rendered" value="<?php echo esc_html($limit); ?>"/>
        </div>
            <article class="widget-rifa-modelo-2 aposta" id="modalRifa">
                <header id="woo-raffles-quotes-modal" class="aposta__header">
                    <aside>
                        <?= esc_html__( 'Números escolhidos', 'plugin-rifa-drope') ?>
                    </aside>
                    <button class="aposta__header__close" type="button"></button>
                </header>
                <section class="aposta__content">
                    <div class="coluna-1" id="colunaUm">
                        Nenhum número escolhido ainda
                    </div>
                    <div class="coluna-2" id="colunaDois">

                    </div>
                    <button style="<?= $shortcode_style['btn_finalizar_compra'] ?>" id="quotes-selected-submit">
                        Finalizar Compra
                    </button>
                </section>
            </article>

    </div>
</div>
<script type="text/javascript">
    let numbersPayed = [<?php echo esc_html(implode(',', $numbers_payed)); ?>];
    let numbersSelected = [<?php echo esc_html(implode(',', $numbers_selected)); ?>];
    const numbersReserved = [<?php echo esc_html(implode(',', $numbers_reserved)); ?>];
    const totalNumbers = <?php echo esc_html(count($all_numbers)); ?>;
    const limit = <?php echo esc_html($limit); ?>;
    const str_pad_left = <?php echo $str_pad_left ?>;
    const openQuoteItemPrice = <?php echo $price ?>;
</script>
