<?php
require('../fpdf/fpdf.php');

$pdf = new FPDF('P', 'mm', 'A4');

//Margin -> Left, Top, Right
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

//border
$pdf->SetFont('Arial', '', 11);
$pdf->Rect(10, 10, 190, 260);

//outside border -  at the start
$pdf->SetXY(10, 5);
$pdf->Cell(180, 5, 'Tax Invoice', 0, 0, 'C');
$pdf->Cell(10, 5, 'Original', 0, 1, 'R');

//Inside border
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 6, 'FIREWORK TRADERS', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'GSTIN : 14DEFDJ62FFD63D', 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 4, 'Address : sivakasi,', 0, 1, 'C');
$pdf->Cell(0, 4, 'Sivakasi - 626123', 0, 1, 'C');
$pdf->Cell(0, 4, 'Virudhunagar, Tamil Nadu', 0, 1, 'C');
$pdf->Cell(0, 4, 'Ph: 123456789', 0, 1, 'C');
$pdf->Cell(0, 4, 'Email: fireworkcrakers@gmail.com', 0, 1, 'C');

//add line
$pdf->Line(10, 37, 200, 37);

//After line
$pdf->SetXY(10, 39);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 2, 'Buyer', 0, 0, 'L');
$pdf->Cell(1, 2, 'Delivery Address', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(63, 4, 'Invoice No', 0, 0, 'R');
$pdf->Cell(23, 4, ':2026INV01', 0, 1, 'R');

$pdf->SetXY(13, 44);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(81, 2, 'BUYER CRACKERS SHOP,', 0, 0, 'L');
$pdf->Cell(10, 2, 'BUYER CRACKERS SHOP', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(43, 6, 'Dated', 0, 0, 'R');
$pdf->Cell(30, 6, ':11-03-2026', 0, 1, 'R');

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(13, 47);
$pdf->Cell(70, 3, 'SIVAKASI,', 0, 0, 'L');
$pdf->Cell(7, 3, 'SIVAKASI', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(63, 13, 'Transport', 0, 0, 'R');
$pdf->Cell(15, 13, ':MISS', 0, 1, 'R');

$pdf->SetXY(13, 51);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(70, 3, 'Virudhunagar, Tamil Nadu,', 0, 0, 'L');
$pdf->Cell(12, 3, 'Virudhunagar', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(59, 18, 'HSN code', 0, 0, 'R');
$pdf->Cell(13, 18, ':3524', 0, 1, 'R');

$pdf->SetXY(13, 55);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(70, 3, 'Ph: 548754552146', 0, 0, 'L');
$pdf->Cell(10, 3, 'Tamil Nadu', 0, 0, 'C');

$pdf->SetXY(13, 59);
$pdf->Cell(70, 3, 'Id:ABCDEFGHI123456', 0, 0, 'L');
$pdf->Cell(13, 3, '548754552146', 0, 0, 'C');

//adding the box
$pdf->Line(75, 37, 75, 64);
$pdf->Line(135, 37, 135, 64);
$pdf->Line(10, 64, 200, 64);

//adding the table tittle
$pdf->SetXY(11, 70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 1, 'S.No', 0, 0, 'L');
$pdf->Cell(83, 1, 'Particulars', 0, 0, 'C');
$pdf->Cell(39, 1, 'Qty & Unit', 0, 0, 'C');
$pdf->Cell(22, 1, 'Rate(in Rs)', 0, 0, 'R');
$pdf->Cell(32, 1, 'Amount(in Rs)', 0, 1, 'R');

//adding the lines for table
$pdf->Line(10, 76, 200, 76);
$pdf->Line(21, 64, 21, 212);
$pdf->Line(109, 64, 109, 217);
$pdf->Line(141, 64, 141, 217);
$pdf->Line(171, 64, 171, 212);

//adding values
//1st
$pdf->SetXY(11, 79);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(10, 1, '1', 0, 0, 'L');
$pdf->Cell(83, 1, 'flower pot 30', 0, 0, 'L');
$pdf->Cell(58, 1, '30 Case', 0, 0, 'C');
$pdf->Cell(8, 1, '30.00', 0, 0, 'R');
$pdf->Cell(30, 1, '900.00', 0, 1, 'R');


//2st
$pdf->SetXY(11, 84);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(10, 1, '2', 0, 0, 'L');
$pdf->Cell(83, 1, 'flower pot 29', 0, 0, 'L');
$pdf->Cell(60, 1, '3 Case', 0, 0, 'C');
$pdf->Cell(6, 1, '30.00', 0, 0, 'R');
$pdf->Cell(30, 1, '90.00', 0, 1, 'R');

//adding net rate
$pdf->SetXY(25, 208.5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(10, 1, '((*) - Net Rated Products.Discount Not Applicable)', 0, 1, 'L');

//adding the lines
$pdf->Line(98, 212, 98, 243);

//adding other lines for total, discount etc.
$pdf->Line(10, 212, 200, 212);
$pdf->Line(10, 217, 200, 217);
$pdf->Line(98, 222, 200, 222);
$pdf->Line(98, 227, 200, 227);

//adding the total, dicount etc

//Total and Sub Total
$pdf->SetXY(98, 213.5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 2, 'Total', 0, 0, 'C');
$pdf->Cell(30, 2, '2', 0, 0, 'C');
$pdf->Cell(20, 2, 'Sub total', 0, 0, 'R');
$pdf->Cell(0, 2, '990.00', 0, 1, 'R');

//Discount 
$pdf->SetXY(150, 218.5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 2, 'Discount (Rs.20)', 0, 0, 'R');
$pdf->Cell(0, 2, '20.00', 0, 1, 'R');

//Discount Total
$pdf->SetXY(150, 223.5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 2, 'Discount Total', 0, 0, 'R');
$pdf->Cell(0, 2, '970.00', 0, 1, 'R');

//Extra Charges
$pdf->SetXY(150, 228.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 2, 'Extra Charges (Rs.30)', 0, 0, 'R');
$pdf->Cell(0, 2, '30.00', 0, 1, 'R');

//adding bill line
$pdf->Line(98, 232, 200, 232);
$pdf->Line(160, 212, 160, 243);

//adding bill total
$pdf->SetXY(150, 234.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, 'Bill Total', 0, 0, 'R');
$pdf->Cell(0, 6, '1000.00', 0, 1, 'R');

//adding amount charged line
$pdf->Line(10, 243, 200, 243);

//adding amount charged
$pdf->SetXY(10, 244);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 3, 'Amount Chargeable (in words):', 0, 0, 'L');
$pdf->Cell(0, 3, 'E. & O.E', 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, 'One Thousand Rupees Only', 0, 1, 'L');

//adding terms line
$pdf->Line(10, 252, 200, 252);

//Terms & Conditions
$pdf->SetXY(10, 252);
$pdf->SetFont('Arial', 'BU', 7);
$pdf->Cell(80, 4, 'Terms & Conditions', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(100, 4, 'FIREWORK TRADERS', 0, 1, 'R');
$pdf->SetFont('Arial', '', 7);
$long_para = "We are not responsiable for any loss, damage, Shortage or piferage during transit. Incase of any such loss, the\n buyers have to obtain proper certificates from careers with 21 days from the date of invoice and forward the same\n to us to enable to lodge claim with the insurace company";
$pdf->MultiCell(150, 4, $long_para, 0, 'J', false);
$pdf->SetXY(10, 267.5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(185, 3, 'Authorised Signatory', 0, 1, 'R');

//outside border - at the end
$pdf->SetXY(10, 270);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(180, 5, '**Composition dealer is not eligible to collect the taxes on supply.**', 0, 0, 'C');
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(10, 5, 'Page No : 1/1', 0, 1, 'R');

//Print output
$pdf->Output();
?>