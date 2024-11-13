<?php
include('conexao.php'); // Inclui a conexão com o banco de dados

// Dados recebidos via GET
$empresaId = $_GET['SCempresaId'];
$tokenMesa = $_GET['SCtokenMesa'];
$numeroMesa = $_GET['SCnumeroMesa'];

// Consulta SQL para verificar se a mesa existe e o token está correto
$stmt = $con->prepare("
    SELECT 1 
    FROM MESA
    WHERE EMPRESA_ID = ? AND NUMERO = ? AND TOKEN = ?
");
$stmt->bind_param("iis", $empresaId, $numeroMesa, $tokenMesa);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se a consulta retornou resultados
if ($result->num_rows > 0) {
    // Gera o link do cardápio se as condições forem atendidas
    $hospedagem = "http://localhost/";
    $nomeDaPastaDoProjeto = "SmartChef-main/";
    $endereco = "cardapio.html?SCempresaId=" . $empresaId . "&SCtokenMesa=" . $tokenMesa . "&SCnumeroMesa=" . $numeroMesa;
    $linkDoCardapio = $hospedagem . $nomeDaPastaDoProjeto . $endereco;

    // Codifica o link para URL
    $texto = urlencode($linkDoCardapio);

    // Tamanho do QR Code
    $tamanho = "225x225";

    // URL da API do QRCode
    $url_qr = "https://api.qrserver.com/v1/create-qr-code/?data=$texto&size=$tamanho";

    // Exibindo o QR code na página
    echo "<img src='$url_qr' alt='QR Code' class='img-fluid' />";
} else {
    // Retorna 404 se as condições não forem atendidas
    http_response_code(404);
}
?>
