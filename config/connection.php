<?php
function connectBanco() {
  $host = 'localhost';
  $usuario = 'root';
  $senha = '';
  $nome_banco = 'ecommerce';

  $conexao = mysqli_connect($host, $usuario, $senha, $nome_banco);

  if (!$conexao) {
      die("Connection failed: " . mysqli_connect_error());
  } 
  return $conexao;
}
?>
