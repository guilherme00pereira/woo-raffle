<?php

namespace WooRaffles\Admin;

use Shuchkin\SimpleXLSXGen;
use UPFlex\MixUp\Core\Base;
use WooRaffles\Woocommerce\GenerateNumbers;

if (!defined('ABSPATH')) {
    exit;
}

class ExportExcel extends Base
{
    public function __construct()
    {
        add_action('woo_raffles_export_file', [self::class, 'createFile'], 10, 2);
    }

    public static function createFile($product_id, $file_type)
    {
        if ($product_id > 0 && $file_type === 'csv') {
            $numbers = GenerateNumbers::getNumbersByProductId($product_id, false);
            $rows = self::generateRows($numbers);
            $xlsx = SimpleXLSXGen::fromArray($rows);

            $xlsx->downloadAs('woo-raffles-v1.xlsx');
        }
    }

    protected static function generateRows($numbers): array
    {
        $rows = [];
        $rows[0] = [
            __('PEDIDO', 'woo-raffles'),
            __('NOME COMPRADOR', 'woo-raffles'),
            __('E-MAIL DO COMPRADOR', 'woo-raffles'),
            __('NÚMEROS RESERVADO', 'woo-raffles'),
            __('PRODUTO', 'woo-raffles'),
        ];

        if ($numbers) {
            $y = 1;

            foreach ($numbers as $item) {
                $key_list = "{$item->order_id}__$y";
                $rows[$key_list]['id'] = $item->order_id ?? '';
                $rows[$key_list]['nome'] = $item->user_name ?? '';
                $rows[$key_list]['email'] = $item->user_email ?? '';
                $rows[$key_list]['cotas_escolhidas'] = $item->quotes ?? '';
                $rows[$key_list]['produto'] = $item->product_name ?? '';

                $y++;
            }
        }

        return $rows;
    }
}