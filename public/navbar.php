<?php
if (!isset($connect) || !$connect) {
    include_once __DIR__ . '/../config/init.php'; 
    $connect = connectBanco();
}

$categorias_query = false; // Inicializa como false
if ($connect) {
    $categorias_sql = "SELECT idCategoria, nome FROM categoriaproduto ORDER BY nome ASC";
    $categorias_query = mysqli_query($connect, $categorias_sql);
    if (!$categorias_query) {
        error_log("Erro ao buscar categorias na navbar: " . mysqli_error($connect));
    }
}

$totalItensCarrinho = 0;
$subtotalCarrinho = 0.00;
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $totalItensCarrinho += $item['quantidade'];
        $subtotalCarrinho += $item['preco'] * $item['quantidade'];
    }
}

?>
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=account_circle" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/index.php"><img src="<?php echo BASE_URL; ?>/images/branco.png" class="navbar-brand"></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse w-100" id="navbarContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if(isAdmin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administração
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/gerenciar_usuarios.php">Gerenciar Usuários</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/product_management.php">Gerenciar Produtos</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>   

            <div class="mx-auto">
                <form class="d-flex" action="<?php echo BASE_URL; ?>/index.php" method="GET" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscar produtos..." aria-label="Search" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" style="width: 300px;">
                    <button class="btn btn-outline-light btn-buscar" type="submit">Buscar</button>
                </form>
            </div>
            
            <div class="navbar-right ms-auto">
                    
            <!-- Usuário / Conta -->
                    <div class="dropdown btn-usuario navbar-right-item">
                        <button class="btn btn-secondary btn-account btn-outline-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined account-icon ">account_circle</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg-end">
                            <?php if (isLoggedIn()): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/users/profile.php">Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/users/logout.php">Sair</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/users/login.php">Entrar</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/users/create_account.php">Criar conta</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Carrinho de Compras -->
                    <?php if (isLoggedIn()): ?>
                    <div class="navbar-right-item ">
                        <button class="btn btn-outline-light btn-cart" type="button" data-bs-toggle="offcanvas" data-bs-target="#carrinhoOffcanvas" aria-controls="carrinhoOffcanvas">
                            <i class="bi bi-cart-fill"></i> <?php if ($totalItensCarrinho > 0): ?>
                                <span class="badge bg-danger rounded-pill number-cart"><?= $totalItensCarrinho ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                <?php endif; ?>
                    
            </div>
        </div>
    </div>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="category-bar">
    <div class="container-fluid">
        <ul class="navbar-nav justify-content-center w-100">
            <?php
            // Verifica se a query de categorias foi bem-sucedida
            if ($categorias_query && mysqli_num_rows($categorias_query) > 0):
                
                // Reinicia o ponteiro da query, pois ela pode ter sido usada antes
                mysqli_data_seek($categorias_query, 0); 
                
                while ($cat = mysqli_fetch_assoc($categorias_query)): 
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php?categoria=<?= $cat['idCategoria'] ?>">
                        <?= htmlspecialchars($cat['nome']) ?>
                    </a>
                </li>
            <?php 
                endwhile;
            else: 
                // Caso não encontre categorias no banco
            ?>
                <li class="nav-item">
                    <span class="nav-link text-muted">Nenhuma categoria encontrada</span>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<div class="offcanvas offcanvas-end" tabindex="-1" id="carrinhoOffcanvas" aria-labelledby="carrinhoOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="carrinhoOffcanvasLabel">Meu Carrinho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php if ($totalItensCarrinho > 0): ?>
            
            <div class="mb-3">
                <?php foreach ($_SESSION['carrinho'] as $item): ?>
                    <div class="card mb-2 carrinho-item">
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($item['nome']) ?></h6>
                            
                            <p class="card-text fw-bold mb-2">
                                R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?>
                            </p>
                            
                            <form method="POST" action="<?php echo BASE_URL; ?>/atualizar_carrinho.php" class="carrinho-botoes">
                                <input type="hidden" name="idProduto" value="<?= $item['id'] ?>">

                                <button type="submit" name="acao" value="subtract" class="btn btn-outline-secondary btn-qty">
                                    <i class="bi bi-dash-lg"></i>
                                </button>

                                <span class="carrinho-quantidade"><?= $item['quantidade'] ?></span>

                                <button type="submit" name="acao" value="add" class="btn btn-outline-secondary btn-qty">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                                
                                <button type="submit" name="acao" value="remove" class="btn btn-outline-danger btn-qty-remove ms-auto" 
                                        onclick="return confirm('Remover este item do carrinho?');">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fs-5">Subtotal:</span>
                <span class="fs-5 fw-bold">R$ <?= number_format($subtotalCarrinho, 2, ',', '.') ?></span>
            </div>

            <div class="d-grid gap-2">
                <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-custom-primary">Finalizar Compra</a>
                <a href="<?php echo BASE_URL; ?>/limpar_carrinho.php" class="btn btn-outline-danger">Limpar Carrinho</a>
            </div>

        <?php else: ?>
            <div class="text-center mt-5">
                <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d;"></i>
                <p class="mt-3">Seu carrinho está vazio.</p>
            </div>
            <?php endif; ?>
    </div>
</div>