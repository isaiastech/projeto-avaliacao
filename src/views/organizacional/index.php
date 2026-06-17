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
    header('Location: /views/dashboard.php');
    exit;
}
use class\data\Database;

$db = new Database();


$stmt =  $db->getResultFromQuery("
    SELECT * 
    FROM perguntas
    WHERE ativo = 1
    ORDER BY id
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Clima Organizacional</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/21a7183a5f.js" crossorigin="anonymous"></script>
<style>
  .card {
      border-radius: 12px;
  }

  .card-header {
      border-radius: 12px 12px 0 0 !important;
  }

  .badge {
      font-size: 1rem;
      min-width: 40px;
  }

  .form-select,
  .form-control {
      border-radius: 8px;
  }

  .btn-success {
      padding: 10px 30px;
      font-weight: 600;
  }

  body {
      background-color: #f8f9fa;
  }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark no-print" style="background-color:#250352;">
   
<div class="container-fluid">
 <ul class="navbar-nav me-auto">

    <li class="nav-item">
        <a class="nav-link text-white"
           href="/views/admin/dashboard_admin.php">
            <i class="fas fa-home"></i>
            Painel de Controle
        </a>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white"
           href="#"
           role="button"
           data-bs-toggle="dropdown">

            <i class="fas fa-building"></i>
            Pesquisa Organizacional
        </a>

</ul>
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
<div class="row mb-4">

    <div class="col-md-4">
        <a href="#" class="text-decoration-none"
           onclick="carregarPagina('formulario.php')">

            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-3x text-primary"></i>
                    <h5 class="mt-3">Pesquisa</h5>
                </div>
            </div>

        </a>
    </div>

    <div class="col-md-4">
        <a href="#" class="text-decoration-none"
           onclick="carregarPagina('relatorios.php')">

            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-3x text-success"></i>
                    <h5 class="mt-3">Relatórios</h5>
                </div>
            </div>

        </a>
    </div>

    <div class="col-md-4">
        <a href="#" class="text-decoration-none"
           onclick="carregarPagina('graficos.php')">

            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-chart-pie fa-3x text-warning"></i>
                    <h5 class="mt-3">Gráficos</h5>
                </div>
            </div>

        </a>
    </div>

</div>
<div id="conteudo-organizacional">

    <!-- Formulário carregado aqui -->

</div>

   <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 text-white" style="background-color: #250352;">
            isaiasTech © <?php echo date('Y') ?>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
function carregarPagina(pagina)
{
    fetch(pagina)
    .then(response => response.text())
    .then(html => {

        const container = document.getElementById(
            'conteudo-organizacional'
        );

        container.innerHTML = html;

        const scripts = container.querySelectorAll("script");

        scripts.forEach(script => {

            const novoScript =
                document.createElement("script");

            novoScript.text = script.innerHTML;

            document.body.appendChild(novoScript);

            document.body.removeChild(novoScript);

        });

    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>