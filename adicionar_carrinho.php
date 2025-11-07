<?php
include_once __DIR__ . '/config/init.php';
$connect = connectBanco(); 

if (!isLoggedIn() || !isset($_SESSION['idCarrinho'])) {
    $_SESSION['mensagem'] = 'Você precisa estar logado para adicionar itens ao carrinho.';
    header('Location: ' . BASE_URL . '/users/login.php');
    exit();
}

// Verifica se os dados do produto foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    
    $idProduto = intval($_POST['id']);
    $nomeProduto = $_POST['nome'];
    $precoProduto = floatval($_POST['preco']);
    $quantidade_adicionar = 1; // Quantidade padrão
    
    $idCarrinho = $_SESSION['idCarrinho'];

    // 1. Buscar o estoque ATUAL do produto na tabela 'produto'
    $stmt_estoque = $connect->prepare("SELECT estoqueProduto FROM produto WHERE idProduto = ?");
    $stmt_estoque->bind_param("i", $idProduto);
    $stmt_estoque->execute();
    $result_estoque = $stmt_estoque->get_result();

    if ($result_estoque->num_rows === 0) {
        $_SESSION['mensagem'] = 'Erro: Produto não encontrado.';
        header('Location: ' . $redirect_url);
        exit();
    }

    $produto_data = $result_estoque->fetch_assoc();
    $estoque_atual = $produto_data['estoqueProduto']; // Ex: 5
    $stmt_estoque->close();

    // 2. Buscar a quantidade que o usuário JÁ POSSUI no 'itemcarrinho'
    $quantidade_no_carrinho = 0;
    $stmt_carrinho = $connect->prepare("SELECT quantidade FROM itemcarrinho WHERE idCarrinho = ? AND idProduto = ?");
    $stmt_carrinho->bind_param("ii", $idCarrinho, $idProduto);
    $stmt_carrinho->execute();
    $result_carrinho = $stmt_carrinho->get_result();
    
    if ($result_carrinho->num_rows === 1) {
        $carrinho_data = $result_carrinho->fetch_assoc();
        $quantidade_no_carrinho = $carrinho_data['quantidade']; // Ex: 4
    }
    $stmt_carrinho->close();

    // 3. A Verificação
    // A quantidade desejada (o que já tem + 1) não pode ser maior que o estoque
    if (($quantidade_no_carrinho + $quantidade_adicionar) > $estoque_atual) {
        // Ex: (4 + 1) > 5 = false (pode adicionar)
        // Ex: (5 + 1) > 5 = true (NÃO pode adicionar)
        
        $_SESSION['notificacao'] = [
            'tipo' => 'danger',
            'mensagem' => 'Não foi possível adicionar "' . htmlspecialchars($nomeProduto) . '". Estoque indisponível.'
        ];
        header('Location: ' . BASE_URL . '/index.php');
        exit(); // Para a execução do script aqui
    }

    $sql = "INSERT INTO itemcarrinho (idCarrinho, idProduto, quantidade, preco_unitario)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            quantidade = quantidade + VALUES(quantidade),
            preco_unitario = VALUES(preco_unitario)"; 
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("iiid", $idCarrinho, $idProduto, $quantidade_adicionar, $precoProduto);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['carrinho'][$idProduto])) {
            $_SESSION['carrinho'][$idProduto]['quantidade'] += $quantidade_adicionar;
            $_SESSION['carrinho'][$idProduto]['preco'] = $precoProduto;
        } else {
            $_SESSION['carrinho'][$idProduto] = [
                'id' => $idProduto,
                'nome' => $nomeProduto,
                'preco' => $precoProduto,
                'quantidade' => $quantidade_adicionar
            ];
        }
        $_SESSION['notificacao'] = [
            'tipo' => 'success', // 'success' para verde
            'mensagem' => '<strong>' . htmlspecialchars($nomeProduto) . '</strong> foi adicionado ao carrinho!'
        ];

    } else {
        $_SESSION['notificacao'] = [
            'tipo' => 'danger',
            'mensagem' => 'Erro ao adicionar produto ao carrinho. Tente novamente.'
        ];
        error_log("Erro no carrinho (DB): " . $stmt->error);
    }
    $stmt->close();

} else {
    $_SESSION['mensagem'] = 'Erro ao adicionar produto.';
}

$redirect_url = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php';
header('Location: ' . $redirect_url);
exit();