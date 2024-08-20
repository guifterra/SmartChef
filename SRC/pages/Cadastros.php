<?php
include("./check.php");
$stmt = $con->prepare("SELECT USERNAME FROM USER WHERE EMPRESA_ID = ? and VALIDO = 0");
$stmt->bind_param("i", $_COOKIE["EMPRESA_ID"]);
$stmt->execute();
$stmt = $stmt->get_result();
session_start();
?>
<style>
#cadastro {
    width: 100%;
    display: flex;
    flex-direction: column;
}

form {
    width: min-content;
    align-self: center;
}

div .form {
    display: flex;
    justify-content: space-between;
    flex-direction: row;
}

label {
    color: white;
}

form button {
    width: 100%;
}

label,
input,
form button {
    font-size: 20pt
}
</style>
<div id="cadastro">
    <h1>DEFINIR SENHAS:</h1>
    <br>
    <form method="POST" id="senhas">
        <?php
        $array = array();
        while ($USERNAME = ($stmt->fetch_assoc())) {
            $USERNAME = $USERNAME['USERNAME'];
            $array[] = $USERNAME;
            echo "
            <div class='form'>
                <label>
                    $USERNAME&nbsp:
                </label>
            <input type='password' name='$USERNAME' required>
            </div>
            <br>
            ";
        }
        $_SESSION['USERS'] = $array;
        ?>
        <button>Enviar</button>
    </form>
</div>
<script>
$('#senhas').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        type: 'post',
        url: 'SRC/senhas.php',
        data: $('#senhas').serialize(),
        success: function(data) {
            location.href = './';
        },
        error: function(error) {
            console.log(error);
            Swal.fire({
                title: 'Oops!',
                text: error.statusText,
                icon: 'error',
                confirmButtonText: 'Tentar novamente'
            })
        }
    });
});
</script>