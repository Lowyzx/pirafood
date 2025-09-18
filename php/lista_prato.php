<?php
    include "connection.php";
    $result = $sql->query("SELECT * FROM pratos");

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
    <h1>Lista de Pratos</h1>
     <?php if($result->num_rows > 0) { ?>
    <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Imagem</th>
            </tr>
                <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nome_prato']; ?></td>
                        <td><?php echo $row['descricao']; ?></td>
                        <td><?php echo $row['preco']; ?></td>
                        <td>
                            <?php if (!empty($row['imagem'])) { ?>
                                <img src="<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem do prato" style="max-width: 120px; height: auto;" />
                            <?php } else { echo '-'; } ?>
                        </td>
                    </tr>
                <?php } ?>
    </table>
    <?php } else {
                    echo "<div id='erro'>Nenhum prato cadastrado.</div>";
                    echo "<button onclick=window.location.href='../index.html'>Voltar</button>";
                } ?>
                </main>
</body>
</html>