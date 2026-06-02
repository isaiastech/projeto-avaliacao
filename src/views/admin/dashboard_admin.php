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

/*
|--------------------------------------------------------------------------
| ESTATÍSTICAS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT COUNT(*) total
FROM usuarios
WHERE ativo = 1
AND nivel IN ('user', 'avaliador')
";

$totalUsuarios = $db->getResultFromQuery($sql)
    ->fetch_assoc()['total'];

$totalNecessario = $totalUsuarios - 1;

$totalAvaliadores = $db->getResultFromQuery(
    "SELECT COUNT(*) total
     FROM usuarios
     WHERE nivel = 'avaliador'"
)->fetch_assoc()['total'];

$totalGerentes = $db->getResultFromQuery(
    "SELECT COUNT(*) total
     FROM usuarios
     WHERE nivel = 'gerente'"
)->fetch_assoc()['total'];

$totalAdmins = $db->getResultFromQuery(
    "SELECT COUNT(*) total
     FROM usuarios
     WHERE nivel = 'admin'"
)->fetch_assoc()['total'];

$totalUsers = $db->getResultFromQuery(
    "SELECT COUNT(*) total
     FROM usuarios
     WHERE nivel = 'user'"
)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/21a7183a5f.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: #f5f6fa;
        }

        .navbar-custom {
            background: #250352;
        }

        .card-dashboard {
            border: none;
            border-radius: 12px;
            transition: .2s;
        }

        .card-dashboard:hover {
            transform: translateY(-3px);
        }

        .menu-card {
            text-decoration: none;
        }

        .menu-card .card {
            transition: .2s;
        }

        .menu-card .card:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

        <div class="container-fluid">

            <a class="navbar-brand" href="#">
                <i class="fas fa-user-shield"></i>
                Painel Administrativo
            </a>

            <div class="ms-auto text-white">

                Bem-vindo:
                <strong>
                    <?= htmlspecialchars($_SESSION['nome']) ?>
                </strong>

                (<?= htmlspecialchars($_SESSION['nivel']) ?>)

                <a href="/logout.php"
                   class="btn btn-danger btn-sm ms-3">

                    Sair

                </a>

            </div>

        </div>

    </nav>

    <!-- CONTEÚDO -->

    <div class="container mt-4">

        <h2 class="mb-4">
            Dashboard Administrativa
        </h2>

        <!-- CARDS -->

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card card-dashboard bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Usuários do Sistema</h5>
                        <h1><?= $totalUsuarios ?></h1>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-success text-white">
                    <div class="card-body text-center">
                      <h5>Colaboradores</h5>
                      <h1><?= $totalAvaliadores ?></h1>
                        
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-warning">
                    <div class="card-body text-center">
                        <h1>Divisão</h1>
                        <h5><a href="rateio.php" class="btn btn-success">Divisão Proporcional</a></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-danger text-white">
                    <div class="card-body text-center">
                        <h1><?= $totalAdmins ?></h1>
                        <h5>Administradores</h5>
                    </div>
                </div>
            </div>

        </div>

        <!-- SEGUNDA LINHA -->

        <div class="row mt-3">

            <div class="col-md-3">
                <div class="card card-dashboard bg-secondary text-white">
                    <div class="card-body text-center">
                        <h1><?= $totalUsers ?></h1>
                        <h5>Aguardando Liberação</h5>
                    </div>
                </div>
            </div>

        </div>

        <!-- MENU -->

        <div class="row mt-5">

            <div class="col-md-4 mb-3">

                <a href="usuarios.php" class="menu-card">

                    <div class="card shadow">

                        <div class="card-body text-center">

                            <i class="fas fa-users fa-3x text-primary mb-3"></i>

                            <h4>
                                Gerenciar Usuários
                            </h4>

                            <p>
                                Alterar permissões e níveis
                            </p>

                        </div>
                        
                    </div>

                </a>

            </div>

            <div class="col-md-4 mb-3">

                <a href="questoes.php" class="menu-card">

                    <div class="card shadow">

                        <div class="card-body text-center">

                            <i class="fas fa-list fa-3x text-success mb-3"></i>

                            <h4>
                                Questões
                            </h4>

                            <p>
                                Gerenciar perguntas da avaliação
                            </p>

                        </div>

                    </div>

                </a>

            </div>

            <div class="col-md-4 mb-3">

                <a href="relatorios.php" class="menu-card">

                    <div class="card shadow">

                        <div class="card-body text-center">

                            <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>

                            <h4>
                                Relatórios
                            </h4>

                            <p>
                                Médias e desempenho
                            </p>

                        </div>

                    </div>

                </a>

            </div>

        </div>

        <!-- ÚLTIMOS USUÁRIOS -->

        <div class="card shadow mt-4">

            <div class="card-header bg-dark text-white">
                Últimos Usuários Cadastrados
            </div>

            <div class="card-body">

                <table class="table table-striped">

                    <thead>

                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Nível</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $usuarios = $db->getResultFromQuery(
                        "SELECT *
                         FROM usuarios
                         ORDER BY id DESC
                         LIMIT 10"
                    );

                    while ($usuario = $usuarios->fetch_assoc()) {
                    ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($usuario['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['nivel']) ?>
                            </td>

                            <td>

                                <?php if ($usuario['ativo']) { ?>

                                    <span class="badge bg-success">
                                        Ativo
                                    </span>

                                <?php } else { ?>

                                    <span class="badge bg-danger">
                                        Inativo
                                    </span>

                                <?php } ?>

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