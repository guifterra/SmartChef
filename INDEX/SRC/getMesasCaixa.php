<?php
include('check.php');

// 1. Seleciona as mesas com pedidos no status "GARCOM"
$stmt = $con->prepare("
    SELECT DISTINCT MESA.ID AS Mesa_ID, MESA.NUMERO AS Numero_da_Mesa
    FROM PI22024.MESA
    LEFT JOIN PI22024.COMANDA ON MESA.ID = COMANDA.MESA_ID
    LEFT JOIN PI22024.PEDIDO ON COMANDA.ID = PEDIDO.COMANDA_ID
    WHERE MESA.EMPRESA_ID = ?
    ORDER BY MESA.NUMERO
");
$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$mesasResult = $stmt->get_result();

$mesas = [];
while ($mesaRow = $mesasResult->fetch_assoc()) {
    $mesas[$mesaRow['Mesa_ID']] = [
        'Numero_da_Mesa' => $mesaRow['Numero_da_Mesa'],
        'Pedidos' => [],
        'Total_Mesa' => 0 // Inicializa o total da mesa
    ];
}

// 2. Busca os pedidos de cada mesa com os itens e valores
$stmt = $con->prepare("
    SELECT 
        MESA.ID AS Mesa_ID,
        PEDIDO.ID AS Pedido_ID,
        PEDIDO.DATA AS Pedido_Data,
        CARDAPIO.NOME AS Nome_Prato,
        ITENS.DESCRICAO AS Descricao_Item,
        ITENS.QUANTIDADE AS Quantidade_Item,
        ITENS.PRECO AS Valor_Item
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
    WHERE 
        MESA.EMPRESA_ID = ?
    ORDER BY 
        MESA.NUMERO, PEDIDO.ID, CARDAPIO.NOME;
");
$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$pedidoResult = $stmt->get_result();

while ($pedido = $pedidoResult->fetch_assoc()) {
    $mesaID = $pedido['Mesa_ID'];
    $pedidoID = $pedido['Pedido_ID'];

    if (!isset($mesas[$mesaID]['Pedidos'][$pedidoID])) {
        $mesas[$mesaID]['Pedidos'][$pedidoID] = [
            'Pedido_Data' => $pedido['Pedido_Data'],
            'Itens' => [],
            'Total_Pedido' => 0
        ];
    }

    $mesas[$mesaID]['Pedidos'][$pedidoID]['Itens'][] = [
        'Nome_Prato' => $pedido['Nome_Prato'],
        'Descricao_Item' => $pedido['Descricao_Item'],
        'Quantidade_Item' => $pedido['Quantidade_Item'],
        'Valor_Item' => $pedido['Valor_Item']
    ];

    // Adiciona o valor do item ao total do pedido e ao total da mesa
    $mesas[$mesaID]['Pedidos'][$pedidoID]['Total_Pedido'] += $pedido['Valor_Item'] * $pedido['Quantidade_Item'];
    $mesas[$mesaID]['Total_Mesa'] += $pedido['Valor_Item'] * $pedido['Quantidade_Item'];
}

echo "<div class='container-grid'>";

// 3. Exibe as mesas e pedidos em formato HTML
foreach ($mesas as $mesaID => $mesa) {
    echo "
        <div class='card-mesa'>
            <div class='mesa'>
                <h3>MESA " . htmlspecialchars($mesa['Numero_da_Mesa']) . "</h3>
            </div>
            <div class='card-corpo'>
    ";

    // Exibe os pedidos da mesa
    foreach ($mesa['Pedidos'] as $pedidoID => $pedido) {
        echo "
            <div class='card-corpo-infos'>
                <div class='pedidos'>
                    <div class='titulo'>Pedido nº " . htmlspecialchars($pedidoID) . "</div>
                    <hr>
        ";
        
        foreach ($pedido['Itens'] as $item) {
            // Verifica se a quantidade é zero
            if ($item['Quantidade_Item'] == 0) {
                // Aplica estilo para quantidade zero
                echo "
                    <div class='linha'>
                        <span style='color: red; text-decoration: line-through;'>" . htmlspecialchars($item['Nome_Prato']) . " ( ".$item['Quantidade_Item']."x )</span>
                        <span style='color: red; text-decoration: line-through;'>R$" . number_format($item['Valor_Item'], 2, ',', '.') . "</span>
                    </div>
                ";
            } else {
                // Exibe normalmente se a quantidade for diferente de zero
                echo "
                    <div class='linha'>
                        <span>" . htmlspecialchars($item['Nome_Prato']) . " ( ".$item['Quantidade_Item']."x )</span>
                        <span>R$" . number_format($item['Valor_Item'], 2, ',', '.') . "</span>
                    </div>
                ";
            }
        }

        echo "
                </div>
            </div>
        ";
    }

    // Calcula o total da mesa, desconto e total a pagar
    $totalMesa = $mesa['Total_Mesa'];
    $desconto = 0; // Valor de desconto temporário
    $totalFinal = $totalMesa - $desconto;
    $totalPago = 0; // Valor pago temporário

    // Exibe os totais e botões para a mesa toda (apenas uma vez por mesa)
    echo "
                <hr>
                <div class='px-4'>
                    <div class='linha linha-total'><span>Total dos Itens</span><span>R$" . number_format($totalMesa, 2, ',', '.') . "</span></div>
                    <div class='linha linha-desconto'><span>Valor de Desconto</span><span>-R$" . number_format($desconto, 2, ',', '.') . "</span></div>
                    <div class='linha linha-final'><span>Total a pagar</span><span>R$" . number_format($totalFinal, 2, ',', '.') . "</span></div>
                    <div class='linha linha-final'><span>Total já pago</span><span>R$" . number_format($totalPago, 2, ',', '.') . "</span></div>
                </div>
                <div class='botoes-container'>
                    <button class='botao cancelar botao-cancelar-caixa' data-bs-toggle='modal' data-bs-target='#pedtModalCaixa' data-idDoRes='{$_COOKIE['EMPRESA_ID']}' data-idDaMesa='{$mesaID}'>Cancelar item</button>
                    <button class='botao subtrair'>Pagamento</button>
                    <button class='botao subtrair'>Desconto</button>
                    <button class='botao finalizar'>Finalizar comanda</button>
                </div>
            </div>
        </div>
    ";
}

echo "</div>";
?>
