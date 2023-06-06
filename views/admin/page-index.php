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
            <h2>Ver dados do sorteio</h2>
            <hr>
        </header>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="selectProduct">Selecione o sorteio</label></th>
                <td class="select-raffle-cell">
                    <select name="selectProduct" id="selectProduct">
                        <option value="0">Selecione</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="loading" class="spinner"></span>
                </td>
            </tr>
            <tr id="quota-search" style="display: none;">
                <th scope="row"><label for="raffleStatus">Número da cota</label></th>
                <td>
                <input name="quotaNumber" type="text" id="quotaNumber" value="" class="regular-text">
                </td>
            </tr>
            <tr id="search-button" style="display: none;">
                <td colspan="2">
                    <button id="searchRaffle" class="button button-primary">Pesquisar cota</button>
                </td>
            </tr>
            </tbody>
        </table>
        <div id="raffle-data">
            <div class="notice notice-warning"><p>Warning notice</p></div>
            <div class="notice notice-success"><p>Success notice</p></div>
            <div class="notice notice-info"><p>Info notice</p></div>
        </div>
    </div>
</div>
