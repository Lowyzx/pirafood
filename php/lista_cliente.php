<?php
    include "connection.php";
    $result = $sql->query("SELECT * FROM cliente");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/lista.css">
</head>
<body>
    <main>
    <h1>Lista de Clientes</h1>
     <?php if($result->num_rows > 0) { ?>
    <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Senha</th>
                <th>CPF</th>
                <th colspan="2">Ações</th>
            </tr>
                <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['senha']; ?></td>
                        <td><?php echo $row['cpf']; ?></td>
                        <td><a href="editar_cliente.php?id=<?php echo $row['id']?>">Editar</a></td>
                        <td><a href="excluir_cliente.php?id=<?php echo $row['id']?>">Excluir</a></td>
                    </tr>
                <?php } ?>
    </table>
    <?php } else {
                    echo "<div id='erro'>Nenhum cliente cadastrado.</div>";
                } ?>
                </main>
                <button onclick="window.location.href='../index.html'">Voltar</button>
</body>
</html>