<?php

require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

if (
    $_SESSION['nivel'] !== 'admin' &&
    $_SESSION['nivel'] !== 'gerente'
) {
    die('Acesso negado.');
}

use class\data\Database;

$db = new Database();

$mensagem = '';
$erro = '';

/*
|--------------------------------------------------------------------------
| CADASTRAR
|--------------------------------------------------------------------------
*/
if (isset($_POST['cadastrar'])) {

    try {

        $questao = trim($_POST['questao']);

        if (empty($questao)) {
            throw new Exception('Informe a questão.');
        }

        $sql = "
            INSERT INTO questoes
            (
                questoes
            )
            VALUES
            (
                ?
            )
        ";

        $db->execute($sql, [$questao]);

        $mensagem = 'Questão cadastrada com sucesso.';

    } catch (Exception $e) {

        $erro = $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| EDITAR
|--------------------------------------------------------------------------
*/
if (isset($_POST['editar'])) {

    try {

        $id = (int) $_POST['id'];
        $questao = trim($_POST['questao']);

        if (empty($questao)) {
            throw new Exception('Informe a questão.');
        }

        $sql = "
            UPDATE questoes
            SET questoes = ?
            WHERE id = ?
        ";

        $db->execute(
            $sql,
            [
                $questao,
                $id
            ]
        );

        $mensagem = 'Questão atualizada com sucesso.';

    } catch (Exception $e) {

        $erro = $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| EXCLUIR
|--------------------------------------------------------------------------
*/
if (isset($_GET['excluir'])) {

    try {

        $id = (int) $_GET['excluir'];

        $sql = "
              UPDATE questoes
              SET ativo = 0
              WHERE id = ?
          ";

$db->execute($sql, [$id]);

        $db->execute($sql, [$id]);

        header('Location: questoes.php');
        exit;

    } catch (Exception $e) {

        $erro = $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| EDITAR REGISTRO
|--------------------------------------------------------------------------
*/
$editar = null;

if (isset($_GET['editar'])) {

    $id = (int) $_GET['editar'];

    $result = $db->getResultFromQuery(
        "SELECT * FROM questoes WHERE id = ?",
        [$id]
    );

    $editar = $result->fetch_assoc();
}

/*
|--------------------------------------------------------------------------
| LISTAGEM
|--------------------------------------------------------------------------
*/
$questoes = $db->getResultFromQuery(
    "SELECT *
     FROM questoes
     WHERE ativo = 1
     ORDER BY id"
);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <title>Questões de Avaliação</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow"
     style="background-color:#250352;">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard_admin.php">
            <i class="fas fa-user-shield"></i>
            Painel Administrativo
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuAdmin">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="menuAdmin">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="dashboard_admin.php">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="usuarios.php">
                        Usuários
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="questoes.php">
                        Questões
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="relatorios.php">
                        Relatórios
                    </a>
                </li>

            </ul>

            <span class="navbar-text text-white me-3">

                <?= htmlspecialchars($_SESSION['nome']) ?>

                (<?= strtoupper($_SESSION['nivel']) ?>)

            </span>

            <a
                href="/logout.php"
                class="btn btn-outline-light">

                Sair

            </a>

        </div>

    </div>

</nav>
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Questões de Avaliação</h2>

        <a
            href="dashboard_admin.php"
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

    <div class="card mb-4">

        <div class="card-header">

            <?= $editar ? 'Editar Questão' : 'Nova Questão' ?>

        </div>

        <div class="card-body">

            <form method="POST">

                <?php if ($editar): ?>

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $editar['id'] ?>">

                <?php endif; ?>

                <div class="mb-3">

                    <label class="form-label">
                        Questão
                    </label>

                    <input
                        type="text"
                        name="questao"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($editar['questoes'] ?? '') ?>">

                </div>

                <button
                    type="submit"
                    name="<?= $editar ? 'editar' : 'cadastrar' ?>"
                    class="btn btn-primary">

                    <?= $editar ? 'Salvar Alterações' : 'Cadastrar' ?>

                </button>

                <?php if ($editar): ?>

                    <a
                        href="questoes.php"
                        class="btn btn-secondary">

                        Cancelar

                    </a>

                <?php endif; ?>

            </form>

        </div>

    </div>

    <div class="card">

        <div class="card-header">

            Questões Cadastradas

        </div>

        <div class="card-body">

            <table class="table table-striped table-hover">

                <thead>

                <tr>

                    <th>ID</th>
                    <th>Questão</th>
                    <th width="180">Ações</th>

                </tr>

                </thead>

                <tbody>

                <?php while ($row = $questoes->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?= $row['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['questoes']) ?>
                        </td>

                        <td>

                            <a
                                href="?editar=<?= $row['id'] ?>"
                                class="btn btn-warning btn-sm">

                                Editar

                            </a>

                            <a
                                href="?excluir=<?= $row['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Deseja excluir esta questão?')">

                                Excluir

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
   <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 text-white" style="background-color: #250352;">
            isaiasTech © <?php echo date('Y') ?>
        </div>
    </footer>
</body>
</html>