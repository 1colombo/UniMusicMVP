<?php
// (Este bloco de código assume que o 'init.php' já foi incluído na página principal antes desta navbar)

// Se a variável de conexão não existir, tenta conectar (medida de segurança)
if (!isset($connect) || !$connect) {
    $connect = connectBanco();
}

// Consulta as categorias de produto para o menu dropdown
$categorias_query = false; // Inicializa como false
if ($connect) {
    $categorias_sql = "SELECT idCategoria, nome FROM categoriaproduto ORDER BY nome ASC";
    $categorias_query = mysqli_query($connect, $categorias_sql);
    if (!$categorias_query) {
        error_log("Erro ao buscar categorias na navbar: " . mysqli_error($connect));
    }
}

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../public/index.php">UniMusic</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categorias
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="categoriasDropdown">
                        <?php
                        if ($categorias_query && mysqli_num_rows($categorias_query) > 0):
                            while ($cat = mysqli_fetch_assoc($categorias_query)): ?>
                                <li>
                                    <a class="dropdown-item" href="../public/index.php?categoria=<?= $cat['idCategoria'] ?>">
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </a>
                                </li>
                            <?php endwhile;
                        else: ?>
                            <li><a class="dropdown-item" href="#">Nenhuma categoria encontrada</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if(isAdmin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Administração
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="../admin/gerenciar_usuarios.php">Gerenciar Usuários</a></li>
                        <li><a class="dropdown-item" href="../admin/gerenciar_produtos.php">Gerenciar Produtos</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <div class="mx-auto">
                <form class="d-flex" action="../public/index.php" method="GET" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscar produtos..." aria-label="Search" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" style="width: 300px;">
                    <button class="btn btn-outline-light" type="submit">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</nav>