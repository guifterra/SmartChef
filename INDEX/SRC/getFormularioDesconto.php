<?php
include('check.php');

// Recebe o ID da mesa via POST
$mesaId = $_POST['mesa_id'];
$idEmpresa = $_COOKIE["EMPRESA_ID"];

echo "
    <form id='formulario-de-desconto'>
        <p>Entre com o valor de desconto (R$):</p>
        <p>
            <input type='number' step='0.50' name='valorDoDesconto' value='0,00'>
            <input type='hidden' name='idDaMesa' value='{$mesaId}'>
            <input type='hidden' name='idDaEmpresa' value='{$idEmpresa}'>
        </p>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
        <button type='button' class='btn btn-primary botao-fazer-desconto' data-form-id='formulario-de-desconto'>Adicionar Desconto</button>
    </form>
";

?>