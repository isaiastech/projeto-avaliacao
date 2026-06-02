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
    die('Acesso negado');
}

use class\data\Database;

$db = new Database();

$id  = (int) ($_GET['id'] ?? 0);
$mes = (int) ($_GET['mes'] ?? date('m'));
$ano = (int) ($_GET['ano'] ?? date('Y'));

if ($id <= 0) {
    die('Colaborador inválido.');
}

// ==============================
// DADOS DO COLABORADOR
// ==============================

$sqlUsuario = "
SELECT
    id,
    nome
FROM usuarios
WHERE id = ?
";

$usuario = $db->getResultFromQuery(
    $sqlUsuario,
    [$id]
)->fetch_assoc();

if (!$usuario) {
    die('Colaborador não encontrado.');
}

// ==============================
// MÉDIA POR QUESTÃO
// ==============================

$sql = "
SELECT
    q.questoes,
    ROUND(AVG(an.nota),2) AS media

FROM avaliacao_notas an

INNER JOIN avaliacoes a
    ON a.id = an.avaliacao_id

INNER JOIN questoes q
    ON q.id = an.questao_id

WHERE a.avaliado_id = ?
AND a.mes = ?
AND a.ano = ?

GROUP BY q.id, q.questoes

ORDER BY q.id
";

$questoes = $db->getResultFromQuery(
    $sql,
    [
        $id,
        $mes,
        $ano
    ]
);

// ==============================
// MÉDIA GERAL
// ==============================

$sqlMedia = "
SELECT
    ROUND(AVG(an.nota),2) AS media

FROM avaliacao_notas an

INNER JOIN avaliacoes a
    ON a.id = an.avaliacao_id

WHERE a.avaliado_id = ?
AND a.mes = ?
AND a.ano = ?
";

$mediaGeral = $db->getResultFromQuery(
    $sqlMedia,
    [
        $id,
        $mes,
        $ano
    ]
)->fetch_assoc();

$mediaGeral = $mediaGeral['media'] ?? 0;

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>
<meta charset="UTF-8">
<title>Relatório Individual</title>
<link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet">
<style>
  @media print {

      .no-print {
          display: none !important;
      }

  }
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark no-print"
     style="background-color:#250352;">

<div class="container-fluid">

    <a class="navbar-brand" href="dashboard_admin.php">
        Painel Administrativo
    </a>

    <div class="ms-auto">

        <a
            href="relatorios.php"
            class="btn btn-light btn-sm">

            Voltar

        </a>

    </div>

</div>


</nav>

<div class="container mt-4">


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2>
            Relatório Individual
        </h2>

        <h4 class="text-primary">
            <?= htmlspecialchars($usuario['nome']) ?>
        </h4>

    </div>

    <button
        onclick="window.print()"
        class="btn btn-success no-print">

        Imprimir

    </button>

</div>

<div class="alert alert-info">

    <strong>Mês/Ano:</strong>

    <?= str_pad($mes,2,'0',STR_PAD_LEFT) ?>/<?= $ano ?>

</div>

<table class="table table-bordered table-hover">

    <thead class="table-dark">

        <tr>

            <th>Questão</th>
            <th width="180">Média</th>
            <th width="180">Classificação</th>

        </tr>

    </thead>

    <tbody>

    <?php while($row = $questoes->fetch_assoc()): ?>

        <?php

        $media = (float)$row['media'];

        if ($media >= 4.5) {

            $situacao =
                '<span class="badge bg-success">
                    Excelente
                </span>';

        } elseif ($media >= 3.5) {

            $situacao =
                '<span class="badge bg-primary">
                    Bom
                </span>';

        } elseif ($media >= 2.5) {

            $situacao =
                '<span class="badge bg-warning text-dark">
                    Regular
                </span>';

        } else {

            $situacao =
                '<span class="badge bg-danger">
                    A Melhorar
                </span>';
        }

        ?>

        <tr>

            <td>
                <?= htmlspecialchars($row['questoes']) ?>
            </td>

            <td>
                <?= number_format($media,2,',','.') ?>
            </td>

            <td>
                <?= $situacao ?>
            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>

<div class="card border-primary">

    <div class="card-body text-center">

        <h5 class="text-muted">
            Média Geral
        </h5>

        <h1 class="text-primary">

            <?= number_format($mediaGeral,2,',','.') ?>

        </h1>

    </div>

</div>
</div>

<footer class="mt-5">

<div
    class="text-center text-white p-3"
    style="background-color:#250352;">

    isaiasTech © <?= date('Y') ?>

</div>
</footer>

</body>

</html>
