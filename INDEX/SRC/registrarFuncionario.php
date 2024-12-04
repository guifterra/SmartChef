<?php
include("conexao.php");

if (isset($_POST["username"]) && isset($_POST["funcao"]) && isset($_POST["pass"]) && isset($_POST["word"])) {

    $username = $_POST["username"];
    $funcao = $_POST["funcao"];
    $password = $_POST["pass"];
    $word = $_POST["word"];
    $empresaId = $_COOKIE["EMPRESA_ID"];

    if ($username == "" || $funcao == "" || $password == "" || $word == "") {
        die(header("HTTP/1.0 401 Preencha todos os campos do formulario"));
    }

    $padrao = '/^[A-Za-z_-]+$/';

    if (preg_match($padrao, $username)) {
        $username =  strtoupper($username);
    } else {
        die(header("HTTP/1.0 401 O nome de usuario apresenta caracteres invalidos"));
    }

    
    $padrao = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (!preg_match($padrao, $password)) {
        die(header("HTTP/1.0 401 Senha não atende aos requisitos de segurança."));
    }
    

    $checkuser = $con->prepare("SELECT ID FROM PI22024.USER WHERE USERNAME = ?");
    $checkuser->bind_param("s", $username);
    $checkuser->execute();
    $count = $checkuser->get_result()->num_rows;
    if ($count > 0) {
        die(header("HTTP/1.0 Nome de usuario ja existente"));
    }

    if ($password != $word) {
        die(header("HTTP/1.0 401 As senhas nao coincidem"));
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $token = bin2hex(openssl_random_pseudo_bytes(20));
    $key = rand(100000000, 999999999);

    $stmt = $con->prepare("INSERT INTO USER (`ID`,`EMPRESA_ID`, `USERNAME`, `SENHA`, `FUNCAO`, `TOKEN`, `KEY`, `VALIDO`) VALUES (0, ?, ?, ?, ?, ?, ?, '1')");
    $tempusername = $username;
    $stmt->bind_param("issssi", $empresaId, $tempusername,$password, $funcao, $token, $key);
    $stmt->execute();

    

} else {
    die(header("HTTP/1.0 401 Formulario de autenticacao invalido"));
}
