<?php
include "../config.php";
require('../fpdf/fpdf.php');

$Date = $Party = $Billno = $Product = $Unit = $Quantity = $Rate = $Amount = $Total = "";
$Name = $Mobileno = $Address = $City = $Pincode = $Email = $Party_type = "";
$purchase_data_rows = [];
$party_data_rows = [];
$buyer_lines = [];

if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];

    $purchase_stmt = $conn->prepare("SELECT * FROM Purchase WHERE id = ?");
    $purchase_stmt->bind_param("i", $edit_id);
    $purchase_stmt->execute();
    $purchase_result = $purchase_stmt->get_result();

    if ($purchase_result->num_rows > 0) {
        $purchase_data_rows[] = $purchase_result->fetch_assoc();
    }
    $Date = $purchase_data_rows[0]['date'];
    $Party = $purchase_data_rows[0]['party_type'] ?? null;

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

//Access purchase data
foreach ($purchase_data_rows as $purchase) {
    $Date = $purchase['date'];
    $Billno = $purchase['bill_no'];
    $Party = $purchase['party_type'];
    $Product = $purchase['product'];
    $Unit = $purchase['unit'];
    $Quantity = $purchase['quantity'];
    $Rate = $purchase['rate'];
    $Amount = $purchase['amount'];
    $Total = $purchase['total'];
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
$pdf->Cell(180, 5, 'Estimate', 0, 1, 'C');

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
$pdf->Cell(0, 4, 'Email: fireworkcrackers@gmail.com', 0, 1, 'C');

//add line
$pdf->Line(10, 37, 200, 37);


// ---------- HEADER ROW (Buyer / Bill No) ----------
$pdf->SetXY(10, 39);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 2, 'Buyer', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(41, 4, 'Bill no', 0, 0, 'R');
$pdf->Cell(14, 4, ':' . $Billno, 0, 1, 'R');

// ---------- RIGHT SIDE DETAILS (fixed Y positions) ----------
$rightX = 120;   // start of right column (after vertical line at 135)
$rightW = 60;    // width for right column cells

// Dated (Y=44)
$pdf->SetXY($rightX, 44);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(11, 6, 'Dated', 0, 0, 'R');
$pdf->Cell(30, 6, ':' . $Date, 0, 1, 'R');

// ---------- BUYER (dynamic) ----------
$startY = 44;          // first line Y
$lineH = 4;            // height per line (matches original 4mm
$currentY = $startY;

foreach ($buyer_lines as $line) {
    // Left column (buyer) – from X=13 to X=75 (width 62)
    $pdf->SetXY(13, $currentY);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(62, $lineH, $line, 0, 0, 'L');

    $currentY += $lineH;
}


//adding the box
$pdf->Line(119, 37, 119, 64);
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
//1st
$product_count = 0;

$m = 1;
$pdf->SetXY(11, 79);
$pdf->SetFont('Arial', '', 10);
foreach ($purchase_data_rows as $purchase) {
    $products = explode(',', $purchase['product']);
    $units = explode(',', $purchase['unit']);
    $quantities = explode(',', $purchase['quantity']);
    $rates = explode(',', $purchase['rate']);
    $amounts = explode(',', $purchase['amount']);

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