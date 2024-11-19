<?php
    session_start();

    $itemId = $_GET['SCitemId']; // Obtém o ID do item a partir da query string

    $idProcurado = $itemId; // ID que você deseja buscar
    $itemAtualizado = false; // Flag para indicar se o item foi atualizado

    // Itera sobre os itens na sessão
    foreach ($_SESSION['SCitems'] as &$item) { // Use & para modificar o item diretamente
        
        if ($item['id'] == $idProcurado) {
            if( $item['quantidade'] > 1 ){
                $item['quantidade']--; // Decrementa a quantidade do item
            }
            $itemAtualizado = true; // Marca como atualizado
            break; // Encerra a busca
        }
    }

    // Verifica se o item foi encontrado e atualizado
    if ($itemAtualizado) {
        return;
    } else {
        http_response_code(404);
    }
?>
