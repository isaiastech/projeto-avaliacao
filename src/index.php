<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use class\data\Usuario;

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        $usuarioModel = new Usuario();

        $usuario = $usuarioModel->login($email, $senha);

        if (!$usuario) {
            throw new Exception('E-mail ou senha inválidos.');
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nivel'] = $usuario['nivel'];

 if ($usuario['nivel'] === 'user') {

    header('Location: views/aguardando_autorizacao.php');

} elseif (
    $usuario['nivel'] === 'admin' ||
    $usuario['nivel'] === 'gerente'
) {

    header('Location: views/admin/dashboard_admin.php');

} else {

    header('Location: views/dashboard.php');
}

        exit;

    } catch (Exception $e) {

        $erro = $e->getMessage();

    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Sistema de Avaliação</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="apple-touch-icon" sizes="180x180" href="icons/icons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="icons/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="icons/icons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="16x16" href="icons/icons/favicon.ico">
	<link rel="manifest" href="icons/site.webmanifest">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="./images/img-01.png" alt="IMG">
				</div>

				<form class="login100-form validate-form" method="POST">
					<span class="login100-form-title">
						Sistema de Avaliação 
					</span>
            <?php if (!empty($erro)): ?>
              <div class="alert alert-danger">
                <?= htmlspecialchars($erro) ?>
              </div>
            <?php endif; ?>
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="email" placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="senha" placeholder="Senha">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Entrar
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Escqueceu?
						</span>
						<a class="txt2" href="views/recuperar_senha.php">
							Usuario / Senha?
						</a>
					</div>

					<div class="text-center p-t-136">
						<a class="txt2" href="views/cadastro.php">
							Criar conta
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script src="/js/main.js"></script>
</body>
</html>