<?php
include "../config.php";

$edit_id = "";
$Edit = 0;

$DateErr = $PartyErr = $BillnoErr = $ProductErr = $UnitErr = $QuantityErr = $RateErr = $AmountErr = $tableError = "";
$Date = $Party = $Billno = $Product = $Unit = $Quantity = $Rate = $Amount = $Total = "";

$topProduct = '';
$topUnit = '';
$topQuantity = '';
$topRate = '';
$topAmount = '';

if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];
    $Edit = 1;

    $sql = "SELECT * FROM Purchase WHERE id='$edit_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $Date = $data['Date'];
        $Billno = $data['BillNo'];
        $Party = $data['Party_Type'];
        $Product = $data['Product'];
        $Unit = $data['Unit'];
        $Quantity = $data['Quantity'];
        $Rate = $data['Rate'];
        $Amount = $data['Amount'];
        $Total = $data['Total'];
    }
}

if ($Edit == 1 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_POST['productArray'] = array_map('trim', explode(',', $Product));
    $_POST['unitArray'] = array_map('trim', explode(',', $Unit));
    $_POST['quantityArray'] = array_map('trim', explode(',', $Quantity));
    $_POST['rateArray'] = array_map('trim', explode(',', $Rate));
    $_POST['amountArray'] = array_map('trim', explode(',', $Amount));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $hasRows = isset($_POST['productArray']) && count($_POST['productArray']) > 0;

    $error = 0;
    $edit_id = $_POST['edit_id'] ?? 0;
    $Edit = ($edit_id > 0) ? 1 : 0;

    // Validate date
    if (empty($_POST['date'])) {
        $DateErr = "Date is required";
        $error = 1;
    } else {
        $Date = test_input($_POST['date']);
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $Date)) {
            $DateErr = "Invalid date format";
            $error = 1;
        }
    }

    // Validate party
    if (empty($_POST['party'])) {
        $PartyErr = "Party is required";
        $error = 1;
    } else {
        $Party = test_input($_POST['party']);
    }

    // Validate bill number
    if (empty($_POST['billno'])) {
        $BillnoErr = "Bill number is required";
        $error = 1;
    } else {
        $Billno = test_input($_POST['billno']);
        if (!preg_match("/^[0-9]*$/", $Billno)) {
            $BillnoErr = "Only numbers are allowed";
            $error = 1;
        } else {
            if ($Edit == 1) {
                $checkBill = mysqli_query(
                    $conn,
                    "SELECT id FROM purchase WHERE BillNo = '$Billno' AND id != '$edit_id'"
                );
            } else {
                $checkBill = mysqli_query(
                    $conn,
                    "SELECT id FROM purchase WHERE BillNo = '$Billno'"
                );
            }

            if (mysqli_num_rows($checkBill) > 0) {
                $BillnoErr = "Bill number already exists";
                $error = 1;
            }
        }
    }

    // If no rows in table, validate the top input fields
    if (!$hasRows) {
        if (empty($_POST['product'])) {
            $ProductErr = "Product is required";
            $error = 1;
        }

        if (empty($_POST['unit'])) {
            $UnitErr = "Unit is required";
            $error = 1;
        }

        if (empty($_POST['quantity'])) {
            $QuantityErr = "Quantity is required";
            $error = 1;
        }

        if (empty($_POST['rate'])) {
            $RateErr = "Rate is required";
            $error = 1;
        }
    }

    // After validating all header fields, check for table rows only if no other errors
    if ($error == 0 && !$hasRows) {
        $tableError = "Please add at least one item to the table before submitting";
        $error = 1;
    }

    if (isset($_POST['productArray'])) {
        $Product = implode(',', $_POST['productArray']);
        $Unit = implode(',', $_POST['unitArray']);
        $Quantity = implode(',', $_POST['quantityArray']);
        $Rate = implode(',', $_POST['rateArray']);
        $Amount = implode(',', $_POST['amountArray']);
    }

    if (!empty($_POST['total'])) {
        $Total = test_input($_POST['total']);
    }

    // For top input fields after POST, use POST values
    $topUnit = $_POST['unit'] ?? '';
    $topQuantity = $_POST['quantity'] ?? '';
    $topRate = $_POST['rate'] ?? '';
    $topAmount = $_POST['amount'] ?? '';

    if ($error === 0) {
        if ($Edit == 0) {
            $sql = "INSERT INTO Purchase 
            (Date, BillNo, Party_Type, Product, Unit, Quantity, Rate, Amount, Total) 
            VALUES (?,?,?,?,?,?,?,?,?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sisssssss",
                $Date,
                $Billno,
                $Party,
                $Product,
                $Unit,
                $Quantity,
                $Rate,
                $Amount,
                $Total
            );

            $stmt->execute();
            header("Location: edit_purchase.php?success=1");
            exit;
        } else {
            $sql = "UPDATE Purchase SET Date=?, BillNo=?, Party_Type=?, Product=?, Unit=?, Quantity=?, Rate=?, Amount=?, Total=? WHERE id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sisssssssi",
                $Date,
                $Billno,
                $Party,
                $Product,
                $Unit,
                $Quantity,
                $Rate,
                $Amount,
                $Total,
                $edit_id
            );

            $stmt->execute();
            header("Location: edit_purchase.php?edit_success=1");
            exit;
        }
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($stmt)) {
    $stmt->close();
}

// Determine the current unit value for the top dropdown
$currentUnitValue = '';
if (isset($_POST['unit']) && !empty($_POST['unit'])) {
    $currentUnitValue = isset($_POST['unit']) ? $_POST['unit'] : '';
} elseif (!empty($topUnit)) {
    $currentUnitValue = $topUnit;
} else {
    $currentUnitValue = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Purchase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon"
        href="https://img.freepik.com/premium-vector/uoe-logo-design-initial-letter-uoe-monogram-logo-using-hexagon-shape_1101554-59452.jpg?semt=ais_hybrid&w=740&q=80">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url("https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRj4ImSMrTKEfFwSyFH80xtk2SZvGU_kb274Q&s");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }

        header {
            background-color: #0a2463;
            color: white;
            padding: 50px;
        }

        h1 {
            text-align: center;
        }

        .input-container {
            padding: 50px 50px 50px 50px;
            margin: 5%;
            border-radius: 12px;
            background-color: white;
            color: black;
        }

        input[type="submit"] {
            padding: 10px;
            width: 100%;
        }

        .error {
            color: red;
            font-size: 0.85em;
            display: block;
            margin-top: 2px;
        }

        .table-error {
            color: red;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }

        input[type="submit"]:hover {
            background-color: #0a2463;
            color: white;
            width: 100%;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100px;
            height: 20px;
        }

        select {
            width: 100px;
            height: 25px;
        }

        footer {
            background-color: #0a2463;
            color: white;
            padding: 30px;
        }

        button {
            margin-bottom: 10px;
            background-color: #0a2463;
            color: white;
            border: 1px;
            cursor: pointer;
            padding: 10px;
        }

        button:hover {
            background-color: #DDD;
            color: black;
        }

        a {
            text-decoration: none;
            color: white;
        }

        a:hover {
            color: black;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #DDD;
        }

        td:hover {
            background-color: #0a2463;
            color: white;
        }

        td#no-hover:hover {
            background-color: initial;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            background-color: white;
            display: flex;
            justify-content: center;
        }

        ul li a {
            display: block;
            color: black;
            padding: 14px 16px;
            text-decoration: none;
        }

        ul li a:hover {
            background-color: #0a2463;
            color: white;
        }

        input[type="number"]#change {
            border: none;
            outline: none;
            text-align: left;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }

        td:hover input[type="number"]#change {
            background-color: #0a2463;
            color: white;
        }

        input[type="number"].row-amount {
            border: none;
            outline: none;
            text-align: left;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }

        td:hover input[type="number"].row-amount {
            background-color: #0a2463;
            color: white;
        }

        .field-group {
            display: inline-block;
            margin-right: 15px;
            vertical-align: top;
            text-align: left;
        }

        .rate-amount-button-wrapper {
            display: inline-flex;
            align-items: flex-start;
            gap: 5px;
        }

        .rate-amount-button-wrapper .field-group {
            margin-right: 0;
        }

        .rate-amount-button-wrapper button {
            margin-top: 18px;
            /* align with input fields */
            height: 25px;
            padding: 0 10px;
            line-height: 25px;
        }
    </style>
    <script src="../js/jquery.min.js"></script>
</head>

<body>
    <header>
        <h1>Purchase</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="../Unit/Unit_table.php">Unit</a></li>
            <li><a href="../Product/Product_table.php">Product</a></li>
            <li><a href="../Party/Party_Page.php">Party</a></li>
        </ul>
    </nav>

    <div class="input-container">
        <button onclick="location.href='edit_purchase.php'" type="button">&#8618; Database</button>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="purchaseForm">
            <div style="text-align: center;">

                <!-- Date field -->
                <div class="field-group">
                    <label for="Date">Date</label><br>
                    <input type="date" id="Date" name="date"
                        value="<?php echo isset($_POST['date']) ? $_POST['date'] : (isset($Date) ? $Date : ''); ?>">
                    <span class="error"><?php echo $DateErr; ?></span>
                </div>

                <!-- Party field -->
                <div class="field-group">
                    <label for="Party">Party</label><br>
                    <select id="Party" name="party">
                        <option value="">Select</option>
                        <?php
                        $partySQL = "SELECT Party_Type FROM Party";
                        $partyResult = mysqli_query($conn, $partySQL);
                        if (mysqli_num_rows($partyResult) > 0) {
                            while ($partyRow = mysqli_fetch_assoc($partyResult)) {
                                $selected = '';
                                $currentParty = isset($_POST['party']) ? $_POST['party'] : (isset($Party) ? $Party : '');
                                if (trim($currentParty) == trim($partyRow['Party_Type'])) {
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?php echo $partyRow['Party_Type']; ?>" <?php echo $selected; ?>>
                                    <?php echo $partyRow['Party_Type']; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <span class="error"><?php echo $PartyErr; ?></span>
                </div>

                <!-- Bill number field -->
                <div class="field-group">
                    <label for="Billno">Bill no</label><br>
                    <input type="text" name="billno" id="Billno" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                        value="<?php echo isset($_POST['billno']) ? $_POST['billno'] : (isset($Billno) ? $Billno : ''); ?>">
                    <span class="error"><?php echo $BillnoErr; ?></span>
                </div>

                <br><br>

                <!-- Product field -->
                <div class="field-group">
                    <label for="Product">Product</label><br>
                    <select id="Product" name="product" onchange="showUser(this.value)">
                        <option value="">Select</option>
                        <?php
                        $prodSQL = "SELECT `Name`, id FROM Product";
                        $prodResult = mysqli_query($conn, $prodSQL);
                        if (mysqli_num_rows($prodResult) > 0) {
                            while ($prodRow = mysqli_fetch_assoc($prodResult)) {
                                $selected = '';
                                $currentProduct = isset($_POST['product']) ? $_POST['product'] : '';
                                if ($currentProduct == $prodRow['id']) {
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?php echo $prodRow['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo $prodRow['Name']; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <span class="error"><?php echo $ProductErr; ?></span>
                </div>

                <!-- Unit field (now with persistence) -->
                <div class="field-group">
                    <label for="Unit">Unit</label><br>
                    <select id="Unit" name="unit">
                        <option value="">Select</option>
                        <?php
                        // Get all units from Unit table
                        $unitSQL = "SELECT unitValue FROM Product";
                        $unitResult = mysqli_query($conn, $unitSQL);
                        $unitOptions = array();
                        if (mysqli_num_rows($unitResult) > 0) {
                            while ($unitRow = mysqli_fetch_assoc($unitResult)) {
                                $unitOptions[] = $unitRow['unitValue'];
                            }
                        }
                        // If the current unit value is not in the options, add it so it can be selected
                        if (!empty($currentUnitValue) && !in_array($currentUnitValue, $unitOptions)) {
                            $unitOptions[] = $currentUnitValue;
                        }
                        foreach ($unitOptions as $unitVal) {
                            $selected = ($currentUnitValue == $unitVal) ? 'selected' : '';
                            echo "<option value=\"$unitVal\" $selected>$unitVal</option>";
                        }
                        ?>
                    </select>
                    <span class="error"><?php echo $UnitErr; ?></span>
                </div>

                <!-- Quantity field -->
                <div class="field-group">
                    <label for="Quantity">Quantity</label><br>
                    <input type="number" id="Quantity" name="quantity" oninput="Add()"
                        value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : ''; ?>">
                    <span class="error"><?php echo $QuantityErr; ?></span>
                </div>

                <!-- Rate, Amount, and + button inline -->
                <div class="rate-amount-button-wrapper">
                    <!-- Rate field -->
                    <div class="field-group">
                        <label for="rateHint">Rate</label><br>
                        <input type="text" id="rateHint" name="rate" readonly
                            value="<?php echo isset($_POST['rate']) ? $_POST['rate'] : ''; ?>">
                        <span class="error"><?php echo $RateErr; ?></span>
                    </div>
                    <!-- Amount field -->
                    <div class="field-group">
                        <label for="Amount">Amount</label><br>
                        <input type="text" id="Amount" name="amount" readonly
                            value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>">
                        <span class="error"><?php echo $AmountErr; ?></span>
                    </div>
                    <!-- + button -->
                    <button type="button" id="addItem">+</button>
                </div>

                <!-- Table error message (appears below the inline group) -->
                <div style="margin-top: 5px; text-align: center;">
                    <span class="table-error"><?php echo $tableError; ?></span>
                </div>
                <br>
            </div>

            <br><br>
            <table>
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="purchaseTableBody">
                    <?php
                    if (isset($_POST['productArray']) && is_array($_POST['productArray'])) {
                        for ($i = 0; $i < count($_POST['productArray']); $i++) {
                            ?>
                            <tr>
                                <td class="row-id"><?php echo $i + 1; ?></td>
                                <td><?php echo $_POST['productArray'][$i]; ?>
                                    <input type="hidden" name="productArray[]"
                                        value="<?php echo $_POST['productArray'][$i]; ?>">
                                </td>
                                <td><?php echo $_POST['unitArray'][$i]; ?>
                                    <input type="hidden" name="unitArray[]" value="<?php echo $_POST['unitArray'][$i]; ?>">
                                </td>
                                <td>
                                    <input type="number" class="edit-qty" value="<?php echo $_POST['quantityArray'][$i]; ?>">
                                    <input type="hidden" name="quantityArray[]"
                                        value="<?php echo $_POST['quantityArray'][$i]; ?>">
                                </td>
                                <td>
                                    <input type="number" class="edit-rate" value="<?php echo $_POST['rateArray'][$i]; ?>">
                                    <input type="hidden" name="rateArray[]" value="<?php echo $_POST['rateArray'][$i]; ?>">
                                </td>
                                <td>
                                    <input type="number" class="row-amount" value="<?php echo $_POST['amountArray'][$i]; ?>"
                                        readonly>
                                    <input type="hidden" name="amountArray[]" value="<?php echo $_POST['amountArray'][$i]; ?>">
                                </td>
                                <td>
                                    <button type="button" class="deleteItem">&#x1F5D1; Delete</button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="5" align="right"><strong>Total</strong></td>
                        <td><input type="number" id="change" name="total" class="grand_total"
                                value="<?php echo $Total; ?>" readonly></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <br>

            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <button id="submitAll" type="submit" name="submit"
                style="padding: 10px; width: 100%; box-sizing: border-box;">Submit</button>
        </form>
    </div>

    <script>
        function showUser(str) {
            if (str == "") return;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var parts = this.responseText.split("|");
                    var unitValue = parts[0];
                    var rateValue = parts[1];

                    document.getElementById("rateHint").value = rateValue;
                    var selectElement = document.getElementById("Unit");
                    if (selectElement) {
                        // Check if the unitValue exists as an option
                        var optionExists = false;
                        for (var i = 0; i < selectElement.options.length; i++) {
                            if (selectElement.options[i].value === unitValue) {
                                optionExists = true;
                                break;
                            }
                        }
                        // If not, add it
                        if (!optionExists && unitValue) {
                            var opt = document.createElement("option");
                            opt.value = unitValue;
                            opt.text = unitValue;
                            opt.selected = true;
                            selectElement.appendChild(opt);
                        } else {
                            selectElement.value = unitValue;
                        }
                    }
                    Add();
                }
            };
            xmlhttp.open("GET", "value.php?q=" + str, true);
            xmlhttp.send();
        }

        function Add() {
            var qty = parseFloat(document.getElementById("Quantity").value) || 0;
            var rate = parseFloat(document.getElementById("rateHint").value) || 0;
            document.getElementById("Amount").value = (qty * rate).toFixed(2);
        }

        $(document).ready(function () {
            calculateTotal();
        });

        $('#addItem').on('click', function () {
            var product = "";
            var unit = "";
            var quantity = "";
            var rate = "";
            var amount = "";
            var count = 0;

            if ($('select[name="product"]').length > 0) {
                product = $('select[name="product"] option:selected').text().trim();
            }

            if ($('select[name="unit"]').length > 0) {
                unit = $('select[name="unit"]').val();
            }

            if ($('input[type="number"][name="quantity"]').length > 0) {
                quantity = $('input[type="number"][name="quantity"]').val();
            }

            if ($('input[type="text"][name="rate"]').length > 0) {
                rate = $('input[type="text"][name="rate"]').val();
            }

            if ($('input[type="text"][name="amount"]').length > 0) {
                amount = $('input[type="text"][name="amount"]').val();
            }

            if (!product || product === 'Select' || !unit || !quantity || !rate || !amount) {
                alert("Please select a product and fill all fields before adding to table");
                return;
            }

            var post_url = "table.php?selected_product=" + encodeURIComponent(product) +
                "&selected_unit=" + encodeURIComponent(unit) +
                "&selected_qty=" + encodeURIComponent(quantity) +
                "&selected_rate=" + encodeURIComponent(rate) +
                "&selected_amount=" + encodeURIComponent(amount);

            $.ajax({
                url: post_url, type: 'GET', cache: false,
                success: function (result) {
                    $('#purchaseTableBody').append(result);
                    renumberRows();
                    calculateTotal();
                    clearInputs();
                    $('.table-error').text('');
                },
                error: function (xhr, status, error) {
                    console.error(status, error);
                }
            });
        });

        $(document).on('click', '.deleteItem', function () {
            $(this).closest('tr').remove();
            renumberRows();
            calculateTotal();
        });

        function renumberRows() {
            $('#purchaseTableBody tr').each(function (index) {
                $(this).find('.row-id').text(index + 1);
            });
        }

        function calculateTotal() {
            let total = 0;
            $('#purchaseTableBody tr').each(function () {
                let amt = $(this).find('.row-amount').val();
                total += parseFloat(amt) || 0;
            });
            $('.grand_total').val(total.toFixed(2));
        }

        function clearInputs() {
            $('#Product').val('');
            $('#Unit').val('');
            $('#Quantity').val('');
            $('#rateHint').val('');
            $('#Amount').val('');
        }

        $(document).on('input', '.edit-qty, .edit-rate', function () {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.edit-qty').val()) || 0;
            let rate = parseFloat(row.find('.edit-rate').val()) || 0;
            let amount = (qty * rate).toFixed(2);
            row.find('.row-amount').val(amount);
            row.find('input[name="quantityArray[]"]').val(qty);
            row.find('input[name="rateArray[]"]').val(rate);
            row.find('input[name="amountArray[]"]').val(amount);
            calculateTotal();
        });
    </script>
</body>

</html>
