<?php
include('check.php');

// Consulta para buscar os usuários registrados na empresa
$stmt = $con->prepare("
    SELECT ID, USERNAME, FUNCAO, VALIDO
    FROM PI22024.USER
    WHERE EMPRESA_ID = ?
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table class='table table-striped tabela-func text-center'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nome de Usuário</th>";
    echo "<th>Função</th>";
    echo "<th>Status</th>";
    echo "<th>Ações</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        $userID = $row['ID'];
        $username = htmlspecialchars($row['USERNAME']);
        $funcao = htmlspecialchars($row['FUNCAO']);
        $status = $row['VALIDO'] == 1 ? 'Ativo' : 'Inativo';

        echo "<tr>";
        echo "<td>$username</td>";
        echo "<td>$funcao</td>";
        echo "<td id='status_$userID'>$status</td>";
        echo "<td class='d-flex justify-content-around'>";

        // Botão Toggle Status (não aparece para ADM)
        if ($funcao !== 'ADM') {
            echo "<button class='btn btn-warning toggle-status' data-user-id='$userID' data-status='{$row['VALIDO']}'>Ativar/Desativar</button>";
        }

        // Botão Alterar Senha
        echo "<button class='btn btn-primary' data-toggle='modal' data-target='#changePasswordModal$userID'>Alterar Senha</button>";
        echo "</td>";
        echo "</tr>";

        // Modal para alterar senha
        echo "
        <div class='modal fade' id='changePasswordModal$userID' tabindex='-1' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Alterar Senha de $username</h5>
                    </div>
                    <div class='modal-body'>
                        <input type='password' class='form-control' id='newPassword$userID' placeholder='Nova senha'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                        <button type='button' class='btn btn-primary save-password' data-user-id='$userID'>Salvar</button>
                    </div>
                </div>
            </div>
        </div>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p class='text-center'>Nenhum usuário encontrado para a empresa selecionada.</p>";
}
?>
