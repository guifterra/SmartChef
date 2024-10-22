<?php
include('conexao.php');

$empresaId = 1; 

$stmt = $con->prepare("
SELECT 
    NOME AS Nome_Prato,
    DESCRICAO AS Descricao_Prato,
    INGREDIENTES AS Ingredientes,
    ADICIONAIS AS Adicionais,
    PRECO AS Preco_Prato,
    CATEGORIA AS Nome_Categoria
FROM 
    PI22024.CARDAPIO
WHERE 
    EMPRESA_ID = ?
ORDER BY 
    CATEGORIA, NOME;
");

$stmt->bind_param("i", $empresaId);
$stmt->execute();
$stmt = $stmt->get_result();

$cardapioAgrupado = [];

// Agrupando os pratos por categoria
while ($prato = $stmt->fetch_assoc()) {
    $categoria = $prato['Nome_Categoria'];
    if (!isset($cardapioAgrupado[$categoria])) {
        $cardapioAgrupado[$categoria] = [];
    }
    $cardapioAgrupado[$categoria][] = $prato;
}

// Imprimindo os pratos agrupados por categoria
foreach ($cardapioAgrupado as $categoria => $pratos) {
    
    echo "<h2>$categoria</h2>"; // Título da categoria
    echo "<div class='categoria'>";

    foreach ($pratos as $prato) {
        // Transformar JSON dos ingredientes e adicionais em arrays
        $ingredientesArray = json_decode($prato['Ingredientes'], true);
        $adicionaisArray = json_decode($prato['Adicionais'], true);

        // Extrair apenas os nomes dos ingredientes
        $ingredientesNomes = array_column($ingredientesArray, 'nome');
        $ingredientesFormatados = implode(", ", $ingredientesNomes);

        echo "
            <div class='prato'>
                <h3>{$prato['Nome_Prato']}</h3>
                <p><strong>Descrição:</strong> {$prato['Descricao_Prato']}</p>
                <p><strong>Ingredientes:</strong> $ingredientesFormatados</p>
                <p><strong>Adicionais:</strong></p>
                <div class='adicionais'>
        ";

        // Exibindo os adicionais como checkboxes
        foreach ($adicionaisArray as $adicional) {
            echo "
                <label>
                    <input type='checkbox' name='adicionais[]' value='{$adicional['nome']}'>
                    {$adicional['nome']}
                </label><br>
            ";
        }

        echo "
                </div>
                <p><strong>Preço:</strong> R$ {$prato['Preco_Prato']}</p>
                <hr>
            </div>
        ";
    }

    echo "</div>"; // Fecha o div da categoria
}
?>
