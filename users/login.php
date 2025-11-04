<?php

include_once __DIR__ . '/../config/init.php';
$connect = connectBanco();

if (isLoggedIn()) {
    header('Location: ../public/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailUsuario']);
    $senha = $_POST['senha'];

    $stmt = $connect->prepare("SELECT idUsuario, nomeUsuario, senhaHash, tipo FROM usuario WHERE emailUsuario = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senhaHash'])) {
            session_regenerate_id(true);

            $_SESSION['idUsuario'] = $usuario['idUsuario'];
            $_SESSION['nomeUsuario'] = $usuario['nomeUsuario'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['logado'] = true;

            header('Location: ../index.php');
            exit();
        }
    }

    $_SESSION['mensagem'] = 'E-mail ou senha inválidos.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UniMusic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> </head>
<body>
<?php include_once __DIR__ . '/../public/navbar.php'; ?>

<div class="container py-5">
    <div class="form-container" style="max-width: 500px;">
        <h1 class="mb-4 text-center">Acesse sua Conta</h1>
        <?php include_once __DIR__ . '/../config/message.php';?>

        <form action="<?php echo BASE_URL; ?>/users/login.php" method="POST">
            <div class="mb-3">
                <label for="emailUsuario" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="emailUsuario" name="emailUsuario" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-custom-primary">Entrar</button>
            </div>
            <div class="text-center mt-3">
                <p>Não tem uma conta? <a href="create_account.php">Cadastre-se</a></p>
                </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>