<?php

namespace WooRaffles\Admin;

use Shuchkin\SimpleXLSXGen;
use UPFlex\MixUp\Core\Base;
use WooRaffles\Admin\ExportPdf;
use WooRaffles\Woocommerce\GenerateNumbers;

if (!defined('ABSPATH')) {
    exit;
}

class ExportExcel extends Base
{
    public function __construct()
    {
        add_action('woo_raffles_export_file', [self::class, 'createFile'], 10, 2);
        add_action('woo_raffles_export_quickie', [self::class, 'createFileQuickie'], 10, 2);
    }

    public static function createFile($product_id, $file_type)
    {
        if ($product_id > 0) {
            $numbers = Database::getNumbersByProductId($product_id, false);
            $rows = self::generateRows($numbers);
            if ($file_type === 'csv') {
                $xlsx = SimpleXLSXGen::fromArray($rows);
                $xlsx->downloadAs('woo-raffles-v1.xlsx');
            }
            if ($file_type === 'pdf') {
                ob_end_clean();
                $pdf = new ExportPdf();
                $pdf->AddPage();
                $pdf->SetFont('Arial', '', 10);
                foreach ($rows as $index => $row) {
                    if ($index > 0) {
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

    public static function generateRowsQuickie($product_ids, $data)
    {
        $rows = [];
        $rows[0] = $product_ids;
var_dump($data);die;
        foreach ($data as $item) {
            $rows[$item->order_id]['nome'] = $item->first_name . ' ' . $item->last_name;
            $rows[$item->order_id]['cotas_escolhidas'] = $item->quotes;
        }
        return $rows;
    }

    public static function createFileQuickie($product_ids, $quotes)
    {
        $pids = explode(',', $product_ids);
        $cotas = explode(',', $quotes);
        if(count($pids) > 0 && count($cotas) > 0)
        {
            $data = Database::getRaffleCustomersPerQuoteAndProduct($pids, $cotas);
            $rows = self::generateRowsQuickie($product_ids, $data);

//            $xlsx = SimpleXLSXGen::fromArray($rows);
//            $xlsx->downloadAs('woo-raffles-rapidinha.xlsx');
        }
    }
}