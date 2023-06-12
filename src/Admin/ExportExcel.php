<?php

namespace WooRaffles\Admin;

use Shuchkin\SimpleXLSXGen;
use UPFlex\MixUp\Core\Base;
use WooRaffles\Admin\PDF;
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
        if ($product_id > 0) {
            $numbers = GenerateNumbers::getNumbersByProductId($product_id, false);
            $rows = self::generateRows($numbers);
            if ($file_type === 'csv') {
                $xlsx = SimpleXLSXGen::fromArray($rows);
                $xlsx->downloadAs('woo-raffles-v1.xlsx');
            }
            if ($file_type === 'pdf') {
                ob_end_clean();
                $pdf = new PDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', '', 10);
                foreach ($rows as $index=>$row) {
                    if($index > 0) {
                        $pdf->Cell(100, 10, $row['nome']);
                        $pdf->Cell(40, 10, $row['cotas_escolhidas']);
                        $pdf->Ln();
                    }
                }
                $pdf->Output('D', 'woo-raffles-v1.pdf', true);
            }
        }
    }

    protected static function generateRows($numbers): array
    {
        $rows = [];
        $rows[0] = [
            __('NOME COMPRADOR', 'woo-raffles'),
            __('NÚMEROS RESERVADO', 'woo-raffles'),
        ];

        if ($numbers) {
            $y = 1;

            foreach ($numbers as $item) {
                $fn = $item->first_name ?? '';
                $ln = $item->last_name ?? '';
                $key_list = "{$item->order_id}__$y";
                $rows[$key_list]['nome'] = $fn . ' ' . $ln;
                $rows[$key_list]['cotas_escolhidas'] = $item->quotes ?? '';
                $y++;
            }
        }

        return $rows;
    }
}