<?php

/**
 * Created by PhpStorm.
 * User: LHX
 * Date: 2017/8/3
 * Time: 10:19
 */
class Ext_PDF extends TCPDF
{
    //Page header
    public function Header() {
        // Logo
//        $image_file = K_PATH_IMAGES.'logo_example.jpg';
//        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('stsongstdlight', '', 8);
        // Title
        $this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('stsongstdlight', '', 8);
        // Page number
        $this->Cell(0, 10, '第'.$this->getAliasNumPage().'页，共'.$this->getAliasNbPages().'页', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}