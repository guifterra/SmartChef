<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $quantidade = $_POST['quantity'] ?? '';
    $observacao = $_POST['observation'] ?? '';
    $cardapioID = $_POST['cardapio_id'] ?? '';
    $preco      = $_POST['preco'] ?? '';
    $nome = $_POST['nomePrato'] ?? '';
    $alergicos  = $_POST['alergenicos'] ?? [];
    $adicionais = $_POST['adicionais'] ?? [];

    if( $observacao == '' ){
        $observacao = "Comum, sem detalhes extras";
    }

    session_start();

    // Inicia o array na sessão, se não existir
    if (!isset($_SESSION['SCitems'])) {
        $_SESSION['SCitems'] = [];
    }

    if (!isset($_SESSION['SCitemID'])) {
        $_SESSION['SCitemID'] = 1; // Começa o ID em 1
    } else {
        $_SESSION['SCitemID']++; // Incrementa o ID
    }

    // Adiciona os novos dados ao array
    $_SESSION['SCitems'][] = [
        'id'         => $_SESSION['SCitemID'],
        'quantidade' => $quantidade,
        'observacao' => $observacao,
        'cardapioID' => $cardapioID,
        'preco'      => $preco,
        'nomePrato'  => $nome,
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
