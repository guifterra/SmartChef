<?php
include('check.php');

$empresaId = $_COOKIE["EMPRESA_ID"];
$sql = "SELECT ID FROM MESA WHERE EMPRESA_ID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $empresaId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row['ID'] . "'>Mesa " . $row['ID'] . "</option>";
}
?>
