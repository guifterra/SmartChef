<?php
include('check.php');

$stmt = $con->prepare("
SELECT 
    PEDIDO.ID AS Pedido_ID,
    CARDAPIO.NOME AS Nome_Prato,
    ITENS.DESCRICAO AS Descricao_Item
FROM 
    PI22024.PEDIDO
LEFT JOIN 
    PI22024.ITENS ON PEDIDO.ID = ITENS.PEDIDO_ID
LEFT JOIN 
    PI22024.CARDAPIO ON ITENS.CARDAPIO_ID = CARDAPIO.ID
LEFT JOIN 
    PI22024.COMANDA ON PEDIDO.COMANDA_ID = COMANDA.ID
LEFT JOIN 
    PI22024.MESA ON COMANDA.MESA_ID = MESA.ID
LEFT JOIN 
    PI22024.EMPRESA ON MESA.EMPRESA_ID = EMPRESA.ID
WHERE 
    PEDIDO.STATUS = 'COZINHA'
    AND EMPRESA.ID = ?
ORDER BY 
    PEDIDO.ID;
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$stmt = $stmt->get_result();

$pedidosAgrupados = [];

while ($pedido = $stmt->fetch_assoc()) {
    $pedidoID = $pedido['Pedido_ID'];
    
    if (!isset($pedidosAgrupados[$pedidoID])) {
        $pedidosAgrupados[$pedidoID] = [
            'Itens' => []
        ];
    }

    $pedidosAgrupados[$pedidoID]['Itens'][] = [
        'Nome_Prato' => $pedido['Nome_Prato'],
        'Descricao_Item' => $pedido['Descricao_Item']
    ];
}

foreach ($pedidosAgrupados as $pedidoID => $pedido) {
    echo "
        <div class='corpo'>
            <div class='card produto' style='width: 18rem;'>
                <div class='card-body'>
                    <h5 class='card-title'>Pedido #".$pedidoID."</h5>
                    <h5 class='card-title'>Itens do Pedido:</h5>
    ";

    foreach ($pedido['Itens'] as $item) {
        echo "
            <p><strong>".$item['Nome_Prato']."</strong>: <br>".$item['Descricao_Item']."</p>
        ";
    }

    echo "
                    <a href='#' class='btn botao-entrega'>Pronto</a>
                    <a href='#' class='btn botao-cancelar'>Cancelar</a>
                </div>
            </div>
        </div>
    ";
}
?>