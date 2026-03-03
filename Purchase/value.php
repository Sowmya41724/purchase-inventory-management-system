<?php
$q = isset($_GET['q']) ? intval($_GET['q']) : 0;

include "../config.php";

$sql = "SELECT unit_name, rate FROM Product WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $q);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    echo trim($row['unit_name']) . "|" . trim($row['rate']);
    ;
}

mysqli_close($conn);
?>