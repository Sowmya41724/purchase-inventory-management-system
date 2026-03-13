<?php
include "../config.php";

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
    <link rel="stylesheet" href="Stylesheet/purchase_stylesheet.css">
    <script src="js/jquery.min.js"></script>
</head>

<body>
    <header>
        <h1>Edit Purchase</h1>
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
                            <?php echo $rows['date']; ?>
                        </td>
                        <td>
                            <?php echo $rows['party_type']; ?>
                        </td>
                        <td>
                            <?php echo $rows['bill_no']; ?>
                        </td>
                        <td>
                            <?php echo $rows['product']; ?>
                        </td>
                        <td>
                            <?php echo $rows['unit']; ?>
                        </td>
                        <td>
                            <?php echo $rows['total']; ?>
                        </td>
                        <td id="no-hover">
                            <button onclick="location.href='Purchase_page.php?edit_id=<?php echo $rows['id']; ?>'" name="edit"
                                type="submit" style="padding: 10px 15px 10px 15px;">
                                &#x1F589;Edit
                            </button>
                            <br>
                            <button
                                onclick="location.href='edit_purchase.php?delete_id=<?php echo $rows['id']; ?>'; return confirm('Are you sure you want to delete this record?');"
                                name="delete" type="submit">
                                &#x1F5D1;Delete
                            </button>
                            <br>
                            <button
                                onclick="window.open('../reports/purchase_pdf.php?edit_id=<?php echo $rows['id']; ?>', '_blank')"
                                name="print" type="submit" style="padding: 10px 13px 10px 13px;">
                                <i class="fa fa-print"> Print</i>
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