<?php
include "../config.php";

$sql = "SELECT * FROM Product";
$result = mysqli_query($conn, $sql);

$delete_id = "";

if (isset($_REQUEST['delete_id'])) {
    $delete_id = $_REQUEST['delete_id'];

    $mySQL = "DELETE FROM Product WHERE id='$delete_id'";
    $a_result = mysqli_query($conn, $mySQL);
    if (!empty($a_result)) {
        header("Location: edit_product.php?delete_success=1");
        exit;
    } else {
        echo "error in deleteing";
    }

}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
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
            background-color: white;
            padding: 50px 50px 50px 50px;
            width: auto;
            height: auto;
            margin: 5%;
            border-radius: 10px
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

        button {
            margin-bottom: 10px;
            background-color: #DDD;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            text-decoration: underline;
            background-color: #0a2463;
            color: white;
        }

        a {
            text-decoration: none;
            color: black;
        }

        a:hover {
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <h1>Edit Product Table</h1>
    </header>
    <div class="input-container">
        <button>
            <a href="Product_table.php">
                &#8617; Back to form
            </a>
        </button>
        <table>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Unit Value</th>
                <th>Rate</th>
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
                        <td><?php echo $rows['Name']; ?></td>
                        <td><?php echo $rows['unitValue']; ?></td>
                        <td><?php echo $rows['Rate']; ?></td>
                        <td id="no-hover">
                            <button name="edit" type="submit">
                                <a href="Product_table.php?edit_id=<?php echo $rows['id']; ?>">
                                    &#x1F589;Edit
                                </a>
                            </button>
                            <br>
                            <button name="delete" type="submit">
                                <a href="edit_product.php?delete_id=<?php echo $rows['id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this record?');">
                                    &#x1F5D1;Delete
                                </a>
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