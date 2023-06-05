<?php
$products = wc_get_products([
    'status' => 'publish',
    'limit' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div>
        <header class="section-header">
            <h2>Ver dados do cliente sorteado</h2>
            <hr>
        </header>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="selectProduct">Selecione o produto</label></th>
                <td>
                    <select name="selectProduct" id="selectProduct">
                        <option value="0">Selecione</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="input_id">Digite o número sorteado</label></th>
                <td><input name="input_id" type="text" id="input_id" value="" class="regular-text"></td>
            </tr>
            </tbody>
        </table>
        <div style="width: 150px;">
            <button id="searchWinner" class="button button-secondary">Pesquisar</button>
            <span id="loading" class="spinner"></span>
        </div>
        <div id="winner"></div>
    </div>
</div>
