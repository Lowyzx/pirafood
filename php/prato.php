<?php
include "connection.php";

$nome = $_POST["nome"];
$descricao = $_POST["descricao"];
$preco = $_POST["preco"];

$sql->query("INSERT INTO pratos(nome_prato, descricao, preco) VALUES('$nome', '$descricao', '$preco')");

echo "Dados salvos com sucesso!!";
?>