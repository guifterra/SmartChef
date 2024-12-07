<?php
include("conexao.php");

if (isset($_COOKIE["EMPRESA_ID"]) && isset($_COOKIE["ID"]) && isset($_COOKIE["TOKEN"]) && isset($_COOKIE["KEY"]) && isset($_COOKIE["FUNCAO"])) {
    // Consulta para verificar os dados do usuário
    $stmt = $con->prepare("SELECT EMPRESA_ID, FUNCAO, VALIDO FROM USER WHERE (ID = ? AND TOKEN LIKE ? AND `KEY` = ?) LIMIT 1");
    $stmt->bind_param("isi", $_COOKIE["ID"], $_COOKIE["TOKEN"], $_COOKIE["KEY"]);
    $stmt->execute();
    $login = $stmt->get_result()->fetch_assoc();

    // Verifica se o usuário existe
    if (!$login) {
        // Redireciona para logout se o usuário não for encontrado
        die("<script>location.href = 'SRC/logout.php';</script>");
    } else {
        // Verifica se o usuário está inativo
        if ($login['VALIDO'] == 0) {
            // Redireciona para logout se o usuário não for válido
            die("<script>location.href = 'SRC/logout.php';</script>");
        }

        // Atualiza os cookies com novos valores e renova o prazo
        setcookie("EMPRESA_ID", $_COOKIE['EMPRESA_ID'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("ID", $_COOKIE['ID'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("TOKEN", $_COOKIE['TOKEN'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("KEY", $_COOKIE['KEY'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("FUNCAO", $login['FUNCAO'], time() + (10 * 365 * 24 * 60 * 60));
    }
} else {
    // Redireciona para logout se algum cookie necessário estiver ausente
    die("<script>location.href = 'SRC/logout.php';</script>");
}
