<?php
include "connection.php";

// Configurações de upload
$maxSize = 5 * 1024 * 1024; // 5MB em bytes
$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'pratos_imagem'; // caminho absoluto para /pratos_imagem
$publicUploadDir = '../pratos_imagem'; // caminho público relativo usado para salvar no banco

// Garante que o diretório exista
if (!is_dir($uploadDir)) {
	mkdir($uploadDir, 0777, true);
}

// Dados do formulário
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$preco = isset($_POST['preco']) ? (float) $_POST['preco'] : 0.0;

// Primeiro insere o prato sem imagem para obter o ID
$stmt = $sql->prepare("INSERT INTO pratos (nome_prato, descricao, preco, imagem) VALUES (?, ?, ?, NULL)");
$stmt->bind_param("ssd", $nome, $descricao, $preco);
$stmt->execute();
$pratoId = $stmt->insert_id;
$stmt->close();

// Se houver arquivo enviado, processa upload
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
	$file = $_FILES['imagem'];

	// Verificações básicas
	if ($file['error'] !== UPLOAD_ERR_OK) {
		// Opcional: tratar mensagens por código de erro
	} else if ($file['size'] > $maxSize) {
		// Excede 5MB
		// Pode-se excluir o registro criado para manter consistência, mas aqui só não salva a imagem
	} else {
		// Validar tipo de arquivo por MIME e extensão
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
			// Fallback por extensão do nome original (não ideal, mas útil se MIME não for detectável)
			$originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			if (isset($allowed[$originalExt])) {
				$ext = $originalExt;
			} else {
				// Tipo não permitido
				$ext = false;
			}
		} else {
			// Monta nome: prato_(ID).ext
			$fileName = 'prato_' . $pratoId . '.' . $ext;
			$destPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
			$publicPath = $publicUploadDir . '/' . $fileName; // salva no banco esse caminho relativo

			if (move_uploaded_file($file['tmp_name'], $destPath)) {
				// Atualiza caminho da imagem no banco
				$upd = $sql->prepare("UPDATE pratos SET imagem = ? WHERE id = ?");
				$upd->bind_param("si", $publicPath, $pratoId);
				$upd->execute();
				$upd->close();
			}
		}
	}
}

header("Location: ../html/cadastrar_produto.html");
exit();
?>