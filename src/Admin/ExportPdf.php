<?php

namespace WooRaffles\Admin;

use Fpdf\Fpdf;

class PDF extends FPDF
{
    function Header()
    {
        $attId = get_option('raffle_logo_export_attachment_id');
        $image = wp_get_attachment_image_url($attId);
        $this->Image($image, 10, 6, 30);
        $this->Ln(30);
    }
}