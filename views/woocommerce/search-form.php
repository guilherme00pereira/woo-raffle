<?php
$product_id = $args['product_id'] ?? 0;
?>

<div class="bootstrap">
    <div class="row">
        <div class="col-12">
            <form method="post" action="">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <input type="hidden" id="product_id" value="<?= $product_id; ?>">
                        <input id="search-cpf-val" type="text" name="cpf" class="form-control woo-raffles-search input-cpf"
                               placeholder="<?php esc_attr_e('Digite seu CPF', 'woo-raffles'); ?>" required/>
                    </div>
                    <div class="col-lg-6 col-12">
                        <button id="btn-search-cpf-numbers" type="submit" class="btn btn-primary woo-raffles-submit mt-2">
                            <?php esc_html_e('Buscar', 'woo-raffles'); ?>
                            <span id="loading-search-cpf-numbers" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                </div>
                <div id="cpf-numbers-search-result">

                </div>
            </form>
        </div>
    </div>
</div>