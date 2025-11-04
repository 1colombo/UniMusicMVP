<?php
// Inicia a sessão e a conexão com o banco de dados
include_once __DIR__ . '/../config/init.php';
$connect = connectBanco();

// --- LÓGICA PARA EXCLUIR UM PRODUTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_produto'])) {
    $id = $_POST['id'];

    // Prepara a query para deletar da tabela correta 'produto' usando a coluna 'idProduto'
    $stmt = $connect->prepare("DELETE FROM produto WHERE idProduto = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Produto excluído com sucesso!';
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir o produto.';
    }
    // Redireciona para a mesma página para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <?php include __DIR__ . '/../public/navbar.php';?>

  <?php if (!isAdmin()): ?>
      <div class="container py-5">
          <div class="alert alert-danger" role="alert">
              Acesso negado. Você não tem permissão para acessar esta página.
          </div>
      </div>
      <?php exit(); 
      else: ?>
  <div class="container py-5">
        <?php include_once __DIR__ . '/../config/message.php'; ?>
        <div class="card">
            <div class="card-header">
                <h4 class="d-flex justify-content-between align-items-center">
                    <span>Lista de Produtos</span>
                    <a href="create_product.php" class="btn btn-custom-primary">Adicionar Produto</a>
                </h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Imagem</th>
                            <th style="width: 120px;">Nome do Produto</th>
                            <th style="width: 100px;">Preço</th>
                            <th style="width: 120px;">Categoria</th>
                            <th style="width: 50px;">Estoque</th>
                            <th style="width: 50px;">Ações</th>
                        </tr>
                    </thead>
              <tbody>
                <?php
                  // Query SQL ajustada para as tabelas e colunas do seu projeto
                  $sql = "SELECT p.idProduto, p.nomeProduto, p.precoProduto, p.estoqueProduto, p.imagem, c.nome AS categoria
                          FROM produto AS p
                          JOIN categoriaproduto AS c ON p.idCategoria = c.idCategoria
                          ORDER BY p.nomeProduto ASC";

                  $result = mysqli_query($connect, $sql);
                  while($produto = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td>
                      <?php if (!empty($produto['imagem'])): ?>
                        <img src="../images/<?= htmlspecialchars($produto['imagem']) ?>" width="60">
                      <?php else: ?>
                        Sem imagem
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($produto['nomeProduto']) ?></td>
                    <td>R$ <?= number_format($produto['precoProduto'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($produto['categoria']) ?></td>
                    <td><?= htmlspecialchars($produto['estoqueProduto']) ?></td>
                    <td>
                      <a 
                        href="edit_product.php?id=<?= $produto['idProduto'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      <form action="" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?= $produto['idProduto'] ?>">
                        <button type="submit" name="delete_produto" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir este produto?')">
                          <i class="bi bi-trash-fill"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>