<?php
$con = mysqli_connect('localhost', 'root', '1234');

if (!$con) {
    echo "Falha ao conectar com a base de dados: " . mysqli_connect_error();
    exit();
}

$sql = file_get_contents('sql.sql');

$queries = explode(';', $sql);
foreach ($queries as $query) {
    $query = trim($query); 
    if (!empty($query)) {
        $con->query($query);
    }
}

$con->close();
