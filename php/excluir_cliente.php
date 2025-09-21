<?php
include 'connection.php';
$id = $_GET['id'];

$sql->query("DELETE FROM cliente WHERE id = $id");
header("Location: lista_cliente.php");
exit();
?>