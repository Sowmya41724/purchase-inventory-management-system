<?php
include "../config.php";
require('../fpdf/fpdf.php');

$Date = $Party = $Billno = $Product = $Unit = $Quantity = $Rate = $Amount = $Total = "";
$Name = $Mobileno = $Address = $City = $Pincode = $Email = $Party_type = "";
$sales_data_rows = [];
$party_data_rows = [];
$buyer_lines = [];


if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];

    $sales_stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
    $sales_stmt->bind_param("i", $edit_id);
    $sales_stmt->execute();
    $sales_result = $sales_stmt->get_result();

    if ($sales_result->num_rows > 0) {
        $sales_data_rows[] = $sales_result->fetch_assoc();
    }
    $Date = $sales_data_rows[0]['date'];
    $Party = $sales_data_rows[0]['party_type'] ?? null;

    if ($Party) {
        $party_stmt = $conn->prepare("SELECT * FROM Party WHERE `name` = ?");
        $party_stmt->bind_param("s", $Party);
        $party_stmt->execute();
        $party_result = $party_stmt->get_result();

        if ($party_result->num_rows > 0) {
            $party_data_rows[] = $party_result->fetch_assoc();
        }
    }
}

//Access sales data
foreach ($sales_data_rows as $sales) {
    $Date = $sales['date'];
    $Billno = $sales['bill_no'];
    $Party = $sales['party_type'];
    $Product = $sales['product'];
    $Unit = $sales['unit'];
    $Quantity = $sales['quantity'];
    $Rate = $sales['rate'];
    $Amount = $sales['amount'];
    $Total = $sales['total'];
}

// Access party data
foreach ($party_data_rows as $party) {
    $Name = $party['name'];
    $Mobileno = $party['mobile_no'];
    $Address = $party['address'];
    $City = $party['city'];
    $Pincode = $party['pincode'];
    $Email = $party['email'];
    $Party_type = $party['party_type'];
}

$buyer_lines = [];

if (!empty($Name)) {
    $buyer_lines[] = $Name;
}
if (!empty($Address)) {
    $buyer_lines[] = $Address;
}
// Combine City and Pincode into one line if at least one exists
$city_line = '';
if (!empty($City)) {
    $city_line .= $City;
}
if (!empty($Pincode)) {
    $city_line .= ($city_line ? ' - ' : '') . $Pincode;
}
if (!empty($city_line)) {
    $buyer_lines[] = $city_line;
}
if (!empty($Mobileno)) {
    $buyer_lines[] = 'Ph: ' . $Mobileno;
}
if (!empty($Email)) {
    $buyer_lines[] = 'Email: ' . $Email;
}

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

// ---------- (Buyer / Delivery Address / Bill No) ----------
$pdf->SetXY(10, 39);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 2, 'Buyer', 0, 0, 'L');
$pdf->Cell(1, 2, 'Delivery Address', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(56, 4, 'Bill no', 0, 0, 'R');
$pdf->Cell(32, 4, ':' . $Billno, 0, 1, 'R');

// ---------- Date (fixed Y positions) ----------
$rightX = 136;   // start
$rightW = 60;    // width 

$pdf->SetXY($rightX, 44);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(11, 6, 'Dated', 0, 0, 'R');
$pdf->Cell(30, 6, ':' . $Date, 0, 1, 'R');

// ---------- Buyer / Delivery (dynamic) ----------
$startY = 44;          // first line Y
$lineH = 4;            // height per line
$currentY = $startY;

foreach ($buyer_lines as $line) {
    // Left column (buyer)
    $pdf->SetXY(13, $currentY);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(62, $lineH, $line, 0, 0, 'L');

    // Middle column00 (delivery)
    $pdf->SetXY(79, $currentY);
    $pdf->Cell(62, $lineH, $line, 0, 0, 'L');

    $currentY += $lineH;
}


//adding the box
$pdf->Line(75, 37, 75, 64);
$pdf->Line(135, 37, 135, 64);
$pdf->Line(10, 64, 200, 64);

$tableStartY = 70; // original table Y
if ($currentY + 2 > $tableStartY) {
    // if buyer lines pushed Y lower, adjust table start
    $tableStartY = $currentY + 5;
}

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
$pdf->Line(21, 64, 21, 227);
$pdf->Line(109, 64, 109, 232);
$pdf->Line(141, 64, 141, 232);
$pdf->Line(171, 64, 171, 227);

//adding values
$product_count = 0;

$m = 1;
$pdf->SetXY(11, 79);
$pdf->SetFont('Arial', '', 10);
foreach ($sales_data_rows as $sales) {
    $products = explode(',', $sales['product']);
    $units = explode(',', $sales['unit']);
    $quantities = explode(',', $sales['quantity']);
    $rates = explode(',', $sales['rate']);
    $amounts = explode(',', $sales['amount']);

    for ($i = 0; $i < count($products); $i++) {
        $pdf->Cell(10, 10, $m++, 0, 0, 'L');
        $pdf->Cell(83, 10, trim($products[$i]), 0, 0, 'L');
        $pdf->Cell(58, 10, $quantities[$i] . " " . $units[$i], 0, 0, 'C');
        $pdf->Cell(8, 10, $rates[$i], 0, 0, 'R');
        $pdf->Cell(30, 10, $amounts[$i], 0, 0, 'R');
        $pdf->Ln();
        $pdf->SetX(11);
        $product_count++;
    }
}

//adding net rate
$pdf->SetXY(25, 223.5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(10, 1, '((*) - Net Rated Products.Discount Not Applicable)', 0, 1, 'L');

//adding the lines for total 
$pdf->Line(98, 227, 98, 243);
$pdf->Line(10, 227, 200, 227);

// Adding Total and Sub Total
$pdf->SetXY(98, 228.5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 2, 'Total', 0, 0, 'C');
$pdf->Cell(30, 2, $product_count, 0, 0, 'C');
$pdf->Cell(20, 2, 'Sub total', 0, 0, 'R');
$pdf->Cell(0, 2, $Total, 0, 1, 'R');


//adding bill line
$pdf->Line(10, 232, 200, 232);
$pdf->Line(160, 227, 160, 243);

//adding bill total
$pdf->SetXY(150, 234.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, 'Bill Total', 0, 0, 'R');
$pdf->Cell(0, 6, $Total, 0, 1, 'R');

//adding amount charged line
$pdf->Line(10, 243, 200, 243);

$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
$tx = $f->format($Total);

//adding amount charged
$pdf->SetXY(10, 244);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 3, 'Amount Chargeable (in words):', 0, 0, 'L');
$pdf->Cell(0, 3, 'E. & O.E', 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, ucwords($tx) . ' Rupees Only', 0, 1, 'L');

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
$pdf->SetXY(10, 266.5);
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