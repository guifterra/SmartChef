<?php
include('check.php');

// 1. Seleciona os IDs dos 5 pedidos mais recentes com o status "COZINHA" para a empresa atual
$stmt = $con->prepare("
    SELECT ID 
    FROM PI22024.PEDIDO
    WHERE STATUS = 'COZINHA' AND COMANDA_ID IN (
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
    // 2. Agora busca todos os itens dos pedidos selecionados
    $in = str_repeat('?,', count($pedidoIDs) - 1) . '?';
    $sql = "
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
            PEDIDO.ID IN ($in)
        ORDER BY 
            PEDIDO.ID, CARDAPIO.NOME, ITENS.DESCRICAO, ITENS.QUANTIDADE;
    ";

    $stmt = $con->prepare($sql);

    // Adiciona os IDs dos pedidos como parâmetros na consulta
    $stmt->bind_param(str_repeat('i', count($pedidoIDs)), ...$pedidoIDs);
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
            // Verifica se a quantidade é zero
            if ($item['Quantidade_Item'] == 0) {
                // Exibe o item com estilo para quantidade zero
                echo "
                    <hr>
                    <p>
                        <strong><span style='color: red; text-decoration: line-through;'>".$item['Nome_Prato']."</span></strong>: <br>
                        <span style='color: red; text-decoration: line-through;'>".$item['Descricao_Item']."</span> <br>
                        <span style='color: red; text-decoration: line-through;'><strong>Quantidade: </strong>".$item['Quantidade_Item']."</span>
                    </p>
                ";
            } else {
                // Exibe o item normalmente
                echo "
                    <hr>
                    <p>
                        <strong>".$item['Nome_Prato']."</strong>: <br>
                        ".$item['Descricao_Item']." <br>
                        <strong>Quantidade: </strong> ".$item['Quantidade_Item']."
                    </p>
                ";
            }
        }

        echo "
                        <a href='#' class='btn botao-entrega' data-pedido-id='".$pedidoID."'>Pronto</a>
                    </div>
                </div>
            </div>
        ";
    }
} else {
    echo "";
}

?>
