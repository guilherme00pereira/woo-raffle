<?php

namespace WooRaffles\Admin;

use Mpdf\Mpdf;

class ExportPdf extends Template
{
    public static function generatePDF($rows)
    {

        try {
            $pdf = new Mpdf();
            $pdf->debug = true;
            ob_start();
            self::getPart('raffle', 'pdf', [
                'rows' => $rows,
                //'logo' => wp_get_attachment_image_url(get_option('raffle_logo_export_attachment_id')),
            ]);
            $html = ob_get_contents();
            if($html === "") throw new \Exception("Não foi possível gerar o PDF");
            $pdf->WriteHTML($html);
            ob_clean();
            $pdf->Output('woo-raffle.pdf', 'D');
        } catch (\Exception $e) {
            var_dump($e->getMessage());die;
        }
    }
}