<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\Usuario;

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmar_senha'];

        if (empty($nome) || empty($email) || empty($senha)) {
            throw new Exception('Preencha todos os campos.');
        }

        if ($senha !== $confirmarSenha) {
            throw new Exception('As senhas não conferem.');
        }

        $usuario = new Usuario();

        $usuario->cadastrar(
            $nome,
            $email,
            $senha,
            'user'
        );

        header('Location: ../index.php');
        exit;

    } catch (Exception $e) {

        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/../css/util.css">
    <link rel="stylesheet" type="text/css" href="/../css/main.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon.ico">
    <link rel="manifest" href="/../icons/site.webmanifest">
    <link rel="stylesheet" href="/../css/cadastro.css">
    <title>Cadastro</title>
</head>
<body>
    <div class="box">
        <div class="img-box">
            <img src="/../images/img-01.png">
        </div>

        <div class="form-box">
            <h2>Criar Conta</h2>
            <p>Já possui Cadastro? <a href="/../index.php"> Login </a></p>

            <?php if (!empty($erro)) { ?>
                <p style="color:red; font-weight:bold;"><?php echo $erro; ?></p>
            <?php } ?>

            <form action="" method="POST">

                <div class="input-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome"
                        placeholder="Digite o seu nome completo"
                        value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                        required>
                </div>

                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email"
                        placeholder="Digite o seu email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required>
                </div>

                <div class="input-group w50">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha"
                        placeholder="Digite sua senha"
                        required>
                </div>

                <div class="input-group w50">
                    <label for="confirmarsenha">Confirmar Senha</label>
                    <input
    type="password"
    id="confirmar_senha"
    name="confirmar_senha"
    placeholder="Confirme a senha"
    required
>
                </div>

                <div class="input-group">
                    <button type="submit">Cadastrar</button>
                </div>

            </form>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
            <script>
               $('form').on('submit', function () {
    if ($('#senha').val() != $('#confirmar_senha').val()) {
        alert('As Senhas não são iguais!');
        return false;
    }
});
            </script>
        </div>
    </div>
</body>
</html>