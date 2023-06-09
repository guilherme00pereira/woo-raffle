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
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Title', 1, 0, 'C');
        $this->Ln(20);
    }
}