<?php
$products = wc_get_products([
    'status' => 'publish',
    'limit' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div>
        <div id="raffle-tabs" class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active" data-tab="tab-01">Pesquisar Cota</a>
            <a href="#" class="nav-tab" data-tab="tab-02">Exportar</a>
        </div>
        <div id="raffle-tabs-content" class="tabs-content">
            <div id="tab-01">
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
                    <tr id="quota-search">
                        <th scope="row"><label for="raffleStatus">Número da cota</label></th>
                        <td>
                            <input name="quotaNumber" type="text" id="quotaNumber" value="" class="regular-text">
                        </td>
                    </tr>
                    <tr id="search-button">
                        <td colspan="2">
                            <button id="searchRaffle" class="button button-primary">Pesquisar cota</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div id="raffle-data"></div>
            </div>
            <div id="tab-02">Tab #02 content here</div>
        </div>
    </div>
</div>
