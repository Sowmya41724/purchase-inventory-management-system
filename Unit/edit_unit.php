<?php

include "../config.php";

$sql = "SELECT * FROM Unit";
$result = mysqli_query($conn, $sql);

$delete_id = "";

if (isset($_REQUEST['delete_id'])) {
    $delete_id = $_REQUEST['delete_id'];

    $mySQL = "DELETE FROM Unit WHERE id='$delete_id'";
    $a_result = mysqli_query($conn, $mySQL);
    if (!empty($a_result)) {
        header("Location: edit_unit.php?delete_success=1");
        exit;
    } else {
        echo "error in deleteing";
    }

}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Unit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon"
        href="https://img.freepik.com/premium-vector/uoe-logo-design-initial-letter-uoe-monogram-logo-using-hexagon-shape_1101554-59452.jpg?semt=ais_hybrid&w=740&q=80">
    <link rel="stylesheet" href="Stylesheet/unit_styles.css">
</head>

<body>
    <header>
        <h1>Edit Unit Table</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="../Purchase/Purchase_page.php">Purchase</a></li>
            <li><a href="../Product/Product_table.php">Product</a></li>
            <li><a href="../Party/Party_Page.php">Party</a></li>
            <li><a href="../Sales/sales_page.php">Sales</a></li>
        </ul>
    </nav>

    <div class="input-container">
        <button onclick="location.href='Unit_table.php'" Type="button">&#8617; Back to form</button>
        <table>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $m = 1;
                $row_count = mysqli_num_rows($result);
                while ($rows = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $m; ?></td>
                        <td><?php echo $rows['unit_name']; ?></td>
                        <td id="no-hover">
                            <button onclick="location.href='Unit_table.php?edit_id=<?php echo $rows['id']; ?>'" name="edit"
                                type="submit">
                                &#x1F589;Edit
                            </button>
                            <br>
                            <button
                                onclick="location.href='edit_unit.php?delete_id=<?php echo $rows['id']; ?>'; return confirm('Are you sure you want to delete this record?');"
                                name="delete" type="submit">
                                &#x1F5D1;Delete
                            </button>
                        </td>
                    </tr>
                    <?php
                    $m++;
                }
            } else {
                echo "<tr><td colspan = '11'>No Record Found</td></tr>";
            }
            ?>

        </table>
    </div>

</body>

</html>