<?php 

if (!empty($_SESSION['mensagem'])): ?>
  <div class="alert alert-warning"><?= htmlspecialchars($_SESSION['mensagem']) ?></div>

<?php 
unset($_SESSION['mensagem']); 
endif

?>