<?php
function isAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
}
function isUser() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'user';
}
function isLoggedIn() {
    return isset($_SESSION['logado']) && $_SESSION['logado'] === true;
}
?>