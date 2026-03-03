<?php
include "../config.php";

$edit_id = "";
$Edit = 0;
$message = "";

$ProductName = $Unit_Value = $Rate = "";
$ProductNameErr = $Unit_ValueErr = $RateErr = "";

$stock = $inward = $inwardErr = $action = $type = $remarks = "";
$outward = 0;

if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];
    $Edit = 1;

    $sql = "SELECT * FROM Product WHERE id='$edit_id'";
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        foreach ($result as $data) {
            $ProductName = $data['product_name'];
            $Unit_Value = $data['unit_name'];
            $Rate = $data['rate'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $error = 0;
    $edit_id = $_POST['edit_id'] ?? 0;
    $Edit = ($edit_id > 0) ? 1 : 0;

    if (empty($_POST["name"])) {
        $ProductNameErr = "Name is Required";
        $error = 1;
    } else {
        $ProductName = test_input($_POST["name"]);
        $stock = 1;
        if (!preg_match("/^[A-Za-z-' ]*$/", $ProductName)) {
            $ProductNameErr = "Only letters and space are allowed";
            $error = 1;
        } else {
            if ($Edit == 0) {
                // checking
                $sql = "SELECT id FROM Product WHERE product_name = ?";
                $result = $conn->execute_query($sql, [$ProductName]);

            } else {
                // ignore if edit
                $sql = "SELECT id FROM Product WHERE product_name = ? AND id != ?";
                $result = $conn->execute_query($sql, [$ProductName, $edit_id]);
            }

            if ($result->num_rows > 0) {
                $ProductNameErr = "This name is already in use";
                $error = 1;
            }
        }
    }

    if (empty($_POST["unit"])) {
        $Unit_ValueErr = "Select an option";
        $error = 1;
    } else {
        $Unit_Value = test_input($_POST["unit"]);
    }

    if (empty($_POST["rate"])) {
        $RateErr = "Rate is Required";
        $error = 1;
    } else {
        $Rate = test_input($_POST["rate"]);
        if (!preg_match("/^[0-9]*$/", $Rate)) {
            $RateErr = "Only numbers are allowed";
            $error = 1;
        } else {
            if ($Edit == 0) {
                // ignore if edit
                $sql = "SELECT id FROM Product WHERE rate = ? AND id != ?";
                $result = $conn->execute_query($sql, [$Rate, $edit_id]);
            }
        }
    }

    if (empty($_POST["stock"])) {
        $inwardErr = "Stock is Required";
        $error = 1;
    } else {
        $inward = test_input($_POST["stock"]);
        if (!preg_match("/^[0-9]*$/", $inward)) {
            $inwardErr = "Only numbers are allowed";
            $error = 1;
        } else {
            if ($Edit == 0) {
                $sql = "SELECT id FROM stock WHERE inward_unit = ? AND id != ?";
                $result = $conn->execute_query($sql, [$stock, $edit_id]);
            }
        }
    }

    if ($error === 0) {
        if ($Edit == 0) {
            $sql = "INSERT INTO Product (product_name, unit_name, rate) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $ProductName, $Unit_Value, $Rate);

            $stmt->execute();
            header("Location: edit_product.php?success=1");
            exit;

            if ($stock == 1) {
                $sql = "INSERT INTO stock (product_name, unit_name, inward_unit, outward_unit, stock_action, stock_type, remarks) VALUES (?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssis", $ProductName, $Unit_Value, $inward, $outward, $action, $type, $remarks);

                $stmt->execute();
                exit;
            }

        } else {
            $sql = "UPDATE Product  SET product_name=?, unit_name=?, rate=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $ProductName, $Unit_Value, $Rate, $edit_id);

            $stmt->execute();
            header("Location: edit_product.php?edit_success=1");
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
<html>

<head>
    <title>Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon"
        href="https://img.freepik.com/premium-vector/uoe-logo-design-initial-letter-uoe-monogram-logo-using-hexagon-shape_1101554-59452.jpg?semt=ais_hybrid&w=740&q=80">
    <link rel="stylesheet" href="Stylesheet/product_styles.css">
</head>

<body>
    <header>
        <h1>Product</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="../Unit/Unit_table.php">Unit</a></li>
            <li><a href="../Purchase/Purchase_page.php">Purchase</a></li>
            <li><a href="../Party/Party_Page.php">Party</a></li>
            <li><a href="../Sales/sales_page.php">Sales</a></li>
        </ul>
    </nav>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <button onclick="location.href='edit_product.php'" Type="button">
            &#8618; Database
        </button>
        <h1>Fill the details:</h1>
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <label for="name">
            <b>
                Product Name :
            </b>
            <span class="error">
                * <?php echo $ProductNameErr; ?>
            </span>
        </label>
        <br><br>
        <input type="text" name="name" placeholder="Enter the name...." value="<?php echo $ProductName; ?>">
        <br><br>
        <label for="unit">
            <b>
                Unit :
            </b>
            <span class="error">
                * <?php echo $Unit_ValueErr; ?>
            </span>
        </label>
        <br><br>
        <select id="unit" name="unit">
            <option <?php if (isset($Unit_Value) && $Unit_Value == "") { ?> selected <?php } ?> value="">Select</option>
            <?php
            $mySQL = "SELECT unit_name FROM Unit";
            $a_result = mysqli_query($conn, $mySQL);
            if (mysqli_num_rows($a_result) > 0) {
                $row_count = mysqli_num_rows($a_result);
                while ($rows = mysqli_fetch_assoc($a_result)) {
                    ?>
                    <option <?php if (isset($Unit_Value) && $Unit_Value == $rows["unit_name"]) { ?> selected <?php } ?>
                        value="<?php echo $rows['unit_name']; ?>">
                        <?php echo $rows['unit_name']; ?>
                    </option>
                    <?php
                }
            } else {
                echo "No Record Found";
            }
            ?>
        </select>
        <br><br>
        <label for="rate">
            <b>
                Rate :
            </b>
            <span class="error">
                * <?php echo $RateErr; ?>
            </span>
        </label>
        <br><br>
        <input type="text" name="rate" placeholder="Enter your rate..." value="<?php echo $Rate; ?>">
        <br><br>
        <label for="inward">
            <b>
                Product Stock :
            </b>
            <span class="error">
                * <?php echo $inwardErr; ?>
            </span>
        </label>
        <br><br>
        <input type="numeric" name="stock" placeholder="Enter the stock no..." value="<?php echo $inward; ?>">
        <br><br><br>
        <button id="mySubmit" type="submit" value="Submit" name="submit"
            style="padding: 10px; width: 100%; box-sizing: border-box;">Submit</button>
    </form>

    <footer>
        <p style="text-align: center;">
            Copyright:
            <?php echo date("Y"); ?>
        </p>
    </footer>
    <script>
        $("#mySubmit").on("click", function () {
            var action = "";
            var type = "";

            if (<?php echo $ProductName; ?>)
        });
    </script>
</body>

</html>