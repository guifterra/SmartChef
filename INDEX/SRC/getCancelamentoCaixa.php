<?php
include('conexao.php'); // Inclui a conexão com o banco de dados

$empresaId = $_GET['SCempresaId'];
$numeroMesa = $_GET['SCmesaId'];

// Consulta SQL para verificar se a mesa existe e o token está correto
$stmt = $con->prepare("
    SELECT ID 
    FROM MESA
    WHERE EMPRESA_ID = ? AND ID = ?
");
$stmt->bind_param("ii", $empresaId, $numeroMesa);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a consulta retornou resultados
if ($result->num_rows > 0) {
    $mesaData = $result->fetch_assoc();
    $mesaId = $mesaData['ID'];

    // Busca a comanda associada à mesa
    $stmt = $con->prepare("
        SELECT ID 
        FROM COMANDA 
        WHERE MESA_ID = ?
    ");
    $stmt->bind_param("i", $mesaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $comandaData = $result->fetch_assoc();
        $comandaId = $comandaData['ID'];

        // Busca os pedidos associados à comanda
        $stmt = $con->prepare("
            SELECT ID 
            FROM PEDIDO 
            WHERE COMANDA_ID = ?
        ");
        $stmt->bind_param("i", $comandaId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $itensEncontrados = false; // Flag para verificar se há itens

            while ($pedidoData = $result->fetch_assoc()) {
                $pedidoId = $pedidoData['ID'];

                // Busca os itens associados ao pedido
                $stmt = $con->prepare("
                    SELECT ID, CARDAPIO_ID, DESCRICAO, QUANTIDADE, PRECO 
                    FROM ITENS 
                    WHERE PEDIDO_ID = ? AND QUANTIDADE != 0
                ");
                $stmt->bind_param("i", $pedidoId);
                $stmt->execute();
                $itemsResult = $stmt->get_result();

                // Exibe os itens no formato desejado
                while ($item = $itemsResult->fetch_assoc()) {
                    $itensEncontrados = true; // Marca que pelo menos um item foi encontrado

                    $cardapioId = $item['CARDAPIO_ID'];

                    // Busca o nome do prato na tabela CARDAPIO
                    $cardapioStmt = $con->prepare("
                        SELECT NOME 
                        FROM CARDAPIO 
                        WHERE ID = ?
                    ");
                    $cardapioStmt->bind_param("i", $cardapioId);
                    $cardapioStmt->execute();
                    $cardapioResult = $cardapioStmt->get_result();
                    $cardapioData = $cardapioResult->fetch_assoc();
                    $nomePrato = $cardapioData['NOME'];

                    $idDoItem = $item['ID'];
                    $descricao = $item['DESCRICAO'];
                    $quantidade = $item['QUANTIDADE'];
                    $preco = $item['PRECO'];

                    echo "
                        <div class='col-8'>
                            <p class='nome-item'>{$nomePrato}</p>
                            <p><span style='font-weight: bold;'>Descrição: </span>{$descricao}</p>
                        </div>
                        <div class='col-4 text-end mb-4'>
                            <p class='quantidade'><span style='font-weight: bold;'>Quantidade: </span>{$quantidade}</p>
                            <button class='botao cancelar botao-cancelar-item-selecionado' data-idDoItem='{$idDoItem}' data-idDoRes='{$_COOKIE['EMPRESA_ID']}' data-idDaMesa='{$mesaId}'>Cancelar item</button>
                        </div>
                        <hr>
                    ";
                }
            }

            // Verifica se nenhum item foi encontrado após iterar todos os pedidos
            if (!$itensEncontrados) {
                echo "<p>Não é possível cancelar nenhum item.</p>";
            }
        } else {
            echo "Nenhum pedido encontrado para esta comanda.";
        }
    } else {
        echo "Nenhuma comanda encontrada para esta mesa.";
    }
} else {
    http_response_code(404);
    echo "Mesa não encontrada.";
}
?>
