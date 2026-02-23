<?php
include "../config.php";

$edit_id = "";
$Edit = 0;
$message = "";

$unitNameErr = "";
$unitName = "";

if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];
    $Edit = 1;

    $sql = "SELECT * FROM Unit WHERE id='$edit_id'";
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        foreach ($result as $data) {
            $unitName = $data['name'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hasError = false;
    $edit_id = $_POST['edit_id'] ?? 0;
    $Edit = ($edit_id > 0) ? 1 : 0;

    if (empty($_POST["name"])) {
        $unitNameErr = "Name is required";
        $hasError = true;
    } else {
        $unitName = test_input($_POST["name"]);                                              //required
        if (!preg_match("/^[A-Za-z-' ]*$/", $unitName)) {
            $unitNameErr = "Only letters and space are allowed";
            $hasError = true;
        } else {
            if ($Edit == 0) {
                // checking
                $sql = "SELECT id FROM Unit WHERE name = ?";
                $result = $conn->execute_query($sql, [$unitName]);

            } else {
                // ignore if edit
                $sql = "SELECT id FROM Unit WHERE name = ? AND id != ?";
                $result = $conn->execute_query($sql, [$unitName, $edit_id]);
            }

            if ($result->num_rows > 0) {
                $unitNameErr = "This name is already in use";
                $hasError = true;
            }
        }
    }

    if (!$hasError) {
        if ($Edit == 0) {
            $sql = "INSERT INTO Unit (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $unitName);

            $stmt->execute();
            header("Location: edit_unit.php?success=1");
            exit;
        } else {
            $sql = "UPDATE Unit  SET name=? WHERE id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $unitName, $edit_id);

            $stmt->execute();
            header("Location: edit_unit.php?edit_success=1");
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
    <title>Unit</title>
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

        form {
            padding: 50px 50px 50px 50px;
            margin: 5% 30% 5% 30%;
            border-radius: 12px;
            background-color: white;
            color: black;
        }

        input {
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .error {
            color: red;
        }

        input[type="submit"]:hover {
            background-color: #0a2463;
            color: white;
            width: 100%;
            box-sizing: border-box;
        }

        footer {
            background-color: #0a2463;
            color: white;
            padding: 30px;
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
        <h1>Unit</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="../Purchase/Purchase_page.php">Purchase</a></li>
            <li><a href="../Product/Product_table.php">Product</a></li>
            <li><a href="../Party/Party_Page.php">Party</a></li>
        </ul>
    </nav>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <button>
            <a href="edit_unit.php">
                &#8618; Database
            </a>
        </button>
        <h1>Fill the details:</h1>
        <label for="name"><b>Name: </b><span class="error">* <?php echo $unitNameErr; ?></span></label><br><br>
        <input type="text" name="name" placeholder="Enter the name...." value="<?php echo $unitName; ?>"><br><br>
        <button onclick="mySubmit()" type="submit" value="Submit" name="submit"
            style="padding: 10px; width: 100%; box-sizing: border-box;">Submit</button>
    </form>

    <footer>
        <p style="text-align: center;">
            Copyright:
            <?php echo date("Y"); ?>
        </p>
    </footer>
</body>

</html>