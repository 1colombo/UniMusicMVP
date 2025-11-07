<?php
include_once __DIR__ . '/config/init.php';
$connect = connectBanco(); 

if (!isLoggedIn() || !isset($_SESSION['idCarrinho'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$redirect_url = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idProduto']) || !isset($_POST['acao'])) {
    header('Location: ' . $redirect_url);
    exit();
}

$idCarrinho = $_SESSION['idCarrinho'];
$idProduto = intval($_POST['idProduto']);
$acao = $_POST['acao']; // 'add', 'subtrair', ou 'remove'
$nomeProduto = $_SESSION['carrinho'][$idProduto]['nome'] ?? 'Produto';

// --- LÓGICA DE ATUALIZAÇÃO ---

try {
    // --- AÇÃO: REMOVER ITEM ---
    if ($acao === 'remove') {
        $stmt = $connect->prepare("DELETE FROM itemcarrinho WHERE idCarrinho = ? AND idProduto = ?");
        $stmt->bind_param("ii", $idCarrinho, $idProduto);
        $stmt->execute();
        
        // Atualiza a sessão (cache)
        unset($_SESSION['carrinho'][$idProduto]);

        $_SESSION['notificacao'] = [
            'tipo' => 'success',
            'mensagem' => '<strong>' . htmlspecialchars($nomeProduto) . '</strong> removido do carrinho.'
        ];

    // --- AÇÃO: ADICIONAR +1 (Incrementar) ---
    } elseif ($acao === 'add') {
        
        // VERIFICAÇÃO DE ESTOQUE (essencial)
        $stmt_estoque = $connect->prepare("SELECT estoqueProduto FROM produto WHERE idProduto = ?");
        $stmt_estoque->bind_param("i", $idProduto);
        $stmt_estoque->execute();
        $estoque_atual = $stmt_estoque->get_result()->fetch_assoc()['estoqueProduto'];
        $stmt_estoque->close();

        $quantidade_no_carrinho = $_SESSION['carrinho'][$idProduto]['quantidade'] ?? 0;

        if (($quantidade_no_carrinho + 1) > $estoque_atual) {
            // Se estourar o estoque
            $_SESSION['notificacao'] = [
                'tipo' => 'danger',
                'mensagem' => 'Não foi possível adicionar mais <strong>' . htmlspecialchars($nomeProduto) . '</strong>. Estoque ('.$estoque_atual.') atingido.'
            ];
        } else {
            // Estoque OK, atualiza o DB
            $stmt = $connect->prepare("UPDATE itemcarrinho SET quantidade = quantidade + 1 WHERE idCarrinho = ? AND idProduto = ?");
            $stmt->bind_param("ii", $idCarrinho, $idProduto);
            $stmt->execute();
            
            // Atualiza a sessão (cache)
            $_SESSION['carrinho'][$idProduto]['quantidade']++;
        }

    // --- AÇÃO: SUBTRAIR -1 (Decrementar) ---
    } elseif ($acao === 'subtract') {
        $quantidade_no_carrinho = $_SESSION['carrinho'][$idProduto]['quantidade'] ?? 1;

        if ($quantidade_no_carrinho <= 1) {
            // Se a quantidade for 1, subtrair significa remover
            $stmt = $connect->prepare("DELETE FROM itemcarrinho WHERE idCarrinho = ? AND idProduto = ?");
            $stmt->bind_param("ii", $idCarrinho, $idProduto);
            $stmt->execute();
            
            // Atualiza a sessão (cache)
            unset($_SESSION['carrinho'][$idProduto]);
            
            $_SESSION['notificacao'] = [
                'tipo' => 'success',
                'mensagem' => '<strong>' . htmlspecialchars($nomeProduto) . '</strong> removido do carrinho.'
            ];

        } else {
            // Apenas subtrai 1 do DB
            $stmt = $connect->prepare("UPDATE itemcarrinho SET quantidade = quantidade - 1 WHERE idCarrinho = ? AND idProduto = ?");
            $stmt->bind_param("ii", $idCarrinho, $idProduto);
            $stmt->execute();
            
            // Atualiza a sessão (cache)
            $_SESSION['carrinho'][$idProduto]['quantidade']--;
        }
    }

} catch (Exception $e) {
    $_SESSION['notificacao'] = [
        'tipo' => 'danger',
        'mensagem' => 'Erro ao atualizar o carrinho.'
    ];
    error_log("Erro ao atualizar carrinho: " . $e->getMessage());
}

header('Location: ' . $redirect_url);
exit();