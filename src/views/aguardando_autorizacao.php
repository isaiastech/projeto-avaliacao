<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aguardando Autorização</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card shadow">

                <div class="card-header bg-warning">
                    <h4 class="mb-0">
                        Acesso Restrito
                    </h4>
                </div>

                <div class="card-body text-center">

                    <h3>
                        Olá, <?= htmlspecialchars($_SESSION['nome']) ?>
                    </h3>

                    <p class="mt-4">

                        Seu cadastro foi realizado com sucesso.

                    </p>

                    <p>

                        Para realizar avaliações é necessário possuir o perfil
                        <strong>Avaliador</strong>.

                    </p>

                    <p>

                        Solicite autorização ao gerente do sistema.

                    </p>

                    <a href="/logout.php" class="btn btn-danger mt-3">
                        Sair do Sistema
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>