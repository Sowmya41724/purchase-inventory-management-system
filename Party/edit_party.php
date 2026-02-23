<?php
include "../config.php";

$sql = "SELECT * FROM Party";
$result = mysqli_query($conn, $sql);

$delete_id = "";

if (isset($_REQUEST['delete_id'])) {
    $delete_id = $_REQUEST['delete_id'];

    $mySQL = "DELETE FROM Party WHERE id='$delete_id'";
    $a_result = mysqli_query($conn, $mySQL);
    if (!empty($a_result)) {
        header("Location: edit_party.php?delete_success=1");
        exit;
    } else {
        echo "error in deleteing";
    }

}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Party</title>
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
        <h1>Edit Party Table</h1>
    </header>
    <div class="input-container">
        <button onclick="location.href='Party_Page.php'" type="button">&#8617; Back to form</button>
        <table>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Mobile No</th>
                <th>Address</th>
                <th>City</th>
                <th>Pincode</th>
                <th>Email</th>
                <th>Party Type</th>
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
                            <?php echo $rows['Name']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Mobileno']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Address']; ?>
                        </td>
                        <td>
                            <?php echo $rows['City']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Pincode']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Email']; ?>
                        </td>
                        <td>
                            <?php echo $rows['Party_Type']; ?>
                        </td>
                        <td id="no-hover">
                            <button onclick="location.href='Party_Page.php?edit_id=<?php echo $rows['id']; ?>'" name="edit"
                                type="submit">
                                &#x1F589;Edit
                            </button>
                            <br>
                            <button
                                onclick="location.href='edit_party.php?delete_id=<?php echo $rows['id']; ?>'; return confirm('Are you sure you want to delete this record?');"
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