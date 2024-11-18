<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantidade = $_POST['quantity'] ?? '';
    $observacao = $_POST['observation'] ?? '';
    $cardapioID = $_POST['cardapio_id'] ?? '';
    $preco      = $_POST['preco'] ?? '';
    $alergicos  = $_POST['alergenicos'] ?? [];
    $adicionais = $_POST['adicionais'] ?? [];

    session_start();

    // Inicia o array na sessão, se não existir
    if (!isset($_SESSION['SCitems'])) {
        $_SESSION['SCitems'] = [];
    }

    // Adiciona os novos dados ao array
    $_SESSION['SCitems'][] = [
        'quantidade' => $quantidade,
        'observacao' => $observacao,
        'cardapioID' => $cardapioID,
        'preco'      => $preco,
        'alergicos'  => $alergicos,
        'adicionais' => $adicionais
    ];

    // Aqui você pode salvar os dados no banco de dados, sessão, etc.
    $response = [
            'status' => 'success',
            'message' => 'Produto adicionado ao carrinho',
    ];
    
    echo json_encode($response);
    exit;
}
