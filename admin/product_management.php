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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

  <?php include __DIR__ . '/../public/navbar.php'; // Inclui a barra de navegação ?>
  <?php include_once __DIR__ . '/../config/message.php'; // Inclui o sistema de mensagens ?>

  <div class="container" id="gerenciamento">
    <h1 class="mt-5">Gerenciador de Produtos</h1>
  </div>

  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>Lista de Produtos
              <a href="../admin/create_product.php" class="btn btn-dark float-end">Adicionar Produto</a>
            </h4>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Imagem</th>
                  <th>Nome</th>
                  <th>Preço</th>
                  <th>Categoria</th>
                  <th>Estoque</th>
                  <th>Ações</th>
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
                      <a href="produto_edit.php?id=<?= $produto['idProduto'] ?>" class="btn btn-warning btn-sm">Editar</a>

                      <form action="" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?= $produto['idProduto'] ?>">
                        <button type="submit" name="delete_produto" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir este produto?')">
                          Excluir
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
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>