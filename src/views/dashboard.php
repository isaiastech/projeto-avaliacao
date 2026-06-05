<?php
require_once __DIR__ . '/../vendor/autoload.php';
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
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon.ico">
    <link rel="manifest" href="/../icons/site.webmanifest">
    <script src="https://kit.fontawesome.com/21a7183a5f.js" crossorigin="anonymous"></script>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<style>
        body{
          background:#f4f6f9;
         }
        footer.bg-body-tertiary {
            position: relative;
            bottom: 0;
            width: 100%;
            padding: 15px 0;
            color: #fff;
            background-color: #250352;
            text-align: center;
        }

        footer p {
            margin: 0;
        }
        .sidebar{
            min-height:100vh;
            background:#fff;
            box-shadow:0 0 15px rgba(0,0,0,.08);
        }
        .sidebar .nav-link{
          color:#444;
          border-radius:8px;
          margin-bottom:4px;
          transition:.3s;
        }

        .sidebar .nav-link:hover{
            background:#250352;
            color:#fff;
        }
</style>
</head>

<body>
    <nav class="navbar navbar-expand-md" style="background-color: #250352; color: #fff">
        <div class="d-flex align-items-center">
          <img src="/../images/image-painel.png" class="rounded-circle shadow-sm" width="50" height="50">
        <div class="ml-3">
          <div class="font-weight-bold text-white">
                  Olá, <?= htmlspecialchars($_SESSION['nome']) ?>
          </div>
            <small class="text-light">
              Sistema de Avaliação
           </small>
          </div>
        </div>
        <ul class="navbar-nav">
          <li class="nav-item">
              <a class="nav-link text-white" href="/views/perfil.php">
                  Atualizar Meu Perfil
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

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block sidebar collapse" id="sidebarMenu" style='background-color:#fff;border-right: 1px solid #fff'>
                <div class="sidebar-sticky">
                    <ul class="nav flex-column" style="padding-top: 20px">
                        <li class="nav-item">
                            <a class="nav-link active" href="?status=1" style="color:#008000;text-decoration: none">
                                <i class="fas fa-tachometer-alt"></i>&nbsp;Notas já Lançadas
                            </a>
                        </li>
<?php

$db = new Database();


if ($_SESSION['nivel'] === 'avaliador') {

    $sql = "
        SELECT *
        FROM usuarios
        WHERE ativo = 1
        AND nivel IN ('user', 'avaliador')
        ORDER BY nome ASC
    ";

} else {

    // Gerente e Admin podem avaliar todos,
    // exceto administradores
    $sql = "
        SELECT *
        FROM usuarios
        WHERE ativo = 1
        AND nivel <> 'admin'
        ORDER BY nome ASC
    ";
}

$colaboradores = $db->getResultFromQuery($sql);

while ($colaborador = $colaboradores->fetch_assoc()) {

    // Não mostrar o próprio usuário
    if ($colaborador['id'] == $_SESSION['usuario_id']) {
        continue;
    }

    $mes = date('m');
    $ano = date('Y');

    $sqlAvaliado = "
        SELECT id
        FROM avaliacoes
        WHERE avaliador_id = ?
        AND avaliado_id = ?
        AND mes = ?
        AND ano = ?
        LIMIT 1
    ";

    $avaliado = $db->getResultFromQuery(
        $sqlAvaliado,
        [
            $_SESSION['usuario_id'],
            $colaborador['id'],
            $mes,
            $ano
        ]
    );

    $jaAvaliou = $avaliado->num_rows > 0;
?>

<li class="nav-item">

    <a class="nav-link"
       href="?avaliar=<?php echo $colaborador['id']; ?>"
       style="text-decoration:none">

        <?php if ($jaAvaliou): ?>
            <i class="fa fa-check-circle text-success"></i>
        <?php else: ?>
            <i class="fa fa-times-circle text-danger"></i>
        <?php endif; ?>

        <?php echo htmlspecialchars($colaborador['nome']); ?>

    </a>

</li>

<?php } ?>
                    </ul>
                </div>
            </nav>

            <!-- Conteúdo -->
<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['sucesso']; ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro']; ?>
    </div>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Avaliação Mensal</h1>
                </div>
            <?php

$db = new Database();

if (isset($_GET['status'])) {

    $mes = date('m');
    $ano = date('Y');

    echo '<div class="card shadow">';
    echo '<div class="card-header bg-info text-white">';
    echo '<h5 class="mb-0">Status das Avaliações do Mês</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    if ($_SESSION['nivel'] === 'avaliador') {

    $usuarios = $db->getResultFromQuery(
        "SELECT *
         FROM usuarios
         WHERE id <> ?
         AND ativo = 1
         AND nivel IN ('user', 'avaliador')
         ORDER BY nome",
        [$_SESSION['usuario_id']]
    );

} else {

    $usuarios = $db->getResultFromQuery(
        "SELECT *
         FROM usuarios
         WHERE id <> ?
         AND ativo = 1
         AND nivel <> 'admin'
         ORDER BY nome",
        [$_SESSION['usuario_id']]
    );

}

    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Colaborador</th>';
    echo '<th>Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($usuario = $usuarios->fetch_assoc()) {

        $avaliacao = $db->getResultFromQuery(
            "SELECT id
             FROM avaliacoes
             WHERE avaliador_id = ?
             AND avaliado_id = ?
             AND mes = ?
             AND ano = ?
             LIMIT 1",
            [
                $_SESSION['usuario_id'],
                $usuario['id'],
                $mes,
                $ano
            ]
        );

        $avaliado = $avaliacao->num_rows > 0;

        echo '<tr>';
        echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';

        if ($avaliado) {
            echo '<td><span class="badge badge-success">Avaliado</span></td>';
        } else {
            echo '<td><span class="badge badge-danger">Pendente</span></td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</div>';

}

if (isset($_GET['avaliar'])) {

    $idAvaliado = (int) $_GET['avaliar'];
    $usuarioAvaliado = $db->getResultFromQuery(
    "SELECT id, nome, nivel
     FROM usuarios
     WHERE id = ?",
    [$idAvaliado]
)->fetch_assoc();

if (!$usuarioAvaliado) {

    die('Usuário não encontrado.');

}

// Avaliador pode avaliar user e avaliador
if (
    $_SESSION['nivel'] === 'avaliador'
    && !in_array(
        $usuarioAvaliado['nivel'],
        ['user', 'avaliador']
    )
) {

    die('Você não possui permissão para avaliar este usuário.');

}

    // Não permitir autoavaliação
    if ($idAvaliado == $_SESSION['usuario_id']) {

        echo '<div class="alert alert-danger">
                Você não pode avaliar a si mesmo.
              </div>';

    } else {

        // Busca usuário avaliado
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $usuario = $db->getResultFromQuery(
            $sql,
            [$idAvaliado]
        )->fetch_assoc();

        if ($usuario) {

            // Busca questões
            $questoes = $db->getResultFromQuery(
                "SELECT * FROM questoes WHERE ativo = 1 ORDER BY id"
            );
?>

<div class="card shadow">

    <div class="card-header bg-primary text-white">
        Avaliando:
        <strong>
            <?= htmlspecialchars($usuario['nome']) ?>
        </strong>
    </div>

    <div class="card-body">

        <form method="POST" action="salvar_avaliacao.php">

            <input type="hidden"
                   name="avaliado_id"
                   value="<?= $usuario['id'] ?>">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Questão</th>
                        <th width="180">Nota</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($questao = $questoes->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($questao['questoes']) ?>
                        </td>

                        <td>

                            <select
                                name="nota[<?= $questao['id'] ?>]"
                                class="form-control"
                                required>

                                <option value="">Selecione</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>

                            </select>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

            <button
                type="submit"
                class="btn btn-success">

                Salvar Avaliação

            </button>

        </form>

    </div>

</div>

<?php
        }
    }

} else {

    echo '
        <div class="alert alert-info">
            Selecione um colaborador no menu lateral para avaliar.
        </div>
    ';
}
?>    
            </main>
        </div>
    </div>

    <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 text-white" style="background-color: #250352;">
            isaiasTech © <?php echo date('Y') ?>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script>
        $('.navbar-toggler').on('click', function() {
            $('#sidebarMenu').toggleClass('collapse');
        });
    </script>
</body>

</html>