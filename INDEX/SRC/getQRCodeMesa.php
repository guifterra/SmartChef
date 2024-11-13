<?php
include('check.php');

// Recebe o ID da mesa via POST
$mesaId = $_POST['mesa_id'];

// Consulta a mesa pelo ID
$stmt = $con->prepare("SELECT NUMERO, EMPRESA_ID, TOKEN FROM PI22024.MESA WHERE NUMERO = ? AND EMPRESA_ID = ?");
$stmt->bind_param("ii", $mesaId, $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$result = $stmt->get_result();

if ($mesa = $result->fetch_assoc()) {
    // Gerar a URL para o cardápio com os parâmetros necessários
    $hospedagem = "http://localhost/";
    $nomeDaPastaDoProjeto = "SmartChef-main/";
    $endereco = "cardapio.html?SCempresaId=" . $mesa['EMPRESA_ID'] . "&SCtokenMesa=" . $mesa['TOKEN'] . "&SCnumeroMesa=" . $mesa['NUMERO'];

    $linkDoCardapio = $hospedagem . $nomeDaPastaDoProjeto . $endereco;
    $texto = urlencode($linkDoCardapio);

    // Tamanho do QR Code
    $tamanho = "225x225";

    // URL da API do QRCode
    $url_qr = "https://api.qrserver.com/v1/create-qr-code/?data=$texto&size=$tamanho";

    // Gerar o conteúdo do modal
    echo "
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='mesaModalLabel'>QR Code da Mesa {$mesa['NUMERO']}</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body d-flex justify-content-center'>
                    <img src='$url_qr' alt='QR Code da Mesa {$mesa['NUMERO']}' class='img-fluid' />
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    ";
}
?>
