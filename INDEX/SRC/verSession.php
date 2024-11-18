<?php
session_start();

if (!isset($_SESSION['SCitems']) || empty($_SESSION['SCitems'])) {
    echo "<p>Carrinho vazio</p>";
} else {
    echo "<h3>Itens no Carrinho:</h3>";
    echo "<ul>";
    foreach ($_SESSION['SCitems'] as $item) {
        echo "<li>";
        echo "<strong>Quantidade:</strong> " . htmlspecialchars($item['quantidade']) . "<br>";
        echo "<strong>Observação:</strong> " . htmlspecialchars($item['observacao']) . "<br>";
        echo "<strong>Preço:</strong> R$ " . number_format($item['preco'], 2, ',', '.') . "<br>";

        // Imprime os alérgenos, se houver
        if (!empty($item['alergicos'])) {
            echo "<strong>Alergicos:</strong> " . implode(", ", $item['alergicos']) . "<br>";
        } else {
            echo "<strong>Alergicos:</strong> Nenhum<br>";
        }

        // Imprime os adicionais, se houver
        if (!empty($item['adicionais'])) {
            echo "<strong>Adicionais:</strong> " . implode(", ", $item['adicionais']) . "<br>";
        } else {
            echo "<strong>Adicionais:</strong> Nenhum<br>";
        }

        echo "</li><hr>";
    }
    echo "</ul>";
}
?>
