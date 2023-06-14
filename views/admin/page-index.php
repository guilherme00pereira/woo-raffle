<?php
$products = wc_get_products([
    'status' => 'publish',
    'limit' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);
$logo = wp_get_attachment_url(get_option('raffle_logo_export_attachment_id'))
?>

<div class="wrap">
    <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
    </h1>
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
                            <select name="selectProductforFilter" id="selectProductforFilter" multiple="multiple">
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="quota-search1">
                        <th scope="row"><label for="raffleStatus">Cota 01</label></th>
                        <td>
                            <input name="quotaNumber" type="text" id="quotaNumber1" value="" class="regular-text">
                        </td>
                    </tr>
                    <tr id="quota-search2">
                        <th scope="row"><label for="raffleStatus">Cota 02</label></th>
                        <td>
                            <input name="quotaNumber" type="text" id="quotaNumber2" value="" class="regular-text">
                        </td>
                    </tr>
                    <tr id="quota-search3">
                        <th scope="row"><label for="raffleStatus">Cota 03</label></th>
                        <td>
                            <input name="quotaNumber" type="text" id="quotaNumber3" value="" class="regular-text">
                        </td>
                    </tr>
                    </tr>
                    <tr id="search-button">
                        <td colspan="2">
                            <button id="searchRaffle" class="button button-primary">Pesquisar cota</button>
                            <span id="loading" class="spinner"></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div id="raffle-data"></div>
            </div>
            <div id="tab-02">
                <div style="display: flex;">
                    <div style="border-right: 1px solid #ccc;">
                        <h3>Logo</h3>
                        <div class='image-preview-wrapper'>
                            <img id='image-preview'
                                    src='<?= $logo; ?>'
                                    width='200'>
                        </div>
                        <input id="upload_image_button" type="button" class="button"
                                value="<?php _e('Upload image'); ?>"/>
                        <input type='hidden' name='image_attachment_id' id='image_attachment_id'
                                value='<?php echo get_option('raffle_logo_export_attachment_id'); ?>'>
                        <button type="button" id="submit_logo_exporter" class="button-primary">Salvar</button>
                        <?php if ($logo): ?>
                            <button type="button" id="remove_logo_exporter" class="button-danger">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        <?php endif; ?> 
                        <div id="logo-return"></div>
                    </div>
                    <div style="display: flex; flex-direction: column; margin-left: 40px;">
                        <h3>Exportar</h3>
                        <div style="padding: 0;display: flex;flex-direction: column;">
                            <label for="selectProductforExport">Selecione o sorteio</label>
                            <select name="selectProductforExport" id="selectProductforExport">
                                <option value="0">Selecione</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product->get_id(); ?>"><?php echo $product->get_name(); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <button id="exportRaffleExcel" class="button button-primary"
                                    style="background-color: #1D6F42; border-color: #1D6F42;">Exportar Excel
                            </button>
                            <button id="exportRafflePdf" class="button button-primary"
                                    style="background-color: #F40F02; border-color: #F40F02;">Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>