<?php
include('check.php');

// Obter o ID do usuário e o status atual da conta
$userId = $_POST['userId'];
$currentStatus = $_POST['currentStatus'];

// Determina o novo status (se for 1, muda para 0, e vice-versa)
$newStatus = $currentStatus == 1 ? 0 : 1;

// Atualiza o status no banco de dados
$stmt = $con->prepare("UPDATE PI22024.USER SET VALIDO = ? WHERE ID = ?");
$stmt->bind_param("ii", $newStatus, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'newStatus' => $newStatus]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status do usuário.']);
}
?>
