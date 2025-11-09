<?php

$localhost = 'localhost';
$user = 'root';
$password = ''; 
$database = 'crud_mundo';

$mysqli = new mysqli($localhost, $user, $password, $database);

if ($mysqli->connect_errno) {
    die("Falha na conexão com o banco: " . $mysqli->connect_error);
}

if (!$mysqli->set_charset("utf8mb4")) {
    die("Erro ao carregar conjunto de caracteres utf8mb4: " . $mysqli->error);
}
?>