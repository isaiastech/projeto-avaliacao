<?php
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

if ($_SESSION['nivel'] === 'user') {
    header('Location: aguardando_autorizacao.php');
    exit;
}
use class\data\Database;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com.br/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icons/favicon.ico">
    <link rel="manifest" href="/icons/site.webmanifest">
    <link rel="stylesheet" href="css/avaliacao.css">
    <script src="https://kit.fontawesome.com/21a7183a5f.js" crossorigin="anonymous"></script>
    <title>Avaliação do Café</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body>
  <nav class="navbar navbar-expand-md" style="background-color: #250352; color: #fff">
    <div class="d-flex align-items-center">
      <img src="/images/image-painel.png" class="rounded-circle shadow-sm" width="50" height="50">
      <div class="ml-3">
        <div class="font-weight-bold text-white">
          Olá, <?= htmlspecialchars($_SESSION['nome']) ?>
        </div>
        <small class="text-light">
          Avaliações do Café
        </small>
    </div>
      </div>
        <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link text-white" href="/views/dashboard.php">
                  Voltar ao sistema
              </a>
          </li>
        </ul>
        <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/logout.php" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-decoration: none;color: #fff">
                        Sair do Sistema
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/logout.php">Sair</a>
                    </div>
                </li>
            </ul>
        </div> 
  </nav>  
<main>
  <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle mr-2"></i>
          <?= htmlspecialchars($_SESSION['mensagem_sucesso']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
      </div>
    </div>
  <?php unset($_SESSION['mensagem_sucesso']); ?>
<?php endif; ?>

<?php

$itens = [
    'cafe_manha' => '☕ Café da manhã',
    'colchao' => '🛏️ Colchão',
    'travesseiro' => '🛌 Travesseiro',
    'limpeza' => '🧹 Limpeza',
    'frigobar' => '🧊 Frigobar',
    'chuveiro_aquecimento' => '🚿 Chuveiro (aquecimento)',
    'chuveiro_ducha' => '🚿 Chuveiro (ducha)',
    'ar_condicionado' => '❄️ Ar condicionado',
    'roupa_cama' => '🛏️ Roupa de cama',
    'internet' => '🌐 Internet',
    'bar' => '🍹 Bar',
    'atendimento' => '🙋 Atendimento',
    'reserva' => '📋 Reserva',
    'recepcao' => '🛎️ Recepção',
    'camareira' => '🧹 Camareira',
    'garcom' => '🍽️ Garçom'
];

$opcoes = [
    'otimo' => '😍 Ótimo',
    'bom' => '😊 Bom',
    'satisfatorio' => '😐 Satisfatório',
    'ruim' => '😞 Ruim'
];

?>

<div class="container my-4">

    <!-- TÍTULO -->

  <div class="text-center mb-4">
    <h1 class="h3 font-weight-bold">
      Avaliação do Hóspede
    </h1>
    <p class="text-muted">Sua opinião é muito importante para nós.</p>

  </div>
    <form action="salvar_avaliacao.php" method="POST">
 <!-- ==============================================
   DADOS DO HÓSPEDE
=============================================== -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <i class="fas fa-user mr-2"></i>
                Dados do Hóspede
        </div>
      <div class="card-body">
        <div class="form-row">
<!-- HÓSPEDE -->
        <div class="form-group col-md-6">
    <label for="hospede">
        👤 Hóspede 
    </label>
    <input type="text" class="form-control" id="hospede" name="hospede" placeholder="Digite o nome completo" autocomplete="name" required>
</div>

<!-- APTO -->
<div class="form-group col-md-3">
    <label for="apto">
        🏨 Apartamento 
    </label>
    <input type="text" class="form-control" id="apto" name="apto" placeholder="Ex.: 101" inputmode="numeric" pattern="[0-9]+" maxlength="5" required >

</div>
<!-- DATA -->
<div class="form-group col-md-3">
    <label for="data_avaliacao">📅 Data </label>
    <input type="date" class="form-control" id="data_avaliacao" name="data_avaliacao" value="<?= date('Y-m-d') ?>" required >
</div>
<!-- TELEFONE -->
<div class="form-group col-md-6">
  <label for="fone">📱 Telefone</label>
  <input type="tel" class="form-control" id="fone" name="fone" placeholder="(00)00000-0000" maxlength="15" inputmode="numeric" autocomplete="tel">
</div>
  <!-- E-MAIL -->
  <div class="form-group col-md-6">
    <label for="email"> 📧 E-mail</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@email.com" autocomplete="email">
  </div>
  </div>
  </div>
  </div>
<!-- ==============================================
             AVALIAÇÃO
=============================================== -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <i class="fas fa-star mr-2"></i> Avalie nossos serviços
   </div>
    <div class="card-body">
      <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
          Selecione uma opção para cada item.
      </div>
        <?php foreach ($itens as $nome => $descricao): ?>
      <div class="card mb-3">
<!-- NOME DO ITEM -->
  <div class="card-header font-weight-bold">
    <?= htmlspecialchars($descricao) ?>
  </div>
<!-- OPÇÕES -->
  <div class="card-body py-3">
    <div class="form-row">
      <?php foreach ($opcoes as $valor => $texto): ?>
    <div class="col-md-3 mb-2 mb-md-0">
  <div class="custom-control custom-radio">
    <input type="radio" class="custom-control-input" id="<?= $nome ?>_<?= $valor ?>" name="<?= $nome ?>" value="<?= $valor ?>" required >
      <label class="custom-control-label" for="<?= $nome ?>_<?= $valor ?>"><?= $texto ?></label>
  </div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<!-- ==============================================
             SUGESTÕES
=============================================== -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white"><i class="fas fa-comment-alt mr-2"></i>
    Deixe suas sugestões
  </div>
    <div class="card-body">
      <div class="form-group mb-0">
        <label for="sugestoes">Suas sugestões, elogios ou observações</label>
        <textarea class="form-control" id="sugestoes" name="sugestoes" rows="5" placeholder="Digite aqui suas sugestões..."></textarea>

      </div>
  </div>
    </div>
 <!-- ==============================================
             BOTÃO
  =============================================== -->
<div class="text-center mb-4">
  <button type="submit" class="btn btn-primary btn-lg px-5">
    <i class="fas fa-paper-plane mr-2"></i>
    Enviar avaliação
  </button>
</div>
</form>
</div>
</main>
  <footer class="bg-body-tertiary text-center text-lg-start">
    <div class="text-center p-3 text-white" style="background-color: #250352;">
      isaiasTech © <?php echo date('Y') ?>
    </div>
  </footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

<!-- Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZ9t27NXFoaoaPmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"></script>

<!-- JavaScript da avaliação -->
<script src="js/avaliacao.js?v=<?= filemtime('js/avaliacao.js') ?>"></script>

</body>
</html>
