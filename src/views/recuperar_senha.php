<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\RecuperacaoSenha;

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {

        $erro = 'Informe o e-mail.';

    } else {

        try {

            $recuperacao = new RecuperacaoSenha();

            if ($recuperacao->enviarLink($email)) {

                $mensagem = 'Link de recuperação enviado para seu e-mail.';

            } else {

                $erro = 'E-mail não encontrado.';
            }

        } catch (Exception $e) {

            $erro = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<title>Recuperar Senha</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="/../images/icons/favicon.png"/>
	<link rel="stylesheet" type="text/css" href="/../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="/../css/util.css">
	<link rel="stylesheet" type="text/css" href="/../css/main.css">
</head>
<body>
<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="/../images/img-01.png" alt="IMG">
				</div>
				<form class="login100-form validate-form" action="" method="POST">
					<span class="login100-form-title">
    Recuperar Senha
</span>

<?php if (!empty($mensagem)): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($mensagem) ?>
    </div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" name="email" placeholder="Email Cadastrado">
						<span class="focus-input100"></span>
						<span class="symbol-input100"> 
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn">
                Enviar
          </button>
					</div>
          <div class="text-center p-t-12">
            <span class="txt1">
              Lembrou
            </span>
              <a class="txt2" href="../index.php">
                 Usuário / Senha?
              </a>
          </div>
				</form>
			</div>
		</div>
	</div>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
	<script src="/../js/main.js"></script>
</body>
</html>