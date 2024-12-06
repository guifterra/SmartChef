<?php
include('check.php'); // Inclui a conexão já configurada

header('Content-Type: application/json'); // Define o cabeçalho como JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDaMesa = isset($_POST['idDaMesa']) ? trim($_POST['idDaMesa']) : '';
    $idDaEmpresa = isset($_COOKIE['EMPRESA_ID']) ? trim($_COOKIE['EMPRESA_ID']) : '';

    if (!empty($idDaMesa) && !empty($idDaEmpresa)) {
        // Verifica se há pelo menos uma comanda e um pedido associados à mesa
        $queryComanda = "SELECT C.ID AS comanda_id 
                         FROM COMANDA C
                         INNER JOIN MESA M ON C.MESA_ID = M.ID
                         WHERE M.ID = ? AND M.EMPRESA_ID = ?";
        $stmtComanda = $con->prepare($queryComanda);
        $stmtComanda->bind_param("ii", $idDaMesa, $idDaEmpresa);
        $stmtComanda->execute();
        $resultComanda = $stmtComanda->get_result();

        if ($resultComanda->num_rows > 0) {
            $comandaId = $resultComanda->fetch_assoc()['comanda_id'];

            // Verifica se todos os pedidos associados à comanda estão com status 'CAIXA'
            $queryPedidosStatus = "SELECT COUNT(*) AS total_nao_caixa 
                                   FROM PEDIDO P 
                                   WHERE P.COMANDA_ID = ? AND P.STATUS != 'CAIXA'";
            $stmtStatus = $con->prepare($queryPedidosStatus);
            $stmtStatus->bind_param("i", $comandaId);
            $stmtStatus->execute();
            $resultStatus = $stmtStatus->get_result();
            $naoCaixa = $resultStatus->fetch_assoc()['total_nao_caixa'];

            if ($naoCaixa == 0) {
                // Verifica se a soma dos preços dos itens (preço * quantidade) é igual a 0
                $querySomaItens = "SELECT SUM(I.PRECO * I.QUANTIDADE) AS total 
                                   FROM ITENS I 
                                   INNER JOIN PEDIDO P ON I.PEDIDO_ID = P.ID 
                                   WHERE P.COMANDA_ID = ?";
                $stmtSoma = $con->prepare($querySomaItens);
                $stmtSoma->bind_param("i", $comandaId);
                $stmtSoma->execute();
                $resultSoma = $stmtSoma->get_result();
                $total = $resultSoma->fetch_assoc()['total'];

                if ($total == 0) {
                    // Tudo certo, executa a procedure
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
                    echo json_encode(['success' => false, 'message' => 'O total dos itens deve ser igual a 0.']);
                    http_response_code(400); // Bad Request
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Nem todos os pedidos estão no CAIXA.']);
                http_response_code(400); // Bad Request
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma comanda ou pedido encontrado para a mesa fornecida.']);
            http_response_code(400); // Bad Request
        }

        $stmtComanda->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes fornecidos.']);
        http_response_code(400); // Bad Request
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    http_response_code(405); // Method Not Allowed
}
?>
