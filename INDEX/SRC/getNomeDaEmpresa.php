<?php
include('conexao.php');
$stmt = $con->prepare("SELECT USERNAME FROM EMPRESA WHERE ID = ? LIMIT 1");

$empresaId = $_GET['SCempresaId'];

$stmt->bind_param("i", $empresaId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo "<h5>" . $data['USERNAME'] . "</h5>";
} else {
    http_response_code(404);
}
