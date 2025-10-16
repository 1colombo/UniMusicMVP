<?php
if (!isset($conexao) || !$conexao) {
    $conexao = connectBanco();
}

// Consulta as categorias de produto para o menu dropdown
$categorias_query = false; // Inicializa como false
if ($conexao) {
    $categorias_sql = "SELECT idCategoria, nome FROM categoriaproduto ORDER BY nome ASC";
    $categorias_query = mysqli_query($conexao, $categorias_sql);
    if (!$categorias_query) {
        error_log("Erro ao buscar categorias na navbar: " . mysqli_error($conexao));
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../public/index.php">{Uni}Music</a>

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
                        // Verifica se a query de categorias retornou resultados antes de fazer o loop
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
        </div>
    </div>
</nav>