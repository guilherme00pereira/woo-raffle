<?php
$products = wc_get_products([
    'status' => 'publish',
    'limit' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);
?>

<div class="bootstrap">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">

                <h4 class="woocommerce-layout__header-heading">
                    <?php echo esc_html($args['title'] ?? __('Pedidos com erros nos números', 'woo-raffles')); ?>
                </h4>

                <div>

                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"><label for="selectProductforFilter">Filtrar por produto</label></th>
                            <td class="select-raffle-cell">
                                <select name="selectProductforFilter" id="selectProductforFilter">
                                    <option value="0">Selecione o produto</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>

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

                    </tbody>
                </table>
                <div id="loadingErrors" style="display: none;" class="alert alert-light" role="alert">
                    <span style="margin-right: 40px;"></span>
                    <img alt="carregando" src="<?php echo esc_url( includes_url() . 'js/tinymce/skins/lightgray/img//loader.gif' ); ?>" />
                </div>
            </div>
        </div>
    </div>
</div>