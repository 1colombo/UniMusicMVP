<?php
include_once __DIR__ . '/config/init.php';

$connect = connectBanco();

$produtos = mysqli_query($connect, "
  SELECT produto.idProduto, produto.nomeProduto, produto.descricaoProduto, produto.precoProduto, produto.imagem, categoriaproduto.nome AS categoria
  FROM produto
  JOIN categoriaproduto ON produto.idCategoria = categoriaproduto.idCategoria
");
$stmt = $connect->prepare("SELECT produto.idProduto, produto.nomeProduto, produto.descricaoProduto, produto.precoProduto, produto.imagem, categoriaproduto.nome AS categoria
  FROM produto
  JOIN categoriaproduto ON produto.idCategoria = categoriaproduto.idCategoria");

$stmt->execute();
$result = $stmt->get_result();
$produtos = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UniMusic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    
<?php 

include __DIR__ . '/public/navbar.php'; 
?>
    
<div id="carouselExampleIndicators" class="carousel slide">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="https://images.unsplash.com/photo-1759928222798-63e73f690563?q=80&w=1172&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="https://images.unsplash.com/photo-1750785354752-2c234b875cdd?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="https://plus.unsplash.com/premium_photo-1759685060540-d6b4a1824f4c?q=80&w=1220&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<div class="page-body">
    <h1 class="text-center">Produtos mais relevantes</h1>

    <div class="row g-4">
        <?php foreach ($produtos as $produto): ?>
            <div class="col-md-4">
              <div class="card h-100">
                <img src="<?php echo BASE_URL; ?>/images/<?= htmlspecialchars($produto['imagem'])?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nomeProduto']) ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($produto['nomeProduto']) ?></h5>
                  <p class="card-text">R$ <?= number_format($produto['precoProduto'], 2, ',', '.') ?></p>
                  <p class="card-text"><small><?= $produto['categoria'] ?></small></p>
                  
                  <form method="POST" action="<?php echo BASE_URL; ?>/adicionar_carrinho.php">
                    <input type="hidden" name="id" value="<?= $produto['idProduto'] ?>">
                    <input type="hidden" name="nome" value="<?= $produto['nomeProduto'] ?>">
                    <input type="hidden" name="preco" value="<?= $produto['precoProduto'] ?>">
                    <?php if(isLoggedIn()): ?>
                      <button type="submit" class="btn btn-sm btn-success mt-2">Adicionar ao carrinho</button>
                    <?php endif?>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
    </div> </div>

<footer class="text-center text-lg-start bg-body-tertiary text-muted">
  <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
    <div class="me-5 d-none d-lg-block">
      <span>Siga nossas redes sociais:</span>
    </div>
    <div>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-google"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-linkedin"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-github"></i>
      </a>
    </div>
    </section>
  <section class="">
    <div class="container text-center text-md-start mt-5">
      <div class="row mt-3">
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">
            <img class="logo-footer"src="<?php echo BASE_URL; ?>/assets/images/UniCode-logo-transparente.png" style="width:220px">
          </h6>
          <p>
            Lorem ipsum
            dolor sit amet, consectetur adipisicing elit.
          </p>
        </div>
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">
            Products
          </h6>
          <p>
            <a href="#!" class="text-reset">Angular</a>
          </p>
          <p>
            <a href="#!" class="text-reset">React</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Vue</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Laravel</a>
          </p>
        </div>
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">
            Useful links
          </h6>
          <p>
            <a href="#!" class="text-reset">Pricing</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Settings</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Orders</a>
          </p>
          <p>
            <a href="#!" class="text-reset">Help</a>
          </p>
        </div>
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Contato</h6>
          <p><i class="fas fa-home me-3"></i>Campinas, SP</p>
          <p>
            <i class="fas fa-envelope me-3"></i>
            email@email.com
          </p>
          <p><i class="fas fa-phone me-3"></i> + 01 234 567 88</p>
          <p><i class="fas fa-print me-3"></i> + 01 234 567 89</p>
        </div>
    
        </div>
    
    </div>
  </section>

  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © 2025 Copyright:
    <a class="text-reset fw-bold" href="">UniMusic</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>