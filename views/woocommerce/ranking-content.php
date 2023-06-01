<table>
    <thead>
    <tr>
        <th>
            <?php esc_html_e('Rank', 'woo-raffles'); ?>
        </th>
        <th>
            <?php esc_html_e('Cliente', 'woo-raffles'); ?>
        </th>
        <th>
            <?php esc_html_e('Quantidade', 'woo-raffles'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $data = $args['data'] ?? [];
    if ($data) {
        $x = 1;
        foreach ($data as $item) {
            ?>
            <tr>
                <td data-label="<?php esc_attr_e('Ranking', 'woo-raffles'); ?>">
                    <span class="badge-ranking">
                        <?php echo esc_html($x); ?>
                    </span>
                </td>
                <td data-label="<?php esc_attr_e('Nome', 'woo-raffles'); ?>">
                    <?php echo esc_html($item->user_name); ?>
                </td>
                <td data-label="<?php esc_attr_e('Quantidade', 'woo-raffles'); ?>">
                    <?php echo esc_html($item->qty); ?>
                </td>
            </tr>
            <?php
            $x++;
        }
    }
    ?>
    </tbody>
</table>