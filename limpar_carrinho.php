<?php
include_once __DIR__ . '/config/init.php';
$connect = connectBanco(); 

if (!isLoggedIn() || !isset($_SESSION['idCarrinho'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$idCarrinho = $_SESSION['idCarrinho'];

// 1. Limpa o banco de dados
$stmt = $connect->prepare("DELETE FROM itemcarrinho WHERE idCarrinho = ?");
$stmt->bind_param("i", $idCarrinho);
$stmt->execute();
$stmt->close();

// 2. Limpa a sessão
$_SESSION['carrinho'] = [];

$_SESSION['notificacao'] = [
    'tipo' => 'success',
    'mensagem' => 'Carrinho esvaziado com sucesso.'
];

// Redireciona de volta para a página anterior
$redirect_url = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php';
header('Location: ' . $redirect_url);
exit();