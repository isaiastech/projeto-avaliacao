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

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');
// ==========================================
// TOTAL DE AVALIADORES
// ==========================================

$sql = "
SELECT COUNT(*) total
FROM usuarios
WHERE nivel = 'avaliador'
AND ativo = 1
";

$totalAvaliadores = $db->getResultFromQuery($sql)
    ->fetch_assoc()['total'];


// ==========================================
// TOTAL DE USUÁRIOS ATIVOS
// ==========================================

$sql = "
SELECT COUNT(*) total
FROM usuarios
WHERE ativo = 1
AND nivel IN ('user', 'avaliador')
";
$totalUsuarios = $db->getResultFromQuery($sql)
    ->fetch_assoc()['total'];


// Cada avaliador deve avaliar todos os outros
$totalNecessario = $totalUsuarios - 1;


// ==========================================
// AVALIADORES CONCLUÍDOS
// ==========================================

$sql = "
SELECT COUNT(*) total
FROM (

    SELECT
        avaliador_id,
        COUNT(DISTINCT avaliado_id) total_avaliados

    FROM avaliacoes

    WHERE mes = ?
    AND ano = ?

    GROUP BY avaliador_id

    HAVING total_avaliados >= ?

) x
";

$concluidos = $db->getResultFromQuery(
    $sql,
    [
        (int)$mes,
        (int)$ano,
        $totalNecessario
    ]
)->fetch_assoc()['total'];


// ==========================================
// PENDENTES
// ==========================================

$pendentesTotal = $totalAvaliadores - $concluidos;


// ==========================================
// PERCENTUAL
// ==========================================

$percentual = $totalAvaliadores > 0
    ? round(($concluidos / $totalAvaliadores) * 100)
    : 0;
$sql = "
SELECT
    u.id,
    u.nome,
    ROUND(AVG(an.nota), 2) AS media
FROM usuarios u

LEFT JOIN avaliacoes a
    ON a.avaliado_id = u.id
    AND a.mes = ?
    AND a.ano = ?

LEFT JOIN avaliacao_notas an
    ON an.avaliacao_id = a.id

WHERE u.ativo = 1
AND u.nivel IN ('user', 'avaliador')

GROUP BY u.id, u.nome

HAVING media IS NOT NULL

ORDER BY media DESC, u.nome ASC
";

$ranking = $db->getResultFromQuery(
    $sql,
    [
        (int)$mes,
        (int)$ano
    ]
);
$sqlConcluidos = "
SELECT
    u.nome,
    COALESCE(x.total_avaliados,0) total_avaliados

FROM usuarios u

LEFT JOIN (

    SELECT
        avaliador_id,
        COUNT(DISTINCT avaliado_id) total_avaliados

    FROM avaliacoes

    WHERE mes = ?
    AND ano = ?

    GROUP BY avaliador_id

) x ON x.avaliador_id = u.id

WHERE u.nivel = 'avaliador'
AND u.ativo = 1

AND COALESCE(x.total_avaliados,0) >= ?

ORDER BY u.nome
";

$concluidosLista = $db->getResultFromQuery(
    $sqlConcluidos,
    [
        (int)$mes,
        (int)$ano,
        $totalNecessario
    ]
);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatórios</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#250352;">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard_admin.php">
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
                    <a class="nav-link" href="questoes.php">
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
            </span>

            <a
                href="/logout.php"
                class="btn btn-outline-light btn-sm">

                Sair

            </a>

        </div>

    </div>

</nav>
<div class="container-fluid mt-4 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Relatório de Avaliações</h2>

        <a
            href="dashboard_admin.php"
            class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-2">

            <label class="form-label">
                Mês
            </label>

            <select
                name="mes"
                class="form-select">

                <?php for ($i = 1; $i <= 12; $i++) : ?>

                    <option
                        value="<?= $i ?>"
                        <?= $i == $mes ? 'selected' : '' ?>>

                        <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>

                    </option>

                <?php endfor; ?>

            </select>

        </div>

        <div class="col-md-2">

            <label class="form-label">
                Ano
            </label>

            <input
                type="number"
                name="ano"
                class="form-control"
                value="<?= $ano ?>">

        </div>

        <div class="col-md-2 align-self-end">

            <button
                class="btn btn-primary">

                Filtrar

            </button>

        </div>

    </form>
<div class="row mb-4">

    <div class="col-md-4">

        <div class="card border-primary shadow">

            <div class="card-body text-center">

                <h6 class="text-muted">
                    Total de Avaliadores
                </h6>

                <h2 class="text-primary">
                    <?= $totalAvaliadores ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-success shadow">

            <div class="card-body text-center">

                <h6 class="text-muted">
                    Já Avaliaram
                </h6>

                <h2 class="text-success">
                    <?= $concluidos ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-danger shadow">

            <div class="card-body text-center">

                <h6 class="text-muted">
                    Pendentes
                </h6>

                <h2 class="text-danger">
                    <?= $pendentesTotal ?>
                </h2>

            </div>

        </div>

    </div>

</div>
    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            Ranking do Mês

        </div>

        <div class="card-body">

            <table class="table table-striped table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Posição</th>
                        <th>Colaborador</th>
                        <th>Média</th>
                        <th>Classificação</th>
                        <th>Detalhes</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                $posicao = 1;

                while ($row = $ranking->fetch_assoc()) :

                    $media = (float) ($row['media'] ?? 0);

                    $classe = '';

                    if ($posicao == 1) {

                        $classe = 'table-warning';

                    } elseif ($posicao == 2) {

                        $classe = 'table-light';

                    } elseif ($posicao == 3) {

                        $classe = 'table-info';
                    }

                ?>

                    <tr class="<?= $classe ?>">

                        <td>

                            <?php

                            if ($posicao == 1) {

                                echo '🥇';

                            } elseif ($posicao == 2) {

                                echo '🥈';

                            } elseif ($posicao == 3) {

                                echo '🥉';

                            } else {

                                echo $posicao;
                            }

                            ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($row['nome']) ?>

                        </td>

                        <td>

                            <?= number_format($media, 2, ',', '.') ?>

                        </td>

                        <td>

                            <?php

                            if ($media >= 4.5) {

                                echo '<span class="badge bg-success">
                                        Excelente
                                      </span>';

                            } elseif ($media >= 3.5) {

                                echo '<span class="badge bg-primary">
                                        Bom
                                      </span>';

                            } elseif ($media >= 2.5) {

                                echo '<span class="badge bg-warning text-dark">
                                        Regular
                                      </span>';

                            } else {

                                echo '<span class="badge bg-danger">
                                        A Melhorar
                                      </span>';
                            }

                            ?>

                        </td>
                        <td>

    <a
        href="relatorio_individual.php?id=<?= $row['id'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>"
        class="btn btn-primary btn-sm">

        Ver Relatório

    </a>

</td>

                    </tr>

                <?php

                    $posicao++;

                endwhile;

                ?>

                </tbody>

            </table>

        </div>

    </div>

<?php

$sqlPendentes = "
SELECT
    u.nome,
    COALESCE(x.total_avaliados,0) total_avaliados

FROM usuarios u

LEFT JOIN (

    SELECT
        avaliador_id,
        COUNT(DISTINCT avaliado_id) total_avaliados

    FROM avaliacoes

    WHERE mes = ?
    AND ano = ?

    GROUP BY avaliador_id

) x ON x.avaliador_id = u.id

WHERE u.nivel = 'avaliador'
AND u.ativo = 1

AND COALESCE(x.total_avaliados,0) < ?

ORDER BY u.nome
";

$pendentes = $db->getResultFromQuery(
    $sqlPendentes,
    [
        (int)$mes,
        (int)$ano,
        $totalNecessario
    ]
);
?>

    <div class="card shadow mt-4">

        <div class="card-header bg-danger text-white">

            Avaliadores Pendentes

        </div>

        <div class="card-body">

            <?php if ($pendentes->num_rows > 0) : ?>

                <ul class="list-group">

                    <?php while ($p = $pendentes->fetch_assoc()) : ?>

                        <li class="list-group-item d-flex justify-content-between">

    <strong>
        <?= htmlspecialchars($p['nome']) ?>
    </strong>

    <span class="badge bg-danger">

        <?= $p['total_avaliados'] ?>
        /
        <?= $totalNecessario ?>

    </span>

</li>

                    <?php endwhile; ?>

                </ul>

            <?php else : ?>

                <div class="alert alert-success mb-0">

                    Todos os avaliadores já realizaram suas avaliações neste período.

                </div>

            <?php endif; ?>

        </div>

    </div>
    <div class="card shadow mt-4">

    <div class="card-header bg-success text-white">

        Avaliadores que Já Concluíram

    </div>

    <div class="card-body">

        <?php if ($concluidosLista->num_rows > 0) : ?>

            <ul class="list-group">

                <?php while ($c = $concluidosLista->fetch_assoc()) : ?>

                    <li class="list-group-item d-flex justify-content-between">

                        <strong>
                            <?= htmlspecialchars($c['nome']) ?>
                        </strong>

                        <span class="badge bg-success">

                            <?= $c['total_avaliados'] ?>
                            /
                            <?= $totalNecessario ?>

                        </span>

                    </li>

                <?php endwhile; ?>

            </ul>

        <?php else : ?>

            <div class="alert alert-warning mb-0">

                Nenhum avaliador concluiu todas as avaliações neste período.

            </div>

        <?php endif; ?>

    </div>

</div>
<div class="card shadow mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Progresso das Avaliações
        </h6>

        <div class="progress" style="height:30px;">

            <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: <?= $percentual ?>%;">

                <?= $percentual ?>%

            </div>

        </div>

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