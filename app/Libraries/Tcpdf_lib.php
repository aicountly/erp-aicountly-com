<?php
namespace App\Libraries;
use TCPDF;
class Tcpdf_lib extends TCPDF
{
    // Declare variables to store dynamic header and footer content
    private $headerText = '';
    private $footerText = '';
	

    // Function to set dynamic header text
    public function setHeaderText($text)
    {
        $this->headerText = $text;
    }

    // Function to set dynamic footer text
    public function setFooterText($text)
    {
        $this->footerText = $text;
    }	

    // Override Header method to set dynamic headers
    public function Header()
    {
        // Set font for the header
       // $this->SetFont('helvetica', 'B', 12);
        
        // Dynamically set header content
		//$this->WriteHTMLCell(0, 0, $this->x, $this->GetY(), $this->headerText, 0, 1);
		$this->writeHTML($this->headerText, true, false, false, false, '');
        //$this->Cell(0, 10, $this->headerText, 0, 1, 'C');
    }

    // Override Footer method to set dynamic footers
    public function Footer()
    {
        // Set Y position for the footer
       
      
        // Dynamically set footer content
		$this->writeHTML($this->footerText, true, false, false, false, '');
       
    } 
}