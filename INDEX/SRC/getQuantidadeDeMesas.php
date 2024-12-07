<?php
include('check.php'); // Inclui a conexão com o banco de dados

// Habilitar exibição de erros no PHP para fins de depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtém o ID da empresa através do cookie
$empresaId = $_COOKIE["EMPRESA_ID"];

// Verifica se o ID da empresa está disponível
if (!isset($empresaId)) {
    echo "Erro: ID da empresa não encontrado.";
    exit;
}

// Consulta para contar o número de mesas associadas à empresa
$sqlMesas = "SELECT COUNT(*) AS total FROM MESA WHERE EMPRESA_ID = ?";
$stmtMesas = $con->prepare($sqlMesas);
$stmtMesas->bind_param("i", $empresaId);
$stmtMesas->execute();
$resultMesas = $stmtMesas->get_result();
$mesas = $resultMesas->fetch_assoc()['total'];

// Exibe a quantidade de mesas em HTML simples
echo "<h4 class='mb-3'>Quantidade de mesas: " . $mesas . "</h4>";
?>
