<?php
include("check.php");

if ($_COOKIE['FUNCAO'] == 'ADM') {
    $stmt = $con->prepare("SELECT VALIDO FROM USER WHERE EMPRESA_ID = ?");
    $stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
    $stmt->execute();
    $stmt = $stmt->get_result();
    while ($while = $stmt->fetch_assoc()) {
        $valido = $while['VALIDO'];
        if ($valido == 0) break;
    }
    if ($valido == 1) include("pages/UserADM.html");
    if ($valido == 0) include("pages/Cadastros.php");
} elseif ($_COOKIE['FUNCAO'] == 'COZINHA') {
    include("pages/UserCOZINHA.html");
} elseif ($_COOKIE['FUNCAO'] == 'GARCOM') {
    include("pages/UserGARCOM.html");
} elseif ($_COOKIE['FUNCAO'] == 'CAIXA') {
    include("pages/UserCAIXA.html");
} else {
    include("logout.php");
}
