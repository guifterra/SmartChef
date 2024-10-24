<?php

$hospedagem = "https://localhost/";
$nomeDaPastaDoProjeto = "PI-Dev-Version/";
$endereco = "cardapio.html?SCempresaId=".$_GET['SCempresaId']."&SCtokenMesa=".$_GET['SCtokenMesa']."&SCnumeroMesa=".$_GET['SCnumeroMesa']."";

$linkDoCardapio = $hospedagem . $nomeDaPastaDoProjeto . $endereco;

$texto = urlencode($linkDoCardapio);

// Tamanho do QR Code
$tamanho = "225x225";

// URL da API do QRCode
$url_qr = "https://api.qrserver.com/v1/create-qr-code/?data=$texto&size=$tamanho";

// Exibindo o QR code na página
echo "<img src='$url_qr' alt='QR Code' class='img-fluid' />";

?>
