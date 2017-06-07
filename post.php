<?php
require('fpdf.php');


class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('images/logo.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(10,10,'Eenmaal Andermaal',0,0,'C');
        // Line break
        $this->Ln(40);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(10,10,'Pagina '.$this->PageNo(),0,0,'C');
    }
}

$voornaam = 'Maurits';
$achternaam = 'Muijsert';
$today = getdate();
$datum = $today['mday'].'-'.$today['mon'].'-'. $today['year'];
$adresregel = 'adresregel';
$postcode = 'postcode';
$plaats = 'plaats';
$activatiecode = 123456;

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Times','',10);
$pdf->Cell(0, 4, 'Eenmaal Andermaal', 4, 1);
$pdf->Cell(0, 4, 'Ruitenberglaan 26', 4, 1);
$pdf->Cell(0, 4, 'Tel. 024 353 0500', 4, 1);
$pdf->Cell(0, 4, 'E-mail info@iproject2.icasites.nl', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, $voornaam.' '.$achternaam, 4, 1);
$pdf->Cell(0, 4, $adresregel, 4, 1);
$pdf->Cell(0, 4, $postcode .' '.$plaats, 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Arnhem, '.$datum, 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Betreft: activatie verkopers account', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Beste '.$voornaam.' '.$achternaam.', ', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->MultiCell(0, 4, 'Wij hebben uw aanvraag voor het registreren als verkoper bij EenmaalAndermaal succesvol ontvangen. U heef er voor gekozen om uw aanvraag te bevestigen per post, daarom ontvangt u hierbij een code die u hiervoor kan gebruiken. De onderstaande code kan via de profielpagina worden ingevoerd.', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Activatiecode: '.$activatiecode, 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Wij wensen u veel veilingplezier op onze website!', 4, 1);
$pdf->Cell(0, 4, ' ', 4, 1);
$pdf->Cell(0, 4, 'Vriendelijke groet,', 4, 1);
$pdf->Cell(0, 4, 'EenmaalAndermaal BV', 4, 1);
$pdf->Output();
?>