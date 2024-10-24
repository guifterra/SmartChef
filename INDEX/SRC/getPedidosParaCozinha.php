<?php
include('check.php');

$stmt = $con->prepare("
SELECT 
    PEDIDO.ID AS Pedido_ID,
    CARDAPIO.NOME AS Nome_Prato,
    ITENS.DESCRICAO AS Descricao_Item,
    ITENS.QUANTIDADE AS Quantidade_Item,
    MESA.NUMERO AS Numero_Mesa
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
    $pedidoMesa = $pedido['Numero_Mesa'];
    
    if (!isset($pedidosAgrupados[$pedidoID])) {
        $pedidosAgrupados[$pedidoID] = [
            'Itens' => [],
            'NumeroMesa' => $pedidoMesa
        ];
    }

    $pedidosAgrupados[$pedidoID]['Itens'][] = [
        'Nome_Prato' => $pedido['Nome_Prato'],
        'Descricao_Item' => $pedido['Descricao_Item'],
        'Quantidade_Item' => $pedido['Quantidade_Item']
    ];
}

foreach ($pedidosAgrupados as $pedidoID => $pedido) {
    echo "
        <div class='corpo'>
            <div class='card produto' style='width: 18rem;'>
                <div class='card-body'>
                    <h5 class='card-title'>Pedido #".$pedidoID."</h5>
                    <hr>
                    <h5 class='card-title'>Mesa: ".$pedido['NumeroMesa']."</h5>
    ";

    foreach ($pedido['Itens'] as $item) {
        echo "
            <hr>
            <p>
                <strong>".$item['Nome_Prato']."</strong>: <br>
                ".$item['Descricao_Item']." <br>
                <strong>Quantidade: </strong> ".$item['Quantidade_Item']."
            </p>
        ";
    }

    echo "
                    <a href='#' class='btn botao-entrega' data-pedido-id='".$pedidoID."'>Pronto</a>
                </div>
            </div>
        </div>
    ";
}
?>
