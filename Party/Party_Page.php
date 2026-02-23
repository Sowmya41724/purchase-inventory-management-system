<?php
include "../config.php";

$edit_id = "";
$Edit = 0;
$message = "";

$Name = $Mobileno = $Address = $City = $Pincode = $Email = $Party_type = "";
$NameErr = $MobilenoErr = $CityErr = $EmailErr = $Party_typeErr = "";

if (isset($_REQUEST['edit_id'])) {
    $edit_id = $_REQUEST['edit_id'];
    $Edit = 1;

    $sql = "SELECT * FROM Party WHERE id='$edit_id'";
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        foreach ($result as $data) {
            $Name = $data['Name'];
            $Mobileno = $data['Mobileno'];
            $Address = $data['Address'];
            $City = $data['City'];
            $Pincode = $data['Pincode'];
            $Email = $data['Email'];
            $Party_type = $data['Party_Type'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $error = 0;
    $edit_id = $_POST['edit_id'] ?? 0;
    $Edit = ($edit_id > 0) ? 1 : 0;

    if (empty($_POST["name"])) {
        $NameErr = "Name is Required";
        $error = 1;
    } else {
        $Name = test_input($_POST["name"]);
        if (!preg_match("/^[A-Za-z-' ]*$/", $Name)) {
            $NameErr = "Only letters and space are allowed";
            $error = 1;
        } else {
            if ($Edit == 0) {
                // checking
                $sql = "SELECT id FROM Party WHERE Name = ?";
                $result = $conn->execute_query($sql, [$Name]);

            } else {
                // ignore if edit
                $sql = "SELECT id FROM Party WHERE Name = ? AND id != ?";
                $result = $conn->execute_query($sql, [$Name, $edit_id]);
            }

            if ($result->num_rows > 0) {
                $NameErr = "This name is already in use";
                $error = 1;
            }
        }
    }

    if (empty($_POST["mobileno"])) {
        $MobilenoErr = "Mobile number is required";
        $error = 1;
    } else {
        $Mobileno = test_input($_POST["mobileno"]);

        if (!preg_match("/^(91)[6789]\d{9}$/", $Mobileno)) {
            $MobilenoErr = "Invaid format";
            $error = 1;
        } else {
            if ($Edit == 0) {
                $sql = "SELECT id FROM Party WHERE Mobileno = ?";
                $result = $conn->execute_query($sql, [$Mobileno]);
            } else {
                $sql = "SELECT id FROM Party WHERE Mobileno = ? AND id != ?";
                $result = $conn->execute_query($sql, [$Mobileno, $edit_id]);
            }

            if ($result->num_rows > 0) {
                $MobilenoErr = "This Mobile Number is already in use";
                $error = 1;
            }
        }
    }

    if (empty($_POST["address"])) {
        $Address = "";
    } else {
        $Address = test_input($_POST["address"]);
        if ($Edit == 0) {
            // ignore if edit
            $sql = "SELECT id FROM Party WHERE Address = ? AND id != ?";
            $result = $conn->execute_query($sql, [$Address, $edit_id]);
        }
    }

    if (empty($_POST["city"])) {
        $CityErr = "City is required";
        $error = 1;
    } else {
        $City = test_input($_POST["city"]);

        if ($Edit == 0) {
            $sql = "SELECT id FROM Party WHERE City = ? AND id != ?";
            $result = $conn->execute_query($sql, [$City, $edit_id]);
        }
    }

    if (empty($_POST["pincode"])) {
        $Pincode = "";
    } else {
        $Pincode = test_input($_POST["pincode"]);

        if ($Edit == 0) {
            $sql = "SELECT id FROM Party WHERE Pincode = ? AND id != ?";
            $result = $conn->execute_query($sql, [$Pincode, $edit_id]);
        }
    }

    if (empty($_POST["email"])) {
        $EmailErr = "";
    } else {
        $Email = test_input($_POST["email"]);

        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $EmailErr = "Invaid format";
            $error = 1;
        } else {
            if ($Edit == 0) {
                $sql = "SELECT id FROM Party WHERE Email = ?";
                $result = $conn->execute_query($sql, [$Email]);
            } else {
                $sql = "SELECT id FROM Party WHERE Email = ? AND id != ?";
                $result = $conn->execute_query($sql, [$Email, $edit_id]);
            }

            if ($result->num_rows > 0) {
                $EmailErr = "This Email is already in use";
                $error = 1;
            }
        }
    }

    if (empty($_POST["party"])) {
        $Party_typeErr = "Party Type is Required";
        $error = 1;
    } else {
        $Party_type = test_input($_POST["party"]);
        if ($Edit == 0) {
            // ignore if edit
            $sql = "SELECT id FROM Party WHERE Party_Type = ? AND id != ?";
            $result = $conn->execute_query($sql, [$Party_type, $edit_id]);
        }
    }

    if ($error === 0) {
        if ($Edit == 0) {
            $sql = "INSERT INTO Party (Name, Mobileno, Address, City, Pincode, Email, Party_Type) VALUES (?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $Name, $Mobileno, $Address, $City, $Pincode, $Email, $Party_type);

            $stmt->execute();
            header("Location: edit_party.php?success=1");
            exit;
        } else {
            $sql = "UPDATE Party  SET Name=?, Mobileno=?, Address=?, City=?, Pincode=?, Email=?, Party_Type=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $Name, $Mobileno, $Address, $City, $Pincode, $Email, $Party_type, $edit_id);

            $stmt->execute();
            header("Location: edit_party.php?edit_success=1");
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
    <title>Party</title>
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
            height: auto;
            box-sizing: border-box;
        }

        select,
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

        textarea {
            height: 100px;
            width: 100%;
            box-sizing: border-box;
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
        <h1>Party</h1>
    </header>
    <nav style="text-align: center;">
        <ul>
            <li><a href="../Unit/Unit_table.php">Unit</a></li>
            <li><a href="../Product/Product_table.php">Product</a></li>
            <li><a href="../Purchase/Purchase_page.php">Purchase</a></li>
        </ul>
    </nav>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <button onclick="location.href='edit_party.php'" type="button">&#8618; Database</button>
        <h1>Fill the details:</h1>
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <label for="name">
            <b>
                Name :
            </b>
            <span class="error">
                *
                <?php echo $NameErr; ?>
            </span>
        </label>
        <br><br>
        <input type="text" name="name" placeholder="Enter the name...." value="<?php echo $Name; ?>">
        <br><br>

        <label for="Mobileno">
            <b>
                Mobile Number :
            </b>
            <span class="error">
                *
                <?php echo $MobilenoErr; ?>
            </span>
        </label>
        <br><br>
        <input type="text" maxlength="12" name="mobileno" placeholder="Enter the mobile no...."
            value="<?php echo $Mobileno; ?>">
        <h5>(format: 91[6 or 7 or 8 or 9]xxxxxxxxx)</h5>

        <label for="Address">
            <b>
                Address:
            </b>
        </label>
        <br><br>
        <textarea type="text" name="address" value=""><?php echo $Address; ?></textarea>
        <br><br>

        <label for="City">
            <b>
                City :
            </b>
            <span class="error">
                *
                <?php echo $CityErr; ?>
            </span>
        </label>
        <br><br>
        <select id="city" name="city">
            <option <?php if (isset($City) && $City == "") { ?> selected <?php } ?> value="">Select</option>
            <option <?php if (isset($City) && $City == "Sivakasi") { ?> selected <?php } ?> value="Sivakasi">Sivakasi
            </option>
            <option <?php if (isset($City) && $City == "Rajapalaiyam") { ?> selected <?php } ?> value="Rajapalaiyam">
                Rajapalaiyam</option>
            <option <?php if (isset($City) && $City == "Madurai") { ?> selected <?php } ?> value="Madurai">Madurai
            </option>
            <option <?php if (isset($City) && $City == "Srivilliputhur") { ?> selected <?php } ?> value="Srivilliputhur">
                Srivilliputhur</option>
            <option <?php if (isset($City) && $City == "Sattur") { ?> selected <?php } ?> value="Sattur">Sattur
            </option>
        </select>
        <br><br>

        <label for="pincode">
            <b>
                Pincode :
            </b>
        </label>
        <br><br>
        <input type="text" name="pincode" placeholder="Enter your pincode...." value="<?php echo $Pincode; ?>">
        <br><br>

        <label for="Email">
            <b>
                Email :
            </b>
            <span class="error">
                <?php echo $EmailErr; ?>
            </span>
        </label>
        <br><br>
        <input type="text" name="email" placeholder="Enter your email..." value="<?php echo $Email; ?>">
        <br><br>

        <label for="Party">
            <b>
                Party Type :
            </b>
            <span class="error">
                *
                <?php echo $Party_typeErr; ?>
            </span>
        </label>
        <br><br>
        <select id="party" name="party">
            <option <?php if (isset($Party_type) && $Party_type == "") { ?> selected <?php } ?> value="">Select
            </option>
            <option <?php if (isset($Party_type) && $Party_type == "sales") { ?> selected <?php } ?> value="sales">
                Sales
            </option>
            <option <?php if (isset($Party_type) && $Party_type == "purchase") { ?> selected <?php } ?> value="purchase">
                Purchase</option>
            <option <?php if (isset($Party_type) && $Party_type == "both") { ?> selected <?php } ?> value="both">Both
            </option>
        </select>
        <br><br><br>
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