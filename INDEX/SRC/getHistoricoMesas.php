<?php
include('check.php');

// Obtém o ID da empresa
$empresaId = $_COOKIE["EMPRESA_ID"];
if (!isset($empresaId)) {
    echo "Erro: ID da empresa não encontrado.";
    exit;
}

// Filtros recebidos via GET
$mesa = isset($_GET['mesa']) && $_GET['mesa'] !== '' ? intval($_GET['mesa']) : null;
$dia = isset($_GET['dia']) && $_GET['dia'] !== '' ? intval($_GET['dia']) : null;
$mes = isset($_GET['mes']) && $_GET['mes'] !== '' ? intval($_GET['mes']) : null;
$ano = isset($_GET['ano']) && $_GET['ano'] !== '' ? intval($_GET['ano']) : null;

// Inicia a consulta base
$sql = "SELECT * FROM HISTORICO WHERE EMPRESA_ID = ?";
$params = [$empresaId];

// Construção dinâmica da consulta com base nos filtros
if ($mesa) {
    $sql .= " AND JSON_EXTRACT(HISTORICO, '$.Comanda.MesaID') = ?";
    $params[] = $mesa;
}
if ($dia) {
    $sql .= " AND DAY(DATA) = ?";
    $params[] = $dia;
}
if ($mes) {
    $sql .= " AND MONTH(DATA) = ?";
    $params[] = $mes;
}
if ($ano) {
    $sql .= " AND YEAR(DATA) = ?";
    $params[] = $ano;
}

// Se não houver filtros, exibe o último histórico de cada mesa
if (!$mesa && !$dia && !$mes && !$ano) {
    $sql = "
        SELECT * FROM (
            SELECT *, ROW_NUMBER() OVER (
                PARTITION BY JSON_EXTRACT(HISTORICO, '$.Comanda.MesaID')
                ORDER BY DATA DESC
            ) AS rank
            FROM HISTORICO
            WHERE EMPRESA_ID = ?
        ) AS t
        WHERE rank = 1";
    $params = [$empresaId];
}

// Prepara a consulta
$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("");
}

// Bind dos parâmetros dinamicamente
if (count($params) > 1) {
    $types = str_repeat('i', count($params));
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('i', $params[0]);
}

// Executa a consulta
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="historico">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php $historico = json_decode($row['HISTORICO'], true); ?>
            <div class="historico-item">
                <h5>Data: <?= htmlspecialchars($row['DATA']); ?> - Mesa <?= htmlspecialchars($historico['Comanda']['MesaID']); ?></h5>
                <ul class="pedido-lista">
                    <?php foreach ($historico['Pedidos'] as $pedido): ?>
                        <li class="pedido">
                            <strong>Pedido </strong> <?= htmlspecialchars($pedido['PedidoID']); ?>
                            <ul class="itens-lista">
                                <?php foreach ($pedido['Itens'] as $item): ?>
                                    <li class="item">
                                        <strong><?= htmlspecialchars($item['Nome']); ?></strong> - R$ <?= number_format($item['Preco'], 2, ',', '.'); ?><br>
                                        <strong>Quantidade:</strong> <?= htmlspecialchars($item['Quantidade']); ?><br>
                                        <?php if (!empty($item['Descricao'])): ?>
                                            <strong>Descrição:</strong> <?= nl2br(html_entity_decode($item['Descricao'])); ?><br>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="sem-historico">Nenhum histórico encontrado para os filtros selecionados.</p>
    <?php endif; ?>
</div>

<style>
    .historico {
        font-family: Arial, sans-serif;
        margin-top: 20px;
    }

    .historico-item {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .historico-item h5 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #333;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .pedido-lista {
        list-style: none;
        padding-left: 0;
        margin: 10px 0;
    }

    .pedido {
        margin-bottom: 15px;
    }

    .pedido strong {
        color: #007BFF;
    }

    .itens-lista {
        list-style: none;
        padding-left: 20px;
    }

    .item {
        margin-bottom: 10px;
    }

    .item strong {
        font-weight: bold;
        color: #333;
    }

    .sem-historico {
        font-size: 16px;
        color: #666;
        margin-top: 10px;
    }
</style>
