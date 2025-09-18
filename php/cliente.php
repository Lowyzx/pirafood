<?php
include "connection.php";

$nome  = $_POST['cliente'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$cpf   = $_POST['cpf'];

$sql->query("INSERT INTO cliente(nome, email, senha, cpf) VALUES('$nome', '$email', '$senha', '$cpf')");

echo "Dados salvos com sucesso!!";
?>

