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
                <th colspan="2">Ações</th>
            </tr>
                <?php while($row = $result->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nome_prato']; ?></td>
                        <td><?php echo $row['descricao']; ?></td>
                        <td><?php echo $row['preco']; ?></td>
                        <td>
                            <?php if (!empty($row['imagem'])) { 
                                $imgSrc = $row['imagem'];
                                $base = basename($imgSrc);
                                $abs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pratos_imagem' . DIRECTORY_SEPARATOR . $base;
                                if (is_file($abs)) {
                                    $imgSrc .= '?v=' . filemtime($abs);
                                }
                            ?>
                                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Imagem do prato" style="max-width: 120px; height: auto;" />
                            <?php } else { echo '-'; } ?>
                        </td>
                        <td><a href="editar_prato.php?id=<?php echo $row['id']; ?>">Editar</a></td>
                        <td><a href="excluir_prato.php?id=<?php echo $row['id']; ?>">Excluir</a></td>
                    </tr>
                <?php } ?>
    </table>
    <?php } else {
                    echo "<div id='erro'>Nenhum prato cadastrado.</div>";
                } ?>
                </main>
                <button onclick="window.location.href='../index.html'">Voltar</button>
</body>
</html>