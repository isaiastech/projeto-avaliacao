<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\Usuario;
use Exception;

$erro = '';
$sucesso = '';

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $erro = 'Token não informado.';
}

$usuario = new Usuario();

if (empty($erro)) {

    $dadosToken = $usuario->validarToken($token);

    if (!$dadosToken) {
        $erro = 'Token inválido ou expirado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro)) {

    try {

        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if (empty($senha)) {
            throw new Exception('Informe a nova senha.');
        }

        if ($senha !== $confirmarSenha) {
            throw new Exception('As senhas não conferem.');
        }

        if (strlen($senha) < 6) {
            throw new Exception('A senha deve possuir pelo menos 6 caracteres.');
        }

        $usuario->redefinirSenha(
            $token,
            $senha
        );

        $sucesso = 'Senha alterada com sucesso.';

    } catch (Exception $e) {

        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Redefinir Senha</h4>
        </div>
  <div class="card-body">
    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>
      <?php if (!empty($sucesso)): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($sucesso) ?>
    </div>
      <div class="text-center">
        <a href="../index.php" class="btn btn-primary">
           Ir para o Login
        </a>
    </div>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="mb-3">
          <label class="form-label">
             Nova Senha
          </label>
          <input type="password" name="senha" class="form-control" required>
        </div>
    <div class="mb-3">
      <label class="form-label">
         Confirmar Senha
      </label>
      <input type="password" name="confirmar_senha" class="form-control" required >
    </div>
      <div class="d-grid">
          <button type="submit" class="btn btn-success">
              Alterar Senha
            </button>
      </div>  
    </form>
  <?php endif; ?>
</div>
</div>
</div>
</div>
</div>

</body>
</html>