<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Unit_Product";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connect failed");
}

$sql = "SELECT * FROM Purchase";
$result = mysqli_query($conn, $sql);

$delete_id = "";

if (isset($_REQUEST['delete_id'])) {
    $delete_id = $_REQUEST['delete_id'];

    $mySQL = "DELETE FROM Purchase WHERE id='$delete_id'";
    $a_result = mysqli_query($conn, $mySQL);
    if (!empty($a_result)) {
        header("Location: edit_purchase.php?delete_success=1");
        exit;
    } else {
        echo "error in deleteing";
    }

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Purchase</title>
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

        .error {
            color: red;
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
    <script src="js/jquery.min.js"></script>
</head>

<body>
    <header>
        <h1>Edit Purchase</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="Unit_table.php">Unit</a></li>
            <li><a href="Product_table.php">Product</a></li>
            <li><a href="Party_Page.php">Party</a></li>
        </ul>
    </nav>

    <div class="input-container">
        <button onclick="location.href='Purchase_page.php'" type="button">
            &#8617; Back to purchase
        </button>
        <table>
            <tr>
                <th>S.no</th>
                <th>Date</th>
                <th>Party</th>
                <th>Billno</th>
                <th>Product</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $m = 1;
                $row_count = mysqli_num_rows($result);
                while ($rows = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $m; ?>
                        </td>
                        <td>
                            <?php echo $rows['Date']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Party_Type']; ?>
                        </td>
                        <td>
                            <?php echo $rows['BillNo']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Product']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Unit']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Quantity']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Rate']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Amount']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Total']; ?>
                        </td>
                        <td id="no-hover">
                            <button onclick="location.href='Purchase_page.php?edit_id=<?php echo $rows['id']; ?>'" name="edit"
                                type="submit">
                                &#x1F589;Edit
                            </button>
                            <br>
                            <button
                                onclick="location.href='edit_purchase.php?delete_id=<?php echo $rows['id']; ?>'; return confirm('Are you sure you want to delete this record?');"
                                name="delete" type="submit">
                                &#x1F5D1;Delete
                            </button>
                        </td>
                    </tr>
                    <?php
                    $m++;
                }
            } else {
                echo "<tr><td colspan = '50'>No Record Found</td></tr>";
            }
            ?>
        </table>
    </div>

</body>

</html>