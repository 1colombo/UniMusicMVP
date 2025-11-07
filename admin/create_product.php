<?php
include_once __DIR__ . '/../config/init.php';

$connect = connectBanco();

$categorias_query = false;
if ($connect) {
    $categorias_sql = "SELECT * FROM categoriaproduto ORDER BY nome ASC";
    $categorias_query = mysqli_query($connect, $categorias_sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeProduto        = trim($_POST['nomeProduto']);
    $descricaoProduto   = trim($_POST['descricaoProduto']);
    $precoProduto       = floatval($_POST['precoProduto']);
    $estoqueProduto     = intval($_POST['estoqueProduto']);
    $idCategoria        = intval($_POST['idCategoria']);

    // Lógica para o upload da imagem
    $caminhoRelativoImagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $diretorioUploads = __DIR__ . '/../images/';

        // Cria a pasta 'uploads' se ela não existir
        if (!is_dir($diretorioUploads)) {
            mkdir($diretorioUploads, 0777, true);
        }

        $nomeArquivoOriginal = basename($_FILES['imagem']['name']);
        $extensao = pathinfo($nomeArquivoOriginal, PATHINFO_EXTENSION);
        $nomeUnico = uniqid() . '.' . $extensao; // Gera nome único para evitar colisões
        $caminhoCompleto = $diretorioUploads . $nomeUnico;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
            // Salva apenas o nome do arquivo, que será usado no src da tag <img>
            $caminhoRelativoImagem = $nomeUnico;
        } else {
            // Se o upload falhar, loga o erro mas continua sem imagem
            error_log("Erro ao mover arquivo de imagem para: " . $caminhoCompleto);
            $_SESSION['notificacao'] = [
                'tipo' => 'warning',
                'mensagem' => 'Imagem do produto não pôde ser carregada. O produto será salvo sem imagem.'
            ];
        }
    }

    // Prepara a query de INSERT para a tabela `produto` com as colunas corretas
    $sql = "INSERT INTO produto (nomeProduto, descricaoProduto, precoProduto, estoqueProduto, imagem, idCategoria)
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($connect, $sql);
    
    mysqli_stmt_bind_param($stmt, "ssdisi", $nomeProduto, $descricaoProduto, $precoProduto, $estoqueProduto, $caminhoRelativoImagem, $idCategoria);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['notificacao'] = [
            'tipo' => 'success',
            'mensagem' => 'Produto cadastrado com sucesso!'
        ];
        header("Location: product_management.php");
        exit();
    } else {
        $_SESSION['notificacao'] = [
            'tipo' => 'danger',
            'mensagem' => 'Erro ao cadastrar o produto. Tente novamente.'
        ];
        error_log("Erro no cadastro do produto: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../public/navbar.php'; ?>

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
            <h1 class="mb-4 text-center">Adicionar Novo Produto</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nomeProduto" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="nomeProduto" name="nomeProduto" required>
                </div>

        <div class="mb-3">
            <label for="descricaoProduto" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricaoProduto" name="descricaoProduto" rows="3" required></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="precoProduto" class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" class="form-control" id="precoProduto" name="precoProduto" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="estoqueProduto" class="form-label">Estoque</label>
                <input type="number" class="form-control" id="estoqueProduto" name="estoqueProduto" min="0" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="idCategoria" class="form-label">Categoria</label>
            <select class="form-select" id="idCategoria" name="idCategoria" required>
                <option value="">Selecione uma categoria</option>
                <?php
                if ($categorias_query) { // Verifica se a query foi bem sucedida antes de tentar manipular
                    mysqli_data_seek($categorias_query, 0);
                }
                if ($categorias_query && mysqli_num_rows($categorias_query) > 0):
                    while ($categoria = mysqli_fetch_assoc($categorias_query)): ?>
                        <option value="<?= htmlspecialchars($categoria['idCategoria']) ?>">
                            <?= htmlspecialchars($categoria['nome']) ?>
                        </option>
                    <?php endwhile;
                else: ?>
                    <option value="" disabled>Nenhuma categoria encontrada</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem do Produto (opcional)</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/png, image/webp">
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-custom-primary">Cadastrar Produto</button>
            <a href="product_management.php" class="btn btn-secondary ms-2">Voltar</a>
        </div>
    </form>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>