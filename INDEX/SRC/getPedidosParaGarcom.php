<?php
include('check.php');

$stmt = $con->prepare("
SELECT 
    PEDIDO.ID AS Pedido_ID,
    PEDIDO.DATA AS Pedido_Data,
    CARDAPIO.NOME AS Nome_Prato,
    ITENS.DESCRICAO AS Descricao_Item,
    ITENS.QUANTIDADE AS Quantidade_Item,
    MESA.NUMERO AS Numero_da_Mesa,
    NOW() AS Horario_Servidor
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
    PEDIDO.STATUS = 'GARCOM'
    AND EMPRESA.ID = ?
ORDER BY 
    PEDIDO.ID;
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$stmt = $stmt->get_result();

$agora = ''; // Hora atual

$pedidosAgrupados = [];

while ($pedido = $stmt->fetch_assoc()) {
    
    $pedidoID = $pedido['Pedido_ID'];
    $agora = new DateTime($pedido['Horario_Servidor']);
    
    if (!isset($pedidosAgrupados[$pedidoID])) {
        $pedidosAgrupados[$pedidoID] = [
            'Pedido_Data' => $pedido['Pedido_Data'],
            'Numero_da_Mesa' => $pedido['Numero_da_Mesa'],
            'Itens' => []
        ];
    }

    $pedidosAgrupados[$pedidoID]['Itens'][] = [
        'Nome_Prato' => $pedido['Nome_Prato'],
        'Descricao_Item' => $pedido['Descricao_Item'],
        'Quantidade_Item' => $pedido['Quantidade_Item']
    ];
}

foreach ($pedidosAgrupados as $pedidoID => $pedido) {
    $dataPedido = new DateTime($pedido['Pedido_Data']); // Formato de Pedido_Data
    $intervalo = $agora->diff($dataPedido);

    // Calcula os minutos passados
    $minutosPassados = ($intervalo->h * 60) + $intervalo->i; // Convertendo horas e minutos para minutos totais

    if ($minutosPassados > 5) {
        $corDeFundo = 'fundo-vermelho'; // Mais de 5 minutos
        $visibilidadeBTNpronto = 'd-none';
        $visibilidadeBTNretorno = 'd-inline';
    } elseif ($minutosPassados >= 2) {
        $corDeFundo = 'fundo-amarelo'; // Entre 2 e 5 minutos
        $visibilidadeBTNpronto = 'd-inline';
        $visibilidadeBTNretorno = 'd-none';
    } else {
        $corDeFundo = 'fundo-normal'; // Menos de 2 minutos
        $visibilidadeBTNpronto = 'd-inline';
        $visibilidadeBTNretorno = 'd-none';
    }

    echo "
        <div class='corpo'>
            <div class='card produto $corDeFundo' style='width: 18rem;'>
                <div class='card-body'>
                    <h5 class='card-title'>Pedido #".$pedidoID."</h5>
                    <hr>
                    <h5 class='card-title'>Entrega para a mesa: ".$pedido['Numero_da_Mesa']." </h5>
                    <hr>
                    <h5 class='card-title'>Conclusão do prato:</h5>
                    <p>".$pedido['Pedido_Data']."</p>
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
                    <a href='#' class='btn botao-entrega $visibilidadeBTNpronto' data-pedido-id='".$pedidoID."'>Pronto</a>
                    <a href='#' class='btn botao-voltar $visibilidadeBTNretorno' data-pedido-id='".$pedidoID."'>Voltar a Cozinha</a>
                </div>
            </div>
        </div>
    ";
}
?>
