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

?>
<head>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/index.php"><img src="<?php echo BASE_URL; ?>/images/branco.png" class="navbar-brand"></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categorias
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="categoriasDropdown">
                        <?php
                        if ($categorias_query && mysqli_num_rows($categorias_query) > 0):
                            while ($cat = mysqli_fetch_assoc($categorias_query)): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/index.php?categoria=<?= $cat['idCategoria'] ?>">
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </a>
                                </li>
                            <?php endwhile;
                        else: ?>
                            <li><a class="dropdown-item" href="#">Nenhuma categoria encontrada</a></li>
                        <?php endif; ?>
                    </ul>
                </li> -->

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

            <div class="navbar-right">

                <div class="mx-auto navbar-right-item">
                    <form class="d-flex" action="<?php echo BASE_URL; ?>/index.php" method="GET" role="search">
                        <input class="form-control me-2" type="search" placeholder="Buscar produtos..." aria-label="Search" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" style="width: 300px;">
                        <button class="btn btn-outline-light btn-buscar" type="submit">Buscar</button>
                    </form>
                </div>
                    
                    <div class="dropdown btn-usuario navbar-right-item">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Minha Conta
                        </button>
                        <ul class="dropdown-menu">
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
                    
            </div>
        </div>
    </div>
</nav>