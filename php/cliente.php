<?php
include "connection.php";

$nome  = $_POST['cliente'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$cpf   = $_POST['cpf'];

$dados = $sql->prepare("INSERT INTO cliente (nome, email, senha, cpf) VALUES (?, ?, ?, ?)");
$dados->bind_param("ssss", $nome, $email, $senha, $cpf);
$dados->execute();

header("Location: ../html/cadastro.html");
exit();
?>

