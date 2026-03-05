<?php
include "../config.php";

$edit_id = "";
$Edit = 0;

$DateErr = $PartyErr = $BillnoErr = $ProductErr = $UnitErr = $QuantityErr = $RateErr = $AmountErr = $tableError = $qty_error = $rate_error = "";
$Date = $Party = $Billno = $Product = $Unit = $Quantity = $Rate = $Amount = $Total = "";

$stock = $stockErr = "";

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

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $Date = $data['date'];
        $Billno = $data['bill_no'];
        $Party = $data['party_type'];
        $Product = $data['product'];
        $Unit = $data['unit'];
        $Quantity = $data['quantity'];
        $Rate = $data['rate'];
        $Amount = $data['amount'];
        $Total = $data['total'];
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
                    "SELECT id FROM purchase WHERE bill_no = '$Billno' AND id != '$edit_id'"
                );
            } else {
                $checkBill = mysqli_query(
                    $conn,
                    "SELECT id FROM purchase WHERE bill_no = '$Billno'"
                );
            }

            if (mysqli_num_rows($checkBill) > 0) {
                $BillnoErr = "Bill number already exists";
                $error = 1;
            }
        }
    }

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
        } else if ($_POST['quantity'] <= 0) {
            $qty_error = "Quantity must be greater than 0";
            $error = 1;
        }

        if (empty($_POST['rate'])) {
            $RateErr = "Rate is required";
            $error = 1;
        } else if ($_POST['rate'] <= 0) {
            $rate_error = "Rate must be greater than 0";
            $error = 1;
        }
    }

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

    $topUnit = $_POST['unit'] ?? '';
    $topQuantity = $_POST['quantity'] ?? '';
    $topRate = $_POST['rate'] ?? '';
    $topAmount = $_POST['amount'] ?? '';

    if ($error === 0 && $hasRows) {
        $rowError = false;
        for ($i = 0; $i < count($_POST['quantityArray']); $i++) {
            $qty = floatval($_POST['quantityArray'][$i]);
            $rate = floatval($_POST['rateArray'][$i]);

            if ($qty <= 0) {
                $rowError = true;
                $qty_error = "All quantities must be greater than 0";
                $error = 1;
                break;
            }
            if ($rate <= 0) {
                $rowError = true;
                $rate_error = "All rates must be greater than 0";
                $error = 1;
                break;
            }
        }
    }

    if ($error === 0) {
        if ($Edit == 0) {
            $sql = "INSERT INTO Purchase 
            (`date`, bill_no, party_type, product, unit, quantity, rate, amount, total) 
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

            $outward = 0;
            $action = 'Plus';
            $type = 'Purchase';
            $remarks = 'Purchase bill: ' . $Billno;

            for ($i = 0; $i < count($_POST['productArray']); $i++) {
                $prodName = $_POST['productArray'][$i];
                $unitName = $_POST['unitArray'][$i];
                $qty = $_POST['quantityArray'][$i];

                $sql2 = "INSERT INTO stock (product_name, unit_name, inward_unit, outward_unit, stock_action, stock_type, remarks,purchase_id) VALUES (?,?,?,?,?,?,?,?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ssiisssi", $prodName, $unitName, $qty, $outward, $action, $type, $remarks, $edit_id);
                $stmt2->execute();
                $stmt2->close();
            }

        } else {
            $sql = "UPDATE Purchase SET `date`=?, bill_no=?, party_type=?, product=?, unit=?, quantity=?, rate=?, amount=?, total=? WHERE id=?";

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

            $delete_sql = "DELETE FROM stock WHERE remarks = ? AND stock_type = 'Purchase'";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $edit_id);
            $delete_stmt->execute();
            $delete_stmt->close();

            $outward = 0;
            $action = 'Plus';
            $type = 'Purchase';
            $remarks_new = 'Purchase bill: ' . $Billno;

            for ($i = 0; $i < count($_POST['productArray']); $i++) {
                $prodName = $_POST['productArray'][$i];
                $unitName = $_POST['unitArray'][$i];
                $qty = $_POST['quantityArray'][$i];

                $insert_sql = "INSERT INTO stock (product_name, unit_name, inward_unit, outward_unit, stock_action, stock_type, remarks, purchase_id) VALUES (?,?,?,?,?,?,?,?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ssiisssi", $prodName, $unitName, $qty, $outward, $action, $type, $remarks_new, $edit_id);
                $insert_stmt->execute();
                $insert_stmt->close();
            }

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
    <link rel="stylesheet" href="Stylesheet/purchase_stylesheet.css">
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
            <li><a href="../Sales/sales_page.php">Sales</a></li>
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
                        $partySQL = "SELECT `name` FROM Party WHERE party_type IN ('Purchase', 'Both')";
                        $partyResult = mysqli_query($conn, $partySQL);
                        if (mysqli_num_rows($partyResult) > 0) {
                            while ($partyRow = mysqli_fetch_assoc($partyResult)) {
                                $selected = '';
                                $currentParty = isset($_POST['party']) ? $_POST['party'] : (isset($Party) ? $Party : '');
                                if (trim($currentParty) == trim($partyRow['name'])) {
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?php echo $partyRow['name']; ?>" <?php echo $selected; ?>>
                                    <?php echo $partyRow['name']; ?>
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
                        $prodSQL = "SELECT product_name, id FROM Product";
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
                                    <?php echo $prodRow['product_name']; ?>
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
                        $unitSQL = "SELECT unit_name FROM Product";
                        $unitResult = mysqli_query($conn, $unitSQL);
                        $unitOptions = array();
                        if (mysqli_num_rows($unitResult) > 0) {
                            while ($unitRow = mysqli_fetch_assoc($unitResult)) {
                                $unitOptions[] = $unitRow['unit_name'];
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
                    <span style="padding: 5px;"></span>
                    <!-- Amount field -->
                    <div class="field-group">
                        <label for="Amount">Amount</label><br>
                        <input type="text" id="Amount" name="amount" readonly
                            value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>">
                        <span class="error"><?php echo $AmountErr; ?></span>
                    </div>
                    <span style="padding: 5px;"></span>
                    <!-- + button -->
                    <button type="button" id="addItem">+</button>
                </div>

                <!-- Table error message (appears below the inline group) -->
                <div style="margin-top: 5px; text-align: center;">
                    <span class="table-error"><?php echo $tableError; ?></span>
                </div>
                <br>
            </div>

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
                                    <span class="error"><?php echo $qty_error; ?></span>
                                </td>
                                <td>
                                    <input type="number" class="edit-rate" value="<?php echo $_POST['rateArray'][$i]; ?>">
                                    <input type="hidden" name="rateArray[]" value="<?php echo $_POST['rateArray'][$i]; ?>">
                                    <span class="error"><?php echo $rate_error; ?></span>
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

    <footer>
        <p style="text-align: center;">
            Copyright:
            <?php echo date("Y"); ?>
        </p>
    </footer>

    <script src="script.js"></script>
</body>

</html>