<?php
include('check.php');

// Consulta para obter as mesas disponíveis
$stmt = $con->prepare("
    SELECT NUMERO, EMPRESA_ID, TOKEN 
    FROM PI22024.MESA
    WHERE EMPRESA_ID = ?
");
$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$result = $stmt->get_result();

while ($mesa = $result->fetch_assoc()) {
    echo "
        <button class='btn btn-mesa botao-mesas' data-mesa-id='{$mesa['NUMERO']}'>
            Mesa {$mesa['NUMERO']}
        </button>
    ";
}
?>
