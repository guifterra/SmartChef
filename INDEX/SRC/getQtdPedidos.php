<?php
include('check.php');

// 1. Consulta para buscar os usuários registrados na empresa (usando o EMPRESA_ID da cookie)
$stmt = $con->prepare("
    SELECT ID, STATUS
    FROM PI22024.PEDIDO
    
");

$stmt->execute();
$result = $stmt->get_result();

// Contadores
$totalItens = 0;
$statusGarcom = 0;
$statusCozinha = 0;
$statusCaixa = 0;

// Processar os resultados
while ($row = $result->fetch_assoc()) {
    $totalItens++;
    switch ($row['STATUS']) {
        case 'GARCOM':
            $statusGarcom++;
            break;
        case 'COZINHA':
            $statusCozinha++;
            break;
        case 'CAIXA':
            $statusCaixa++;
            break;
    }
}

// Gerar o HTML para os cards
echo '
<div class="card-container-pedidos">
    <div class="card-pedidos">
        <h3>' . $totalItens . '</h3>
        <p>Total Itens</p>
    </div>
    <div class="card-pedidos">
        <h3>' . $statusGarcom . '</h3>
        <p>Status Garçom</p>
    </div>
    <div class="card-pedidos">
        <h3>' . $statusCozinha . '</h3>
        <p>Status Cozinha</p>
    </div>
    <div class="card-pedidos">
        <h3>' . $statusCaixa . '</h3>
        <p>Status Caixa</p>
    </div>
</div>';
?>
