<?php
include('check.php'); // Inclui a conexão com o banco de dados

// Habilitar exibição de erros no PHP para fins de depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verifica se o valor de quantidadeDeMesa foi enviado via POST
if (isset($_POST['quantidadeDeMesa'])) {
    // Coleta o valor do número de mesas desejado
    $quantidadeDeMesa = intval($_POST['quantidadeDeMesa']); // Converte para inteiro
    $empresaId = $_COOKIE["EMPRESA_ID"]; // Obtém o ID da empresa do cookie

    // Consulta para verificar a quantidade de mesas atuais na empresa
    $sqlMesasAtuais = "SELECT COUNT(*) AS total FROM MESA WHERE EMPRESA_ID = ?";
    $stmtMesasAtuais = $con->prepare($sqlMesasAtuais);
    $stmtMesasAtuais->bind_param("i", $empresaId);
    $stmtMesasAtuais->execute();
    $resultMesasAtuais = $stmtMesasAtuais->get_result();
    $mesasAtuais = $resultMesasAtuais->fetch_assoc()['total'];

    // Verifica as condições
    if ($mesasAtuais == $quantidadeDeMesa) {
        // Caso a quantidade desejada já seja igual à atual
        echo json_encode(["success" => false, "message" => "A quantidade de mesas já é igual à quantidade solicitada."]);
        exit;
    }

    if ($mesasAtuais > $quantidadeDeMesa) {
        // Verifica se alguma mesa tem comandas abertas
        $sqlComandasAbertas = "SELECT M.NUMERO FROM COMANDA C INNER JOIN MESA M ON C.MESA_ID = M.ID WHERE M.EMPRESA_ID = ?";
        $stmtComandasAbertas = $con->prepare($sqlComandasAbertas);
        $stmtComandasAbertas->bind_param("i", $empresaId);
        $stmtComandasAbertas->execute();
        $resultComandasAbertas = $stmtComandasAbertas->get_result();

        $comandas = [];
        while ($row = $resultComandasAbertas->fetch_assoc()) {
            $comandas[] = $row['NUMERO'];
        }

        if (count($comandas) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Não é possível excluir, existem mesas com comandas abertas. Mesas: " . implode(', ', $comandas)
            ]);
            exit;
        }

        // Se não houver comandas abertas, pode excluir as mesas a mais
        $sqlExcluirMesas = "DELETE FROM MESA WHERE EMPRESA_ID = ? AND NUMERO > ?";
        $stmtExcluirMesas = $con->prepare($sqlExcluirMesas);
        $stmtExcluirMesas->bind_param("ii", $empresaId, $quantidadeDeMesa);
        
        if ($stmtExcluirMesas->execute()) {
            echo json_encode(["success" => true, "message" => "Mesas removidas com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao remover mesas: " . $stmtExcluirMesas->error]);
        }
        exit;
    }

    if ($mesasAtuais < $quantidadeDeMesa) {
        // Insere as novas mesas
        $mesasParaAdicionar = $quantidadeDeMesa - $mesasAtuais;
        for ($i = 1; $i <= $mesasParaAdicionar; $i++) {
            $token = bin2hex(openssl_random_pseudo_bytes(20)); // Gerando o token para cada mesa

            $sqlInsertMesa = "
                INSERT INTO MESA (EMPRESA_ID, NUMERO, TOKEN)
                VALUES (?, ?, ?)
            ";
            $stmtInsertMesa = $con->prepare($sqlInsertMesa);
            $numeroMesa = $mesasAtuais + $i;
            $stmtInsertMesa->bind_param("iis", $empresaId, $numeroMesa, $token);

            if (!$stmtInsertMesa->execute()) {
                echo json_encode(["success" => false, "message" => "Erro ao adicionar a mesa número $numeroMesa: " . $stmtInsertMesa->error]);
                exit;
            }
        }
        echo json_encode(["success" => true, "message" => "Mesas adicionadas com sucesso!"]);
        exit;
    }
} else {
    // Retorna erro se a quantidade de mesas não foi enviada
    echo json_encode(["success" => false, "message" => "Número de mesas não informado."]);
    http_response_code(400);
}
?>
