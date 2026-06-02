<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

if (
    $_SESSION['nivel'] !== 'admin' &&
    $_SESSION['nivel'] !== 'gerente'
) {
    header('Location: /views/dashboard.php');
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use class\data\Database;

$db = new Database();

$mensagem = '';
$erro = '';

/*
|--------------------------------------------------------------------------
| SALVAR ALTERAÇÕES
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $id = (int) $_POST['id'];
        $nivel = $_POST['nivel'];
        $ativo = (int) $_POST['ativo'];

        // gerente não pode alterar admin
        if ($_SESSION['nivel'] === 'gerente') {

            $usuarioAtual = $db->getResultFromQuery(
                "SELECT nivel FROM usuarios WHERE id = ?",
                [$id]
            )->fetch_assoc();

            if ($usuarioAtual['nivel'] === 'admin') {
                throw new Exception(
                    'Você não possui permissão para alterar um administrador.'
                );
            }
        }

        $sql = "
            UPDATE usuarios
            SET
                nivel = ?,
                ativo = ?
            WHERE id = ?
        ";

        $db->execute(
            $sql,
            [
                $nivel,
                $ativo,
                $id
            ]
        );

        $mensagem = 'Usuário atualizado com sucesso!';

    } catch (Exception $e) {

        $erro = $e->getMessage();

    }
}

/*
|--------------------------------------------------------------------------
| LISTA DE USUÁRIOS
|--------------------------------------------------------------------------
*/

$usuarios = $db->getResultFromQuery(
    "SELECT *
     FROM usuarios
     ORDER BY nome"
);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">

        <h2>Gerenciar Usuários</h2>

        <a href="dashboard_admin.php"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <?php if ($mensagem): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>

    <?php if ($erro): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>

    <div class="card shadow">

        <div class="card-body">

            <table class="table table-striped table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Nível</th>
                        <th>Status</th>
                        <th width="220">Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($usuario = $usuarios->fetch_assoc()) { ?>

                    <tr>

                        <form method="POST">

                            <td>

                                <?= $usuario['id'] ?>

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $usuario['id'] ?>">

                            </td>

                            <td>

                                <?= htmlspecialchars($usuario['nome']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($usuario['email']) ?>

                            </td>

                            <td>

                                <select
                                    name="nivel"
                                    class="form-select">

                                    <option
                                        value="user"
                                        <?= $usuario['nivel'] === 'user' ? 'selected' : '' ?>>
                                        Usuário
                                    </option>

                                    <option
                                        value="avaliador"
                                        <?= $usuario['nivel'] === 'avaliador' ? 'selected' : '' ?>>
                                        Avaliador
                                    </option>

                                    <option
                                        value="gerente"
                                        <?= $usuario['nivel'] === 'gerente' ? 'selected' : '' ?>>
                                        Gerente
                                    </option>

                                    <?php if ($_SESSION['nivel'] === 'admin') { ?>

                                        <option
                                            value="admin"
                                            <?= $usuario['nivel'] === 'admin' ? 'selected' : '' ?>>
                                            Administrador
                                        </option>

                                    <?php } ?>

                                </select>

                            </td>

                            <td>

                                <select
                                    name="ativo"
                                    class="form-select">

                                    <option
                                        value="1"
                                        <?= $usuario['ativo'] ? 'selected' : '' ?>>
                                        Ativo
                                    </option>

                                    <option
                                        value="0"
                                        <?= !$usuario['ativo'] ? 'selected' : '' ?>>
                                        Inativo
                                    </option>

                                </select>

                            </td>

                            <td>

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm">

                                    Salvar

                                </button>

                            </td>

                        </form>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
   <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 text-white" style="background-color: #250352;">
            isaiasTech © <?php echo date('Y') ?>
        </div>
    </footer>
</html>