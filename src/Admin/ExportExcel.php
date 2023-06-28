<?php

namespace WooRaffles\Admin;

use Shuchkin\SimpleXLSXGen;
use UPFlex\MixUp\Core\Base;

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
            if ($file_type === 'csv') {
                $rows = self::generateExcelRows($numbers, $product_id);
                $end = count($rows);
                $xlsx = SimpleXLSXGen::fromArray($rows)
                    ->setColWidth(0, 100)
                    ->setColWidth(1, 100)
                    ->mergeCells('A2:B2')
                    ->mergeCells('A' . $end . ':B' . $end);
                $xlsx->downloadAs('woo-raffles-v1.xlsx');
            }
            if ($file_type === 'pdf') {
                ob_end_clean(); 
                $pdf = new ExportPdf();
                $pdf->SetFont('Arial', '', 10);
                $pdf->AddPage('P', 'A4', 0);
                $rows = self::generatePdfRows($numbers, $product_id);
                foreach ($rows as $row) {
                    if(count($row) == 2)
                    {
                        $pdf->Cell(100, 10, $row[0]);
                        $pdf->Cell(40, 10, $row[1]);
                        $pdf->Ln();
                    } else {
                        $pdf->Cell(140, 10, $row[0]);
                        $pdf->Ln();
                    }
                }
                $pdf->Output('D', 'woo-raffles-v1.pdf', true);
                unset($pdf);
            }
        }
    }

    protected static function generateExcelRows($numbers, $product_id): array
    {
        $rows = [];

        $attId = get_option('raffle_logo_export_attachment_id');
        $image = wp_get_attachment_image_url($attId);
        $product = wc_get_product($product_id);
        $warningText = "TESTE";


        $rows[0] = [
            '<img src="' . $image . '" />',
            '<middle><center><style height="60" bgcolor="#000000" color="#FFFFFF">' . $product->get_title() . '</style></center></middle>',
        ];

        $rows[1] = [
            '<middle><center><style bgcolor="#F28500" color="#FFFFFF">' . $warningText . '</style></center></middle>',
        ];

        $rows[2] = [
            '<center><style bgcolor="#000000" color="#FFFFFF">PARTICIPANTES</style></center>',
            '<center><style bgcolor="#000000" color="#FFFFFF">NÚMERO DA SORTE</style></center>',
        ];

        if ($numbers) {
            $y = 3;

            foreach ($numbers as $item) {
                $fn = $item->first_name ?? '';
                $ln = $item->last_name ?? '';
                //$key_list = "{$item->order_id}__$y";
                $rows[$y]['nome'] = $fn . ' ' . $ln;
                $rows[$y]['cotas_escolhidas'] = $item->quotes ?? '';
                $y++;
            }
        }

        $rows[] = [
            '<middle><center><style bgcolor="#F28500" color="#FFFFFF">' . $warningText . '</style></center></middle>',
        ];
        return $rows;
    }

    protected static function generatePdfRows($numbers, $product_id): array
    {
        $rows = [];
        $warningText = "TESTE";

        //$rows[1] = [$warningText,null];

        $rows[2] = [
            'PARTICIPANTES',
            'NÚMERO DA SORTE',
        ];

        if ($numbers) {
            $y = 3;

            foreach ($numbers as $item) {
                $fn = $item->first_name ?? '';
                $ln = $item->last_name ?? '';
                //$key_list = "{$item->order_id}__$y";
                $rows[$y][0] = $fn . ' ' . $ln;
                $rows[$y][1] = $item->quotes ?? '';
                $y++;
            }
        }
        return $rows;
    }

    public static function generateRowsQuickie($cotas, $data): array
    {
        $winners = $data[0];
        $names = $data[1];
        if(count($names) > 0) {
            sort($names);
            array_unshift($names, '');
        }
        sort($cotas);

        $rows = [];
        $rows[0] = $names;
        foreach ($cotas as $idx => $cota) {
            $rows[$idx + 1][0] = $cota;
        }
        
        foreach ($winners as $key => $value) {
            $keys = explode('|', $key);
            $pname = array_search($keys[0], $names);
            $cidx = array_search($keys[1], $cotas);
            $rows[$cidx + 1][$pname] = $value;
        }
        
        for($i=0; $i<count($names); $i++)
        {
            for($j=0; $j<count($cotas)+1; $j++)
            {
                if(!isset($rows[$j][$i]))
                {
                    $rows[$j][$i] = "";
                }
            }
        }

        for($i=0;$i<count($rows);$i++)
        {
            ksort($rows[$i]);
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
            $rows = self::generateRowsQuickie($cotas, $data);
            
            $xlsx = SimpleXLSXGen::fromArray($rows);
            $xlsx->downloadAs('woo-raffles-rapidinha.xlsx');
        }
    }
}