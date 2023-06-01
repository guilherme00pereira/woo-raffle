<div>
    <?php
    $generated_numbers = $args['numbers'] ?? [];
    $str_pad_left = $args['str_pad_left'] ?? 5;
    if ($generated_numbers):
        ?>
        <h3><?php esc_html_e('Números da Sorte: ', 'woo-raffles');  ?></h3>
        <p style="word-break: break-all;">
            <?php
            $x = 0;
            foreach ($generated_numbers as $item):
                echo $x > 0 ? ', ' : '';
                echo esc_html(str_pad($item->generated_number, $str_pad_left, '0', STR_PAD_LEFT));
                $x++;
            endforeach;
            ?>
        </p>
    <?php
    endif;
    ?>
</div>