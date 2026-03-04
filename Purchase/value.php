<?php
$q = isset($_GET['q']) ? intval($_GET['q']) : 0;

include "../config.php";

if (isset($_GET['q']) && is_numeric($_GET['q'])) {
    $id = intval($_GET['q']);

    $stmt = $conn->prepare("SELECT unit_name, rate FROM Product WHERE id = ?");
    $stmt->bind_param("i", $q);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo $row['unit_name'] . "|" . $row['rate'];
    } else {
        echo "|";
    }

    $stmt->close();
} else {
    echo "|";
}

?>