<?php
include('check.php');
if ($_COOKIE['FUNCAO'] != 'ADM') {
    die(header("HTTP/1.0 401 PERMICAO NEGADA"));
}
session_start();
if (!isset($_SESSION['USERS'][0])) {
    die(header("HTTP/1.0 401 ERRO"));
} else {
    $password = $_SESSION['USERS'][0];
    $password = str_replace('.', '_', $password);
    if (!isset($_POST[$password])) {
        die(header("HTTP/1.0 401 ERRO"));
    }
}

// for ($i = 0; $i < count($_SESSION['USERS']); $i++) {
//     $password = $_SESSION['USERS'][$i];
//     $password = str_replace('.', '_', $password);
//     $password = $_POST[$password];
//     $user = $_SESSION['USERS'][$i];
//     echo strlen($password) . "<br>";
//     if (strlen($password) < 8) {
//         die(header("HTTP/1.0 401 SENHA DO $user MUITO CURTA"));
//     }
// }

for ($i = 0; $i < count($_SESSION['USERS']); $i++) {
    $password = $_SESSION['USERS'][$i];
    $password = str_replace('.', '_', $password);
    $password = $_POST[$password];
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $con->prepare("UPDATE `USER` SET `SENHA` = ?, `VALIDO` = '1' WHERE (`USERNAME` = ?)");
    $stmt->bind_param("ss", $password, $_SESSION['USERS'][$i]);
    $stmt->execute();
}

session_unset();
session_destroy();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 1, '/');
}

// UPDATE `PI22024`.`USER` SET `SENHA` = ?, `VALIDO` = '1' WHERE (`USERNAME` = ?);