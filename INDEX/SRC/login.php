<?php
include("conexao.php");

if (isset($_POST["user"]) && isset($_POST["password"])) {
    $username = $_POST["user"];
    $email = $username;
    $password = $_POST["password"];

    if ($username == "" || $password == "") {
        die(header("HTTP/1.0 401 Preencha todos os campos do formulario!"));
    }

    $padrao = '/^[A-Za-z_.@-]+$/';

    if (preg_match($padrao, $username)) {
        $username =  strtoupper($username);
    } else {
        die(header("HTTP/1.0 401 O nome de usuario apresenta caracteres invalidos!"));
    }

    /*
    $padrao = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (preg_match($padrao, $password)) {
        echo "Senha forte.";
    } else {
        // Se não corresponder, retorna uma mensagem de erro
        die(header("HTTP/1.0 400 Senha não atende aos requisitos de segurança."));
    }
    */

    $stmt = $con->prepare("SELECT EMPRESA_ID, ID, TOKEN, `KEY`, FUNCAO, SENHA FROM USER WHERE USERNAME = ? OR EMAIL = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Check password
    if ($user && password_verify($password, $user['SENHA'])) {
        setcookie("EMPRESA_ID", $user['EMPRESA_ID'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("ID", $user['ID'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("TOKEN", $user['TOKEN'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("KEY", $user['KEY'], time() + (10 * 365 * 24 * 60 * 60));
        setcookie("FUNCAO", $user['FUNCAO'], time() + (10 * 365 * 24 * 60 * 60));
        return true;
    } else {
        if (!password_verify($password, $user['SENHA']))
        die(header("HTTP/1.0 401 Usuario ou Senha incorreto"));
    }
} else {
    die(header("HTTP/1.0 401 Formulario de autenticacao invalido"));
}