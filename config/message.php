<?php 
// Verifica se existe uma notificação na sessão
if (isset($_SESSION['notificacao'])):
    
    // Extrai as variáveis (ex: 'success' e 'Produto...')
    $tipo = $_SESSION['notificacao']['tipo'];
    $mensagem = $_SESSION['notificacao']['mensagem'];

?>
  <div class="alert alert-<?php echo htmlspecialchars($tipo); ?> alert-dismissible fade show" role="alert" id="autoFadeAlert">
    <?php echo $mensagem; // Mensagem (já pode conter HTML, como <br> das validações) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Tenta encontrar o alerta pelo ID
        var autoFadeAlert = document.getElementById('autoFadeAlert');
        
        if (autoFadeAlert) {
            // Espera 5 segundos (5000 ms)
            setTimeout(() => {
                // Cria uma instância do Alerta Bootstrap para poder usar o método .close()
                var bsAlert = new bootstrap.Alert(autoFadeAlert);
                bsAlert.close(); // Fecha o alerta com o fade-out
            }, 5000); // 5 segundos
        }
    });
  </script>

<?php 
// Limpa a notificação da sessão para que não apareça novamente
unset($_SESSION['notificacao']); 
endif;
?>