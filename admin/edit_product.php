<?php

include_once __DIR__ . '/../config/init.php';

$connect = connectBanco();


// 1. Busca APENAS as categorias, removendo raridades e universos
$categorias_query = false;
if ($connect) {
    $categorias_sql = "SELECT idCategoria, nome FROM categoriaproduto ORDER BY nome ASC";
    $categorias_query = mysqli_query($connect, $categorias_sql);
}

// --- 2. Lógica para buscar o produto a ser editado ---
if (!isset($_GET['id'])) {
    $_SESSION['notificacao'] = [
        'tipo' => 'danger',
        'mensagem' => 'ID do produto não fornecido.'
    ];
    header("Location: product_management.php");
    exit();
}

$idProduto = intval($_GET['id']); // Usando idProduto para consistência

// Consulta o produto específico usando a tabela 'produto' e a coluna 'idProduto'
$query = $connect->prepare("SELECT * FROM produto WHERE idProduto = ?"); 
$query->bind_param("i", $idProduto);
$query->execute();
$result = $query->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['notificacao'] = [
        'tipo' => 'danger',
        'mensagem' => 'Produto não encontrado.'
    ];
    header("Location: gerenciar_produtos.php");
    exit();
}

$produto = $result->fetch_assoc(); // Dados do produto carregados

// --- 3. Lógica para processar a atualização do produto ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nomeProduto        = trim($_POST['nomeProduto']);
    $descricaoProduto   = trim($_POST['descricaoProduto']);
    $precoProduto       = floatval($_POST['precoProduto']);
    $estoqueProduto     = intval($_POST['estoqueProduto']);
    $idCategoria        = intval($_POST['idCategoria']);

    $imagem = $produto['imagem']; // Mantém a imagem antiga se não for enviada uma nova

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $diretorioUploads = __DIR__ . '/../images/';
        if (!is_dir($diretorioUploads)) {
            mkdir($diretorioUploads, 0777, true);
        }

        $nomeArquivoOriginal = basename($_FILES['imagem']['name']);
        $extensao = pathinfo($nomeArquivoOriginal, PATHINFO_EXTENSION);
        $nomeUnico = uniqid() . '.' . $extensao;
        $caminhoCompleto = $diretorioUploads . $nomeUnico;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            // Se a imagem antiga existe e é diferente da nova, apaga a antiga
            if ($produto['imagem'] && $produto['imagem'] !== $nomeUnico && file_exists($diretorioUploads . $produto['imagem'])) {
                unlink($diretorioUploads . $produto['imagem']);
            }
            $imagem = $nomeUnico;
        } else {
            $_SESSION['notificacao'] = [
                'tipo' => 'warning',
                'mensagem' => 'Falha ao fazer upload da nova imagem. A imagem antiga será mantida.'
            ];
            error_log("Erro ao mover arquivo de imagem para: " . $caminhoCompleto);
        }
    }

    // Prepara a query de UPDATE para a tabela `produto` com as colunas corretas
    $stmt = $connect->prepare("UPDATE produto SET 
                                nomeProduto = ?, 
                                descricaoProduto = ?, 
                                precoProduto = ?, 
                                estoqueProduto = ?, 
                                imagem = ?, 
                                idCategoria = ? 
                                WHERE idProduto = ?");
    
    // Tipos de dados: s(string), s(string), d(double), i(integer), s(string), i(integer), i(integer)
    $stmt->bind_param("ssdisii", 
                    $nomeProduto, 
                    $descricaoProduto, 
                    $precoProduto, 
                    $estoqueProduto, 
                    $imagem, 
                    $idCategoria, 
                    $idProduto);

    if ($stmt->execute()) {
        $_SESSION['notificacao'] = [
            'tipo' => 'success',
            'mensagem' => 'Produto atualizado com sucesso.'
        ];
        header("Location: product_management.php");
        exit();
    } else {
        $_SESSION['notificacao'] = ['tipo' => 'danger', 'mensagem' => 'Erro ao atualizar produto: ' . $stmt->error];
        error_log("Erro na atualização do produto: " . $stmt->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../public/navbar.php'; // Inclui a navbar ?>

<div class="notificacao-container">
    <?php include_once __DIR__ . '/../config/message.php'; ?>
</div>

<?php if (!isAdmin()): ?>
    <div class="container py-5">
        <div class="alert alert-danger" role="alert">
            Acesso negado. Você não tem permissão para acessar esta página.
        </div>
    </div>
    <?php exit(); 
    else: ?>
<div class="container py-5">
    
    <div class="form-container">
        <h1 class="mb-4 text-center">Editar Produto</h1>
        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="nomeProduto" class="form-label">Nome do Produto</label>
                <input type="text" class="form-control" id="nomeProduto" name="nomeProduto" 
                    value="<?= htmlspecialchars($produto['nomeProduto']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descricaoProduto" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricaoProduto" name="descricaoProduto" rows="3" required><?= htmlspecialchars($produto['descricaoProduto']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="precoProduto" class="form-label">Preço (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="precoProduto" name="precoProduto" 
                        value="<?= htmlspecialchars($produto['precoProduto']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="estoqueProduto" class="form-label">Estoque</label>
                    <input type="number" class="form-control" id="estoqueProduto" name="estoqueProduto" 
                        value="<?= htmlspecialchars($produto['estoqueProduto']) ?>" min="0" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="idCategoria" class="form-label">Categoria</label>
                <select class="form-select" id="idCategoria" name="idCategoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php
                    if ($categorias_query) {
                        mysqli_data_seek($categorias_query, 0);
                    }
                    if ($categorias_query && mysqli_num_rows($categorias_query) > 0):
                        while ($categoria = mysqli_fetch_assoc($categorias_query)): ?>
                            <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>" 
                                <?= ($categoria['idCategoria'] == $produto['idCategoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </option>
                        <?php endwhile;
                    endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="imagem" class="form-label">Trocar Imagem (opcional)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/png, image/webp">
                <?php if ($produto['imagem']): ?>
                    <div class="mt-2">
                        <small class="form-text text-muted">Imagem atual:</small><br>
                        <img src="../images/<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem atual do produto" style="max-width: 150px; height: auto; border-radius: 5px; margin-top: 5px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <button type="submit" name="atualizar" class="btn btn-custom-primary">Salvar Alterações</button>
                <a href="product_management.php" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>
    <?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>