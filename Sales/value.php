<?php
$q = isset($_GET['q']) ? intval($_GET['q']) : 0;

include "../config.php";

$sql = "SELECT unitValue, Rate FROM Product WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $q);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    echo trim($row['unitValue']) . "|" . trim($row['Rate']);
    ;
}

mysqli_close($conn);
?>