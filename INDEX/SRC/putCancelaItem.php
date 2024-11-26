<?php
include('conexao.php'); // Inclui a conexão com o banco de dados

$itemId = $_GET['SCidItemCancelado']; // Obtém o ID do item a ser cancelado

if (isset($itemId)) {
    // Prepara a consulta SQL para atualizar a quantidade do item
    $stmt = $con->prepare("
        UPDATE ITENS 
        SET QUANTIDADE = 0 
        WHERE ID = ?
    ");
    $stmt->bind_param("i", $itemId); // Liga o parâmetro ID

    if ($stmt->execute()) {
        // Verifica se a atualização foi bem-sucedida
        if ($stmt->affected_rows > 0) {
            
        } else {
            http_response_code(404);
        }
    } else {
        // Exibe mensagem de erro caso a execução da consulta falhe
        http_response_code(404);
    }

    $stmt->close(); // Fecha o comando preparado
} else {
    // Resposta caso o parâmetro SCidItemCancelado não seja fornecido
    http_response_code(404);
}

$con->close(); // Fecha a conexão com o banco de dados
?>
