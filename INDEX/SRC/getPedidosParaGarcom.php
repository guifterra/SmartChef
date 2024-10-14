<?php
include('check.php');

$stmt = $con->prepare("
SELECT 
    PEDIDO.ID AS Pedido_ID,
    PEDIDO.DATA AS Pedido_Data,
    CARDAPIO.NOME AS Nome_Prato,
    ITENS.DESCRICAO AS Descricao_Item,
    MESA.NUMERO AS Numero_da_Mesa
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
    PEDIDO.STATUS = 'GARCON'
    AND EMPRESA.ID = ?
ORDER BY 
    PEDIDO.ID;
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$stmt = $stmt->get_result();

date_default_timezone_set('America/Sao_Paulo');
$agora = new DateTime(); // Hora atual

$pedidosAgrupados = [];

while ($pedido = $stmt->fetch_assoc()) {
    
    $pedidoID = $pedido['Pedido_ID'];
    
    if (!isset($pedidosAgrupados[$pedidoID])) {
        $pedidosAgrupados[$pedidoID] = [
            'Pedido_Data' => $pedido['Pedido_Data'],
            'Numero_da_Mesa' => $pedido['Numero_da_Mesa'],
            'Itens' => []
        ];
    }

    $pedidosAgrupados[$pedidoID]['Itens'][] = [
        'Nome_Prato' => $pedido['Nome_Prato'],
        'Descricao_Item' => $pedido['Descricao_Item']
    ];
}

foreach ($pedidosAgrupados as $pedidoID => $pedido) {
    $dataPedido = new DateTime($pedido['Pedido_Data']); // Formato de Pedido_Data
    $intervalo = $agora->diff($dataPedido);

    // Calcula os minutos passados
    $minutosPassados = ($intervalo->h * 60) + $intervalo->i; // Convertendo horas e minutos para minutos totais

    if ($minutosPassados > 5) {
        $corDeFundo = 'fundo-vermelho'; // Mais de 5 minutos
    } elseif ($minutosPassados >= 2) {
        $corDeFundo = 'fundo-amarelo'; // Entre 2 e 5 minutos
    } else {
        $corDeFundo = 'fundo-normal'; // Menos de 2 minutos
    }

    echo "
        <div class='corpo'>
            <div class='card produto $corDeFundo' style='width: 18rem;'>
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
                    <h5 class='card-title'>Entrega para a mesa:</h5>
                    <p>".$pedido['Numero_da_Mesa']."</p>
                    <h5 class='card-title'>D/h de conclusão do prato:</h5>
                    <p>".$pedido['Pedido_Data']."</p>
                    <a href='#' class='btn botao-entrega'>Pronto</a>
                    <a href='#' class='btn botao-cancelar'>Cancelar</a>
                </div>
            </div>
        </div>
    ";
}
?>
