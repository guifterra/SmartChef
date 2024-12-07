<?php
include('check.php');

// Obter o ID do usuário e a nova senha
$userId = $_POST['userId'];
$newPassword = $_POST['newPassword'];

// Validação da senha
$padrao = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

if (!preg_match($padrao, $newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.'
    ]);
    exit;
}

// Criptografa a nova senha
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Atualiza a senha no banco de dados
$stmt = $con->prepare("UPDATE PI22024.USER SET SENHA = ? WHERE ID = ?");
$stmt->bind_param("si", $hashedPassword, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log("Erro ao alterar a senha: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar a senha do usuário.']);
}
?>
