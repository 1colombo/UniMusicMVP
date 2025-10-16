<?php 
function isAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'Admin';
}
function isClient() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'Client';
}
function isLoggedIn() {
    return isset($_SESSION['logado']) && $_SESSION['logado'] === true;
}
?>