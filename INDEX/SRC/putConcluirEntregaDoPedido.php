<?php
include('check.php');

if (isset($_POST['pedido_id'])) {
    
    $pedido_id = $_POST['pedido_id'];

    $stmt = $con->prepare("UPDATE PI22024.PEDIDO SET STATUS = 'CAIXA', DATA = NOW() WHERE ID = ?");
    $stmt->bind_param("i", $pedido_id);

    if ($stmt->execute()) {

        echo json_encode(['success' => true, 'message' => 'Comanda enviado para o CAIXA']);

    } else {
        
        echo json_encode(['success' => false, 'message' => 'Falha ao enviar comanda']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Comanda não identificada']);
}
?>
