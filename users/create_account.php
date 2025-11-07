<?php
include_once __DIR__ . '/../config/init.php';
$connect = connectBanco();

$nome = $email = $telefone = $cpf = $senha = $confirmar_senha = '';
$cep = $logradouro = $numero = $complemento = $bairro = $cidade = $estado = $ibge = '';

if (isLoggedIn()) {
    header('Location: ../public/index.php');
    exit();
}

// Lógica para processar o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- COLETA DE DADOS ---
    $nome = trim($_POST['nomeUsuario']);
    $email = trim($_POST['emailUsuario']);
    $telefone = trim($_POST['telefoneUsuario']);
    $cpf = trim($_POST['CPFUsuario']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Dados do endereço
    $cep = trim($_POST['cep']);
    $logradouro = trim($_POST['logradouro']);
    $numero = trim($_POST['numero']);
    $complemento = trim($_POST['complemento']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    $ibge = ''; 

    // --- BLOCO DE VALIDAÇÃO ---
    $errors = [];

    // 1. Validação de campos obrigatórios e formatos
    if (empty($nome)) { $errors[] = 'O campo "Nome Completo" é obrigatório.'; }
    if (empty($email)) {
        $errors[] = 'O campo "E-mail" é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'O formato do E-mail é inválido.';
    }
    if (empty($telefone)) { 
        $errors[] = 'O campo "Telefone" é obrigatório.'; 
    } elseif (!preg_match('/^\(?\d{2}\)?[\s-]?\d{4,5}[-]?\d{4}$/', $telefone)) {
        $errors[] = 'O formato do Telefone é inválido. Use (XX) XXXXX-XXXX.';
    }

    if (empty($cpf)) { 
        $errors[] = 'O campo "CPF" é obrigatório.'; 
    } elseif (!preg_match('/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/', $cpf)) {
        $errors[] = 'O formato do CPF é inválido. Use XXX.XXX.XXX-XX.';
    }
    
    if (empty($senha)) {
        $errors[] = 'O campo "Senha" é obrigatório.';
    } elseif (strlen($senha) < 8) {
        $errors[] = 'A senha deve ter no mínimo 8 caracteres.';
    }
    if ($senha !== $confirmar_senha) {
        $errors[] = 'As senhas não coincidem. Tente novamente.';
    }
    if (empty($cep) || empty($logradouro) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado)) {
        $errors[] = 'Todos os campos de endereço (exceto complemento) são obrigatórios.';
    }

    // 2. Verificação de duplicidade (somente se os formatos básicos estiverem corretos)
    if (empty($errors)) {
        $stmt_check_email = $connect->prepare("SELECT idUsuario FROM usuario WHERE emailUsuario = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        if ($stmt_check_email->get_result()->num_rows > 0) { $errors[] = 'Este E-mail já está cadastrado.'; }
        $stmt_check_email->close();

        $stmt_check_cpf = $connect->prepare("SELECT idUsuario FROM usuario WHERE CPFUsuario = ?");
        $stmt_check_cpf->bind_param("s", $cpf);
        $stmt_check_cpf->execute();
        if ($stmt_check_cpf->get_result()->num_rows > 0) { $errors[] = 'Este CPF já está cadastrado.'; }
        $stmt_check_cpf->close();

        $stmt_check_tel = $connect->prepare("SELECT idUsuario FROM usuario WHERE telefoneUsuario = ?");
        $stmt_check_tel->bind_param("s", $telefone);
        $stmt_check_tel->execute();
        if ($stmt_check_tel->get_result()->num_rows > 0) { $errors[] = 'Este Telefone já está cadastrado.'; }
        $stmt_check_tel->close();
    }
    
    // --- FIM DO BLOCO DE VALIDAÇÃO ---

    if (!empty($errors)) {
        $_SESSION['notificacao'] = [
            'tipo' => 'danger',
            'mensagem' => implode('<br>', $errors) // Mostra todos os erros
        ];
    
    } else {
        // NENHUM ERRO - Tenta preencher o IBGE (se já não estiver)
        $cep_digits = preg_replace('/\D/', '', $cep);
        if (strlen($cep_digits) === 8 && empty($ibge)) {
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $url = 'https://viacep.com.br/ws/' . $cep_digits . '/json/';
            $viacep_raw = @file_get_contents($url, false, $context);
            if ($viacep_raw !== false) {
                $viacep_data = json_decode($viacep_raw, true);
                if (is_array($viacep_data) && empty($viacep_data['erro']) && !empty($viacep_data['ibge'])) {
                    $ibge = $viacep_data['ibge'];
                }
            }
        }

        // Hash da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $connect->begin_transaction();
        try {
            // 1. Insere o usuário
            $stmt_usuario = $connect->prepare("INSERT INTO usuario (nomeUsuario, emailUsuario, senhaHash, telefoneUsuario, CPFUsuario) VALUES (?, ?, ?, ?, ?)");
            $stmt_usuario->bind_param("sssss", $nome, $email, $senhaHash, $telefone, $cpf);
            $stmt_usuario->execute();
            $idNovoUsuario = $connect->insert_id;
            if (!$idNovoUsuario) { throw new Exception("Erro ao obter o ID do novo usuário."); }
            $stmt_usuario->close();

            // 2. Insere o endereço
            $stmt_endereco = $connect->prepare("INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, uf, idUsuario, ibge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_endereco->bind_param("sssssssis", $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $idNovoUsuario, $ibge);
            $stmt_endereco->execute();
            $stmt_endereco->close();

            $connect->commit();
            $_SESSION['notificacao'] = [
                'tipo' => 'success',
                'mensagem' => 'Cadastro realizado com sucesso! Faça o login para continuar.'
            ];
            header('Location: login.php');
            exit();

        } catch (Exception $e) {
            $connect->rollback();
            $_SESSION['notificacao'] = [
                'tipo' => 'danger',
                'mensagem' => 'Erro interno ao realizar o cadastro. Tente novamente.'
            ];
            error_log("Erro no cadastro (transação revertida): " . $e->getMessage() . " / SQL error: " . $connect->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - UniMusic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> </head>
<body>
<?php include_once __DIR__ . '/../public/navbar.php'; ?>

<div class="notificacao-container">
    <?php include_once __DIR__ . '/../config/message.php'; ?>
</div>

<div class="container py-5">
    <div class="form-container">
        <h1 class="mb-4 text-center">Crie sua Conta</h1>

    <form action="<?php echo BASE_URL; ?>/users/create_account.php" method="POST">
            <h4 class="mb-3 text-center">Informações Pessoais</h4>
            <div class="mb-3">
                <label for="nomeUsuario" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nomeUsuario" name="nomeUsuario" value="<?= htmlspecialchars($nome ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="emailUsuario" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="emailUsuario" name="emailUsuario" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefoneUsuario" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefoneUsuario" name="telefoneUsuario" placeholder="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($telefone ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="CPFUsuario" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="CPFUsuario" name="CPFUsuario" placeholder="XXX.XXX.XXX-XX" value="<?= htmlspecialchars($cpf ?? '') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>
            </div>

            <h4 class="mb-3 mt-4 text-center">Informações de Endereço</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" placeholder="XXXXX-XXX" value="<?= htmlspecialchars($cep ?? '') ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro"  value="<?= htmlspecialchars($logradouro ?? '') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero"  value="<?= htmlspecialchars($numero ?? '') ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="complemento" class="form-label">Complemento (opcional)</label>
                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= htmlspecialchars($complemento ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= htmlspecialchars($bairro ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?= htmlspecialchars($cidade ?? '') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" maxlength="2" placeholder="UF" value="<?= htmlspecialchars($estado ?? '') ?>" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-custom-primary">Cadastrar</button>
            </div>
            <div class="text-center mt-3">
                <p>Já tem uma conta? <a href="login.php">Faça o login</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    if (!cepInput) return;

    let debounceTimer = null;

    const setAddressFields = (data) => {
        // Preenche apenas se o campo estiver vazio, para não sobrescrever a entrada do usuário
        const logradouroEl = document.getElementById('logradouro');
        const bairroEl = document.getElementById('bairro');
        const cidadeEl = document.getElementById('cidade');
        const estadoEl = document.getElementById('estado');

        if (logradouroEl && (!logradouroEl.value || logradouroEl.value === '...') && data.logradouro) logradouroEl.value = data.logradouro;
        if (bairroEl && (!bairroEl.value || bairroEl.value === '...') && data.bairro) bairroEl.value = data.bairro;
        if (cidadeEl && (!cidadeEl.value || cidadeEl.value === '...') && data.localidade) cidadeEl.value = data.localidade;
        if (estadoEl && (!estadoEl.value || estadoEl.value === '...') && data.uf) estadoEl.value = data.uf;
    };

    const clearAddressPlaceholders = () => {
        const logradouroEl = document.getElementById('logradouro');
        const bairroEl = document.getElementById('bairro');
        const cidadeEl = document.getElementById('cidade');
        const estadoEl = document.getElementById('estado');
        if (logradouroEl && logradouroEl.value === '...') logradouroEl.value = '';
        if (bairroEl && bairroEl.value === '...') bairroEl.value = '';
        if (cidadeEl && cidadeEl.value === '...') cidadeEl.value = '';
        if (estadoEl && estadoEl.value === '...') estadoEl.value = '';
    };

    const fetchByCep = (cepDigits) => {
        // coloca placeholders enquanto busca
        const logradouroEl = document.getElementById('logradouro');
        const bairroEl = document.getElementById('bairro');
        const cidadeEl = document.getElementById('cidade');
        const estadoEl = document.getElementById('estado');
        if (logradouroEl && !logradouroEl.value) logradouroEl.value = '...';
        if (bairroEl && !bairroEl.value) bairroEl.value = '...';
        if (cidadeEl && !cidadeEl.value) cidadeEl.value = '...';
        if (estadoEl && !estadoEl.value) estadoEl.value = '...';

        fetch('https://viacep.com.br/ws/' + cepDigits + '/json/')
            .then(function(response) {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(function(data) {
                if (data.erro) {
                    // CEP não encontrado
                    clearAddressPlaceholders();
                    return;
                }
                setAddressFields(data);
            })
            .catch(function(err) {
                console.error('Erro ao consultar ViaCEP:', err);
                clearAddressPlaceholders();
            });
    };

    cepInput.addEventListener('input', function() {
        // normaliza para dígitos
        const onlyDigits = cepInput.value.replace(/\D/g, '');

        // se houver debounce anterior, limpa
        if (debounceTimer) clearTimeout(debounceTimer);

        // só consulta quando houver 8 dígitos
        if (onlyDigits.length === 8) {
            // pequena espera para o usuário terminar a digitação/paste
            debounceTimer = setTimeout(function() {
                fetchByCep(onlyDigits);
            }, 400);
        } else {
            // se o CEP for reduzido para menos de 8, limpa placeholders
            if (onlyDigits.length < 8) {
                clearAddressPlaceholders();
            }
        }
    });

    // Também tenta quando o campo perde o foco se o usuário colou e não houve input evento
    cepInput.addEventListener('blur', function() {
        const onlyDigits = cepInput.value.replace(/\D/g, '');
        if (onlyDigits.length === 8) {
            fetchByCep(onlyDigits);
        }
    });
});
</script>
</body>
</html>