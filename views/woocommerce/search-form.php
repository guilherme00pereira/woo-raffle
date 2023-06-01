<?php $cpf = $args['cpf'] ?? ''; ?>

<div class="bootstrap">
    <div class="row">
        <div class="col-12">
            <form method="post" action="">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <input type="text" name="cpf" class="form-control woo-raffles-search input-cpf"
                               placeholder="<?php esc_attr_e('Digite seu CPF', 'woo-raffles'); ?>"
                               value="<?php echo $cpf; ?>" required/>
                    </div>
                    <div class="col-lg-6 col-12">
                        <button type="submit" class="btn btn-primary woo-raffles-submit mt-2">
                            <?php esc_html_e('Buscar', 'woo-raffles'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>