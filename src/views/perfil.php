<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\Database;

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

$db = new Database();

$usuario = $db->getResultFromQuery(
    "SELECT id, nome, email
     FROM usuarios
     WHERE id = ?",
    [$_SESSION['usuario_id']]
)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            Meu Perfil
        </div>

        <div class="card-body">

            <?php if(isset($_SESSION['sucesso'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['sucesso'] ?>
                </div>
                <?php unset($_SESSION['sucesso']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['erro'] ?>
                </div>
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>

            <form method="POST" action="salvar_perfil.php">

                <div class="mb-3">
                    <label class="form-label">Nome</label>

                    <input
                        type="text"
                        name="nome"
                        class="form-control"
                        value="<?= htmlspecialchars($usuario['nome']) ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($usuario['email']) ?>"
                        required>
                </div>

                <hr>

                <h5>Alterar Senha</h5>

                <div class="mb-3">
                    <label class="form-label">
                        Senha Atual
                    </label>

                    <input
                        type="password"
                        name="senha_atual"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Nova Senha
                    </label>

                    <input
                        type="password"
                        name="nova_senha"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Confirmar Nova Senha
                    </label>

                    <input
                        type="password"
                        name="confirmar_senha"
                        class="form-control">
                </div>

                <button
                    class="btn btn-success">

                    Salvar Alterações

                </button>

            </form>

        </div>

    </div>

</div>
</body>
</html>