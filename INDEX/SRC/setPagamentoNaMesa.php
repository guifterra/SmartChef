<?php
include('check.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtendo os valores do POST
    $valorPagamento = $_POST['valorDoPagamento'] ?? '';
    $idDaMesa = $_POST['idDaMesa'] ?? '';
    $idDaEmpresa = $_POST['idDaEmpresa'] ?? '';

    // Verifica se o pagamento é maior que zero
    if ($valorPagamento <= 0) {
        http_response_code(404);
        return;
    }

    if ($valorPagamento && $idDaMesa && $idDaEmpresa) {

        // 1. Verifica se existe item no cardápio com categoria 'Pagamento'
        $stmt = $con->prepare("
            SELECT ID FROM PI22024.CARDAPIO 
            WHERE EMPRESA_ID = ? AND CATEGORIA = 'Pagamento'
        ");
        $stmt->bind_param("i", $idDaEmpresa);
        $stmt->execute();
        $result = $stmt->get_result();
        $pagamentoCardapio = $result->fetch_assoc();

        // Se não existir item de pagamento, criar
        if (!$pagamentoCardapio) {
            $stmtInsert = $con->prepare("
                INSERT INTO PI22024.CARDAPIO (EMPRESA_ID, NOME, PRECO, CATEGORIA, STATUS)
                VALUES (?, 'Pagamento', 0, 'Pagamento', 1)
            ");
            $stmtInsert->bind_param("i", $idDaEmpresa);
            $stmtInsert->execute();
            $pagamentoCardapioID = $stmtInsert->insert_id;
        } else {
            $pagamentoCardapioID = $pagamentoCardapio['ID'];
        }

        // 2. Verificar se a mesa tem uma comanda
        $stmt = $con->prepare("
            SELECT COMANDA.ID AS comanda_id
            FROM PI22024.COMANDA
            WHERE MESA_ID = ?
        ");
        $stmt->bind_param("i", $idDaMesa);
        $stmt->execute();
        $result = $stmt->get_result();
        $comanda = $result->fetch_assoc();

        if ($comanda) {
            $comandaID = $comanda['comanda_id'];

            // 3. Verificar se existe pedido com item da categoria "Pagamento" OU "Desconto"
            $stmt = $con->prepare("
                SELECT PEDIDO.ID AS pedido_id
                FROM PI22024.PEDIDO
                JOIN PI22024.ITENS ON ITENS.PEDIDO_ID = PEDIDO.ID
                JOIN PI22024.CARDAPIO ON ITENS.CARDAPIO_ID = CARDAPIO.ID
                WHERE PEDIDO.COMANDA_ID = ? 
                AND (CARDAPIO.CATEGORIA = 'Pagamento' OR CARDAPIO.CATEGORIA = 'Desconto' OR CARDAPIO.CATEGORIA = 'Troco')
            ");
            $stmt->bind_param("i", $comandaID);
            $stmt->execute();
            $result = $stmt->get_result();
            $pedidoPagamentoOuDesconto = $result->fetch_assoc();

            // Se já houver um pedido de pagamento ou desconto, vamos usá-lo
            if ($pedidoPagamentoOuDesconto) {
                $pedidoID = $pedidoPagamentoOuDesconto['pedido_id'];
            } else {
                // Caso contrário, criar novo pedido
                $stmtInsertPedido = $con->prepare("
                    INSERT INTO PI22024.PEDIDO (COMANDA_ID, STATUS, DATA)
                    VALUES (?, 'CAIXA', NOW())
                ");
                $stmtInsertPedido->bind_param("i", $comandaID);
                $stmtInsertPedido->execute();
                $pedidoID = $stmtInsertPedido->insert_id;
            }

        } else {
            // Erro: Não existe comanda para a mesa informada
            http_response_code(404);
            return;
        }

        // 4. Inserir o pagamento na tabela ITENS (com valor negativo)
        $valorPagamentoNegativo = $valorPagamento * -1;
        $stmtInsertItem = $con->prepare("
            INSERT INTO PI22024.ITENS (PEDIDO_ID, CARDAPIO_ID, DESCRICAO, PRECO, QUANTIDADE)
            VALUES (?, ?, '', ?, 1)
        ");
        $stmtInsertItem->bind_param("iid", $pedidoID, $pagamentoCardapioID, $valorPagamentoNegativo);
        $stmtInsertItem->execute();

        echo "Pagamento registrado com sucesso!";
        
    } else {
        http_response_code(404);
        return;
    }

} else {
    http_response_code(404);
    return;
}
?>
