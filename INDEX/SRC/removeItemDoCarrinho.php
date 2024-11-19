<?php
    session_start();

    $itemId = $_GET['SCitemId']; // Obtém o ID do item a partir da query string

    $idProcurado = $itemId; // ID que você deseja buscar
    $itemRemovido = false; // Flag para indicar se o item foi removido

    // Itera sobre os itens na sessão usando o índice
    foreach ($_SESSION['SCitems'] as $key => $item) {
        if ($item['id'] == $idProcurado) {
            unset($_SESSION['SCitems'][$key]); // Remove o item do array
            $itemRemovido = true; // Marca como removido
            break; // Encerra a busca
        }
    }

    // Verifica se o item foi encontrado e removido
    if ($itemRemovido) {
        return;
    } else {
        http_response_code(404);
}
?>
