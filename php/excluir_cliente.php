<?php
include 'connection.php';
$id = $_GET['id'];

$sql->query("DELETE FROM clientes WHERE id = $id");
header("Location: lista_cliente.php");
exit();
?>