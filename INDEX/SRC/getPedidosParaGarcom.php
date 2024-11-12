<?php
include('check.php');

// 1. Seleciona os IDs dos 5 pedidos mais recentes com status "GARCOM"
$stmt = $con->prepare("
    SELECT ID 
    FROM PI22024.PEDIDO
    WHERE STATUS = 'GARCOM' AND COMANDA_ID IN (
        SELECT COMANDA.ID FROM PI22024.COMANDA 
        LEFT JOIN PI22024.MESA ON COMANDA.MESA_ID = MESA.ID 
        LEFT JOIN PI22024.EMPRESA ON MESA.EMPRESA_ID = EMPRESA.ID 
        WHERE EMPRESA.ID = ?
    )
    ORDER BY ID 
    LIMIT 5
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$result = $stmt->get_result();
$pedidoIDs = [];

while ($row = $result->fetch_assoc()) {
    $pedidoIDs[] = $row['ID'];
}

// Verifica se encontrou pedidos para continuar a consulta
if (count($pedidoIDs) > 0) {
    // 2. Busca todos os itens dos pedidos selecionados
    $in = str_repeat('?,', count($pedidoIDs) - 1) . '?';
    $sql = "
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
            PEDIDO.ID IN ($in)
        ORDER BY 
            PEDIDO.ID, CARDAPIO.NOME, ITENS.DESCRICAO, ITENS.QUANTIDADE;
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($pedidoIDs)), ...$pedidoIDs);
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
        $dataPedido = new DateTime($pedido['Pedido_Data']);
        $intervalo = $agora->diff($dataPedido);
        $minutosPassados = ($intervalo->h * 60) + $intervalo->i;

        if ($minutosPassados > 5) {
            $corDeFundo = 'fundo-vermelho';
            $visibilidadeBTNpronto = 'd-none';
            $visibilidadeBTNretorno = 'd-inline';
        } elseif ($minutosPassados >= 2) {
            $corDeFundo = 'fundo-amarelo';
            $visibilidadeBTNpronto = 'd-inline';
            $visibilidadeBTNretorno = 'd-none';
        } else {
            $corDeFundo = 'fundo-normal';
            $visibilidadeBTNpronto = 'd-inline';
            $visibilidadeBTNretorno = 'd-none';
        }

        echo "
            <div class='corpo'>
                <div class='card produto' style='width: 18rem;'>
                    <div class='card-body'>
                        <div class='$corDeFundo p-2' style='border-radius: 5px'>
                        <h5 class='card-title text-center' style='font-weight: bold;'>Pedido ".$pedidoID."</h5>
                        </div>
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
} else {
    echo "";
}
?>
