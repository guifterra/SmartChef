<?php
session_start(); // Inicia a sessão
include('conexao.php'); // Inclui a conexão com o banco de dados

// Dados recebidos via GET
$empresaId = $_GET['SCempresaId'];
$tokenMesa = $_GET['SCtokenMesa'];
$numeroMesa = $_GET['SCnumeroMesa'];

// Consulta SQL para verificar se a mesa existe e o token está correto
$stmt = $con->prepare("
    SELECT ID
    FROM MESA
    WHERE EMPRESA_ID = ? AND NUMERO = ? AND TOKEN = ?
");
$stmt->bind_param("iis", $empresaId, $numeroMesa, $tokenMesa);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a consulta retornou resultados
if ($result->num_rows > 0 && isset($_SESSION['SCitems']) && !empty($_SESSION['SCitems'])) {
    $mesaData = $result->fetch_assoc();
    $mesaId = $mesaData['ID'];

    // Verifica se já existe uma comanda para essa mesa
    $stmt = $con->prepare("
        SELECT ID 
        FROM COMANDA 
        WHERE MESA_ID = ?
    ");
    $stmt->bind_param("i", $mesaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Comanda já existe, obter ID da comanda
        $comandaData = $result->fetch_assoc();
        $comandaId = $comandaData['ID'];
    } else {
        // Cria nova comanda para a mesa
        $stmt = $con->prepare("
            INSERT INTO COMANDA (MESA_ID) 
            VALUES (?)
        ");
        $stmt->bind_param("i", $mesaId);
        $stmt->execute();
        $comandaId = $stmt->insert_id; // ID da nova comanda
    }

    // Cria novo pedido na tabela PEDIDO
    $stmt = $con->prepare("
        INSERT INTO PEDIDO (COMANDA_ID, STATUS, DATA) 
        VALUES (?, 'COZINHA', NOW())
    ");
    $stmt->bind_param("i", $comandaId);
    $stmt->execute();
    $pedidoId = $stmt->insert_id; // ID do novo pedido

    // Copia os dados da sessão para uma variável
    $items = isset($_SESSION['SCitems']) ? $_SESSION['SCitems'] : [];

    // Adiciona itens ao pedido
    if (!empty($items) && is_array($items)) {
        foreach ($items as $item) {
            $cardapioId = $item['cardapioID'];
            $descricao = $item['observacao'];
            $quantidade = $item['quantidade'];
            $preco = $item['preco'];

            // Tratamento de adicionais e alergicos
            $alergicos = !empty($item['alergicos']) ? "<p><strong>Alergicos:</strong> " . implode(", ", $item['alergicos']) . "</p>" : "";
            $adicionaisTexto = [];
            $adicionaisPrecoTotal = 0;

            if (!empty($item['adicionais'])) {
                foreach ($item['adicionais'] as $adicional) {
                    // Separar a string em nome e preço
                    list($nome, $precoAdicional) = explode("&", $adicional);
                    $adicionaisTexto[] = $nome; // Adicionar o nome à lista
                    $adicionaisPrecoTotal += (float)$precoAdicional; // Somar o preço ao total
                }
            }

            $adicionais = !empty($adicionaisTexto) ? "<p><strong>Adicionais:</strong> " . implode(", ", $adicionaisTexto) . "</p>" : "";

            // Atualizar a descrição com alergicos e adicionais
            $descricao .= $alergicos . $adicionais;

            // Atualizar o preço total com os adicionais
            $preco += $adicionaisPrecoTotal;

            // Inserir o item no banco
            $stmt = $con->prepare("
                INSERT INTO ITENS (PEDIDO_ID, CARDAPIO_ID, DESCRICAO, QUANTIDADE, PRECO) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iissd", $pedidoId, $cardapioId, $descricao, $quantidade, $preco);
            $stmt->execute();

            $_SESSION['SCitems'] = [];
        }
    }

} else {
    // Retorna 404 se as condições não forem atendidas
    http_response_code(404);
}
?>
