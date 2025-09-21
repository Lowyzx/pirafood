<?php
include "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['cliente'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cpf = $_POST['cpf'];

    $sql->query("UPDATE cliente SET nome='$nome', email='$email', senha='$senha', cpf='$cpf' WHERE id=$id");
    header("Location: lista_cliente.php");
    exit();
}else{
    $id = $_GET['id'];
    $result = $sql->query("SELECT * FROM cliente WHERE id = $id");
    $cliente = $result->fetch_assoc();
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PiraFood - Cadastro</title>
    <link rel="stylesheet" href="../css/cadastro.css">

</head>
<body>
      
           
<main>
<a href="../index.html"><button class="voltar">Voltar</button></a>

</main>
<section>
    <h1>Edite seu Cadastro</h1>
    <form action="editar_cliente.php" method="post">
<input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
<input id="campos" type="text" name="cliente" placeholder="Nome Completo..." value="<?php echo $cliente['nome']; ?>">
<input id="campos" type="email" name="email" placeholder="Digite seu Email..." value="<?php echo $cliente['email']; ?>">
<input id="campos" type="password" name="senha" placeholder="Digite sua Senha..." value="<?php echo $cliente['senha']; ?>">
<input id="campos" type="password" placeholder="Confirme sua Senha..." value="<?php echo $cliente['senha']; ?>">
<input id="campos"  type="number" name="cpf" placeholder="Digite seu CPF..." value="<?php echo $cliente['cpf']; ?>">
<br>
<input type="radio" id="checkzin"> <label for="checkzin">Eu Aceito os Termos e Condições</label>
<br><br><br><br><br>
<button type="submit">Cadastrar-se</button>

    </form>
</section>
</body>
</html>