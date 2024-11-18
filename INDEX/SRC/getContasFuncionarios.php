<?php
include('check.php');

// 1. Consulta para buscar os usuários registrados na empresa (usando o EMPRESA_ID da cookie)
$stmt = $con->prepare("
    SELECT ID, USERNAME, FUNCAO, VALIDO
    FROM PI22024.USER
    WHERE EMPRESA_ID = ?
");

$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se há usuários
if ($result->num_rows > 0) {
    echo "<table class='table table-striped tabela-func'>";
    echo "<thead>";
    echo "<tr class='tabela-func-content'>";
    echo "<th scope='col'>Nome de Usuário</th>";
    echo "<th scope='col'>Função</th>";
    echo "<th scope='col'>Ação</th>"; // Nova coluna para o ícone de edição
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Exibe os usuários encontrados
    while ($row = $result->fetch_assoc()) {
        $userID = $row['ID'];
        $username = htmlspecialchars($row['USERNAME']);
        $funcao = htmlspecialchars($row['FUNCAO']);
        $status = $row['VALIDO'] == 1 ? 'Ativo' : 'Inativo';

        echo "<tr>";
        echo "<td>$username</td>";
        echo "<td>$funcao</td>";
        echo "<td class='editar'>
                <a href='#' data-toggle='modal' data-target='#editModal$userID'>
                    <i class='bx bxs-edit-alt'></i>
                </a>
              </td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>Nenhum usuário encontrado para a empresa selecionada.</p>";
}
?>
