<?php
include 'connection.php';
$id = $_GET['id'];
$dados = $sql->query("SELECT * FROM pratos WHERE id = $id")->fetch_assoc();
unlink($dados['imagem']);
$sql->query("DELETE FROM pratos WHERE id = $id");
header("Location: lista_prato.php");
exit();
?>