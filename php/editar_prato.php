<?php
include "connection.php";

// Config de upload (mesma base do prato.php)
$maxSize = 5 * 1024 * 1024; // 5MB
$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pratos_imagem';
$publicUploadDir = '../pratos_imagem';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $preco = isset($_POST['preco']) ? (float) $_POST['preco'] : 0.0;

    // Atualiza campos básicos com prepared statement
    $upd = $sql->prepare("UPDATE pratos SET nome_prato = ?, descricao = ?, preco = ? WHERE id = ?");
    $upd->bind_param("ssdi", $nome, $descricao, $preco, $id);
    $upd->execute();
    $upd->close();

    // Se veio arquivo novo, processa upload e atualiza imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['imagem'];

        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= $maxSize) {
            // Descobre MIME/Extensão
            $mime = null;
            if (class_exists('finfo')) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
            } else {
                $imgInfo = @getimagesize($file['tmp_name']);
                if ($imgInfo && isset($imgInfo['mime'])) {
                    $mime = $imgInfo['mime'];
                }
            }

            $allowed = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ];

            $ext = $mime ? array_search($mime, $allowed, true) : false;
            if ($ext === false) {
                // Fallback por extensão original
                $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (isset($allowed[$originalExt])) {
                    $ext = $originalExt;
                }
            }

            if ($ext !== false) {
                $fileName = 'prato_' . $id . '.' . $ext;
                $destPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                $publicPath = $publicUploadDir . '/' . $fileName;

                // Buscar imagem antiga para remover se necessário
                $stmtImg = $sql->prepare("SELECT imagem FROM pratos WHERE id = ?");
                $stmtImg->bind_param("i", $id);
                $stmtImg->execute();
                $resImg = $stmtImg->get_result();
                $rowImg = $resImg ? $resImg->fetch_assoc() : null;
                $oldPublicPath = $rowImg && !empty($rowImg['imagem']) ? $rowImg['imagem'] : null;
                $stmtImg->close();

                // Move upload
                if (move_uploaded_file($file['tmp_name'], $destPath)) {
                    // Atualiza caminho público no banco
                    $updImg = $sql->prepare("UPDATE pratos SET imagem = ? WHERE id = ?");
                    $updImg->bind_param("si", $publicPath, $id);
                    $updImg->execute();
                    $updImg->close();

                    // Remove arquivo antigo se o nome mudou (ext diferente) e se existir
                    if ($oldPublicPath) {
                        $oldBase = basename($oldPublicPath);
                        $newBase = basename($publicPath);
                        if ($oldBase !== $newBase) {
                            $absOld = $uploadDir . DIRECTORY_SEPARATOR . $oldBase;
                            if (is_file($absOld)) {
                                @unlink($absOld);
                            }
                        }
                    }
                }
            }
        }
    }

    header("Location: lista_prato.php");
    exit();
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $result = $sql->query("SELECT * FROM pratos WHERE id = $id");
    $prato = $result ? $result->fetch_assoc() : null;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Prato</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: orangered;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }
        input, textarea, button {
            width: 100%;
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .back-button {
            background-color: #dc3545;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Prato</h2>
        <?php if ($prato) { ?>
        <form action="editar_prato.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($prato['id']); ?>">

            <label for="nome">Nome do Prato:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($prato['nome_prato']); ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($prato['descricao']); ?></textarea>

            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" value="<?php echo htmlspecialchars($prato['preco']); ?>" required>

            <label>Imagem atual:</label>
            <div>
                <?php if (!empty($prato['imagem'])) { 
                    $imgSrc = $prato['imagem'];
                    $base = basename($imgSrc);
                    $abs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pratos_imagem' . DIRECTORY_SEPARATOR . $base;
                    if (is_file($abs)) {
                        $imgSrc .= '?v=' . filemtime($abs);
                    }
                ?>
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Imagem do prato" style="max-width: 200px; height: auto;" />
                <?php } else { ?>
                    <em>Sem imagem</em>
                <?php } ?>
            </div>

            <label for="imagem">Nova imagem (opcional):</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">
            <small>Tamanho máximo: 5 MB. Deixe em branco para manter a atual.</small>

            <button type="submit">Salvar Alterações</button>
        </form>
        <?php } else { ?>
            <p>Prato não encontrado.</p>
        <?php } ?>
        <button class="back-button" onclick="window.location.href='lista_prato.php'">Voltar para a Lista</button>
    </div>
</body>
</html>
