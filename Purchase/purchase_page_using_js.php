<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Unit_Product";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['save']) && $_POST['save'] == "1") {

    $date = $_POST['date'] ?? '';
    $billno = $_POST['billno'] ?? '';
    $party_type = $_POST['party'] ?? '';

    $productArray = $_POST['productArray'] ?? '';
    $unitArray = $_POST['unitArray'] ?? '';
    $quantityArray = $_POST['quantityArray'] ?? '';
    $rateArray = $_POST['rateArray'] ?? '';
    $amountArray = $_POST['amountArray'] ?? '';
    $total = $_POST['total'] ?? '';

    if (empty($date)) {
        echo "Date required";
        $error = 1;
        exit;
    }

    if (empty($party_type)) {
        echo "Party type required";
        $error = 1;
        exit;
    }

    if (empty($billno) || !preg_match("/^[0-9]*$/", $billno)) {
        echo "Valid bill number required";
        exit;
    }

    if (
        empty($productArray) ||
        empty($unitArray) ||
        empty($quantityArray) ||
        empty($rateArray) ||
        empty($amountArray)
    ) {
        echo "No product data";
        exit;
    }

    $check = $conn->prepare("SELECT id FROM Purchase WHERE BillNo = ?");
    $check->bind_param("i", $billno);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Bill number already exists";
        exit;
    }
    $check->close();



    $stmt = $conn->prepare(
        "INSERT INTO Purchase 
            (Date, BillNo, Party_Type, Product, Unit, Quantity, Rate, Amount, Total) 
            VALUES (?,?,?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "sissssssd",
        $date,
        $billno,
        $party_type,
        $productArray,
        $unitArray,
        $quantityArray,
        $rateArray,
        $amountArray,
        $total
    );

    if ($stmt->execute()) {
        echo "Saved successfully";
    } else {
        echo "Insert failed";
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html>

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

        input[type="text"] {
            width: 80px;
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
    </style>
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
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="purchaseForm">
            <div style="text-align: center;">

                <!------- Date ------->
                <label for="Date">Date</label>
                <span class="error"></span>
                <input type="date" id="Date" placeholder="Date..." name="date" value="">

                <!------- Party ------->
                <label style="padding-left: 15px;" for="Party">Party</label>
                <span class="error"></span>
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
                <span class="error"></span>
                <input type="text" name="billno" id="Billno" inputmode="numeric" pattern="[0-9]*" maxlength="4"
                    value=""><br><br>

                <!-------- Product ------->
                <label for="Product">Product</label>
                <span class="error"></span>
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
                <span class="error"></span>
                <select id="Unit" name="unit">
                </select>

                <!-------- Quantity ------->
                <label style="padding-left: 15px;" for="Quantity">Quantity</label>
                <span class="error"></span>
                <input type="number" id="Quantity" oninput="Add()" name="quantity" value="">

                <!-------- Rate ------->
                <label style="padding-left: 15px;" for="rateHint">Rate</label>
                <span class="error"></span>
                <input id="rateHint" type="text" name="rate" readonly>

                <!-------- Amount ------->
                <label style="padding-left: 15px;" for="Amount">Amount</label>
                <span class="error"></span>
                <input type="text" id="Amount" name="amount" value="" readonly>
                <span style="padding-left: 15px;"></span>

                <button style="width: 35px;margin-top: 5px;" type="button" onclick="addItem()">+</button>
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

                <tbody id="purchaseTableBody"></tbody>

                <tfoot>
                    <tr>
                        <td colspan="5" ,align="right"><strong>Total</strong></td>
                        <td id="grandTotal">0</td>
                        <input type="hidden" name="total" id="total">
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <br>

            <input type="hidden" name="productArray" id="productArray">
            <input type="hidden" name="unitArray" id="unitArray">
            <input type="hidden" name="quantityArray" id="quantityArray">
            <input type="hidden" name="rateArray" id="rateArray">
            <input type="hidden" name="amountArray" id="amountArray">
            <input type="hidden" name="total" id="total">
            <input type="hidden" name="save" value="1">

            <button id="Total" type="submit" value="Submit" name="submit"
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
    <!-----display the items------>
    <script>
        let items = [];

        function addItem() {

            const productSelect = document.getElementById("Product");
            const productName = productSelect.options[productSelect.selectedIndex].text;
            const unit = Unit.value;
            const quantity = Quantity.value;
            const rate = rateHint.value;
            const amount = Amount.value;

            if (!productName || !unit || !quantity || !rate || !amount) {
                alert("Fill all fields");
                return;
            }

            items.push({ product: productName, unit, quantity, rate, amount });
            renderTable();
            clearForm();
        }

        function renderTable() {
            let tbody = document.getElementById("purchaseTableBody");
            tbody.innerHTML = "";

            let total = 0;

            items.forEach((item, index) => {
                total += parseFloat(item.amount);

                tbody.innerHTML += `
            <tr>
                <td><input type="text" name="id" value="${index + 1}"></td>
                <td><input type="text" name="product" value="${item.product}"></td>
                <td><input type="text" name="unit" value="${item.unit}"></td>
                <td>${item.quantity}<input type="text" name="quantity" ></td>
                <td>${item.rate}</td>
                <td>${item.amount}</td>
                <td id="no-hover">
                    <button onclick="deleteItem(${index})">Delete</button>
                </td>
            </tr>`;
            });

            document.getElementById("grandTotal").innerText = total.toFixed(2);
        }

        function deleteItem(index) {
            items.splice(index, 1);
            renderTable();
        }

        function clearForm() {
            Product.value = "";
            Unit.value = "";
            Quantity.value = "";
            rateHint.value = "";
            Amount.value = "";
        }

        document.getElementById("purchaseForm").addEventListener("submit", function (e) {
            e.preventDefault();

            if (items.length === 0) {
                alert("Please add at least one product");
                return;
            }

            let productArray = [];
            let unitArray = [];
            let quantityArray = [];
            let rateArray = [];
            let amountArray = [];

            items.forEach(item => {
                productArray.push(item.product);
                unitArray.push(item.unit);
                quantityArray.push(item.quantity);
                rateArray.push(item.rate);
                amountArray.push(item.amount);
            });

            document.getElementById("productArray").value = productArray.join(",");
            document.getElementById("unitArray").value = unitArray.join(",");
            document.getElementById("quantityArray").value = quantityArray.join(",");
            document.getElementById("rateArray").value = rateArray.join(",");
            document.getElementById("amountArray").value = amountArray.join(",");
            document.getElementById("total").value =
                document.getElementById("grandTotal").innerText;

            this.submit();
        });

    </script>


</body>

</html>