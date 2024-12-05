<?php
include('check.php'); // Inclui a conexão já configurada

header('Content-Type: application/json'); // Define o cabeçalho como JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDaMesa = $_POST['idDaMesa'] ?? '';
    $idDaEmpresa = $_COOKIE["EMPRESA_ID"] ?? '';

    if (!empty($idDaMesa) && !empty($idDaEmpresa)) {
        $stmt = $con->prepare("CALL SalvarHistoricoELimparDados(?, ?)");
        $stmt->bind_param("ii", $idDaMesa, $idDaEmpresa);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Histórico salvo e dados limpos com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao executar a procedure.', 'error' => $stmt->error]);
            http_response_code(400); // Bad Request
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes fornecidos.']);
        http_response_code(400); // Bad Request
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    http_response_code(405); // Method Not Allowed
}
?>
