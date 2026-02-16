<?php
include "../config.php";

$edit_id = "";
$Edit = 0;

$DateErr = $PartyErr = $BillnoErr = $ProductErr = $UnitErr = $QuantityErr = $RateErr = $AmountErr = $buttonError = "";
$Date = $Party = $Billno = $Product = $Unit = $Quantity = $Rate = $Amount = $Total = "";

if (isset($_REQUEST['edit_id'])) {

    $edit_id = $_REQUEST['edit_id'];
    $Edit = 1;

    $sql = "SELECT * FROM Purchase WHERE id='$edit_id'";
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        foreach ($result as $data) {
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

    if (empty($_POST['date'])) {
        $DateErr = "Date is Required";
        $error = 1;
    } else {
        $Date = test_input($_POST['date']);
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $Date)) {
            $DateErr = "Only numbers allowed";
            $error = 1;
        }
    }

    if (empty($_POST['party'])) {
        $PartyErr = "Party is Required";
        $error = 1;
    } else {
        $Party = test_input($_POST['party']);
    }

    if (empty($_POST['billno'])) {
        $BillnoErr = "Billno is Required";
        $error = 1;
    } else {
        $Billno = test_input($_POST['billno']);
        if (!preg_match("/^[0-9]*$/", $Billno)) {
            $BillnoErr = "Only numbers are allow";
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

        if (empty($_POST['amount'])) {
            $AmountErr = "Amount is required";
            $error = 1;
        }
    }

    if (!empty($_POST['total'])) {
        $Total = test_input($_POST['total']);
    }

    if (isset($_POST['addItem']) && !$hasRows) {
        if (!isset($_POST['productArray']) || count($_POST['productArray']) === 0) {
            $buttonError = "Please click + button to add item";
            $error = 1;
        }
    }

    if (isset($_POST['productArray'])) {
        $Product = implode(',', $_POST['productArray']);
        $Unit = implode(',', $_POST['unitArray']);
        $Quantity = implode(',', $_POST['quantityArray']);
        $Rate = implode(',', $_POST['rateArray']);
        $Amount = implode(',', $_POST['amountArray']);
    }


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

                <!------- Date ------->
                <label for="Date">Date</label>
                <span class="error">
                    <?php echo $DateErr; ?>
                </span>
                <input type="date" id="Date" placeholder="Date..." name="date"
                    value="<?php echo $_POST['date'] ?? $Date; ?>">

                <!------- Party ------->
                <label style="padding-left: 15px;" for="Party">Party</label>
                <span class="error">
                    <?php echo $PartyErr; ?>
                </span>
                <select id="Party" name="party">
                    <option value="">Select
                    </option>
                    <?php
                    $mySQL = "SELECT Party_Type FROM Party";
                    $a_result = mysqli_query($conn, $mySQL);
                    if (mysqli_num_rows($a_result)) {
                        $row_count = mysqli_num_rows($a_result);
                        while ($rows = mysqli_fetch_assoc($a_result)) {
                            ?>
                    <option value="
                        <?php echo $rows['Party_Type']; ?>">
                        <?php echo $rows['Party_Type']; ?>
                    </option>
                    <?php
                        }
                    } else {
                        echo "No Record Found";
                    }
                    ?>
                </select>


                <!-------- Bill no ------->
                <label style="padding-left: 15px;" for="Billno">Bill no</label>
                <span class="error">
                    <?php echo $BillnoErr; ?>
                </span>
                <input type="text" name="billno" id="Billno" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                    value="<?php echo $_POST['billno'] ?? $Billno; ?>">
                <br><br>

                <?php
                $Product_arr = explode(",", $Product);
                $Unit_arr = explode(",", $Unit);
                $Quantity_arr = explode(",", $Quantity);
                $Rate_arr = explode(",", $Rate);
                $Amount_arr = explode(",", $Amount);
                ?>
                <!-------- Product ------->
                <label for="Product">Product</label>
                <span class="error">
                    <?php echo $ProductErr; ?>
                </span>
                <select id="Product" name="product" onchange="showUser(this.value)">
                    <option value="">Select
                    </option>
                    <?php
                    $mySQL = "SELECT `Name`, id FROM Product";
                    $a_result = mysqli_query($conn, $mySQL);
                    if (mysqli_num_rows($a_result)) {
                        $row_count = mysqli_num_rows($a_result);
                        while ($rows = mysqli_fetch_assoc($a_result)) {
                            ?>
                    <option value="<?php echo $rows['id']; ?>">
                        <?php echo $rows['Name']; ?>
                    </option>
                    <?php
                    $m++;
                        }
                    } else {
                        echo "No Record Found";
                    }
                    ?>
                </select>

                <!-------- Unit ------->
                <label style="padding-left: 15px;" for="Unit">Unit</label>
                <span class="error">
                    <?php echo $UnitErr; ?>
                </span>
                <select id="Unit" name="unit">
                    <option value="">Select </option>

                    <?php foreach ($unitList as $u): ?>
                    <option value="<?php echo $row['id']; ?>" <?php
                       if (
                           !$Edit &&
                           isset($_POST['unit']) &&
                           $_POST['unit'] == $row['id']
                       )
                           echo 'selected';
                       ?>
                    >
                        <?php echo $row['Unit']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <!-------- Quantity ------->
                <label style="padding-left: 15px;" for="Quantity">Quantity</label>
                <span class="error">
                    <?php echo $QuantityErr; ?>
                </span>
                <input type="number" id="Quantity" name="quantity" oninput="Add()"
                    value="<?php echo $_POST['quantity'] ?? ''; ?>">

                <!-------- Rate ------->
                <label style="padding-left: 15px;" for="rateHint">Rate</label>
                <span class="error">
                    <?php echo $RateErr; ?>
                </span>
                <input type="text" id="rateHint" name="rate" value="<?php echo $_POST['rate'] ?? ''; ?>" readonly>

                <!-------- Amount ------->
                <label style="padding-left: 15px;" for="Amount">Amount</label>
                <span class="error">
                    <?php echo $AmountErr; ?>
                </span>
                <input type="text" id="Amount" name="amount" value="<?php echo $_POST['amount'] ?? ''; ?>" readonly>
                <span style="padding-left: 15px;"></span>

                <button style="width: 35px;margin-top: 5px;" type="button" id="addItem">+</button>
                <span class="error">
                    <?php echo $buttonError; ?>
                </span>
                <br>
            </div>

            <br><br>
            <table>
                <thread>
                    <tr>
                        <th>S.no</th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thread>

                <tbody id="purchaseTableBody">
                    <?php
                    if (isset($_POST['productArray'])) {
                        for ($i = 0; $i < count($_POST['productArray']); $i++) {
                            ?>
                    <tr>
                        <td class="row-id">
                            <?php echo $i + 1; ?>
                        </td>

                        <td>
                            <?php echo $_POST['productArray'][$i]; ?>
                            <input type="hidden" name="productArray[]"
                                value="<?php echo $_POST['productArray'][$i]; ?>">
                        </td>

                        <td>
                            <?php echo $_POST['unitArray'][$i]; ?>
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


                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="5" ,align="right"><strong>Total</strong></td>
                        <td><input type="number" id="change" name="total" class="grand_total"
                                value="<?php echo $Total; ?>" readonly></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <br>

            <button id="submitAll" type="submit" id="submitItem" value="Submit" name="submit"
                style="padding: 10px; width: 100%; box-sizing: border-box;">Submit</button>
        </form>
    </div>

    <!-----copyright----->
    <script>
        const date = document.getElementById("")
    </script>
    <!-----product------->
    <script>
        function showUser(str) {
            if (str == "") {
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        var parts = this.responseText.split("|");
                        var textValue = parts[0];

                        document.getElementById("rateHint").value = parts[1];

                        var selectElement = document.getElementById("Unit");

                        if (selectElement) {
                            var opt = document.createElement("option");
                            opt.value = textValue;
                            opt.textContent = textValue;
                            selectElement.appendChild(opt);

                            selectElement.value = textValue;
                        } else {
                            console.error("Select element not found! Check your ID.");
                        }
                    }
                };
                xmlhttp.open("GET", "value.php?q=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
    <!-----amount-------->
    <script>
        function Add() {
            var qtyInput = document.getElementById("Quantity");
            var rateInput = document.getElementById("rateHint");
            var amountElement = document.getElementById("Amount");

            var qty = parseFloat(qtyInput.value) || 0;
            var rate = parseFloat(rateInput.value) || 0;

            var total = qty * rate;

            amountElement.value = total.toFixed(2);
        }


    </script>
    <!-----AJAX value------->
    <script>
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

            var post_url = "table.php?selected_product=" + encodeURIComponent(product) + "&selected_unit=" + encodeURIComponent(unit) + "&selected_qty=" + encodeURIComponent(quantity) + "&selected_rate=" + encodeURIComponent(rate) + "&selected_amount=" + encodeURIComponent(amount);

            $.ajax({
                url: post_url, type: 'GET', cache: false, success: function (result) {
                    if ($('#purchaseTableBody').length > 0) {
                        $('#purchaseTableBody').append(result);
                        count += $('#purchaseTableBody').length;
                        console.log(count);
                        renumberRows();
                        calculateTotal();
                        clearInputs();
                    }
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
            $('#Unit').empty();
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