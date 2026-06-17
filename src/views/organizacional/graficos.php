<?php

require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    exit('Acesso negado');
}

if (
    $_SESSION['nivel'] !== 'admin' &&
    $_SESSION['nivel'] !== 'gerente'
) {
    exit('Acesso negado');
}

use class\data\Database;

$db = new Database();

/*
|--------------------------------------------------------------------------
| Média por Setor
|--------------------------------------------------------------------------
*/

$sqlSetor = "
    SELECT
        setor,
        ROUND(AVG(CAST(resposta AS UNSIGNED)),2) AS media
    FROM respostas_clima
    WHERE resposta REGEXP '^[1-4]$'
    GROUP BY setor
    ORDER BY setor
";

$resultSetor = $db->getResultFromQuery($sqlSetor);

$setores = [];
$mediasSetor = [];

while ($row = $resultSetor->fetch_assoc()) {
    $setores[] = $row['setor'];
    $mediasSetor[] = (float)$row['media'];
}

/*
|--------------------------------------------------------------------------
| Média por Pergunta
|--------------------------------------------------------------------------
*/

$sqlPergunta = "
    SELECT
        pergunta_id,
        ROUND(AVG(CAST(resposta AS UNSIGNED)),2) AS media
    FROM respostas_clima
    WHERE resposta REGEXP '^[1-4]$'
    GROUP BY pergunta_id
    ORDER BY pergunta_id
";

$resultPergunta = $db->getResultFromQuery($sqlPergunta);

$perguntas = [];
$mediasPergunta = [];

while ($row = $resultPergunta->fetch_assoc()) {
    $perguntas[] = 'Pergunta ' . $row['pergunta_id'];
    $mediasPergunta[] = (float)$row['media'];
}

/*
|--------------------------------------------------------------------------
| Total de respostas
|--------------------------------------------------------------------------
*/

$resultTotal = $db->getResultFromQuery("
    SELECT COUNT(*) total
    FROM respostas_clima
");

$totalRespostas = $resultTotal->fetch_assoc()['total'] ?? 0;
?>

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total de Respostas</h6>
                    <h2 class="text-primary"><?= $totalRespostas ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Setores</h6>
                    <h2 class="text-success"><?= count($setores) ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Perguntas</h6>
                    <h2 class="text-warning"><?= count($perguntas) ?></h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white">
            Média das Respostas por Setor
        </div>

        <div class="card-body">
            <canvas id="graficoSetores"></canvas>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            Média das Respostas por Pergunta
        </div>

        <div class="card-body">
            <canvas id="graficoPerguntas"></canvas>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Interpretação:</strong>

        <ul class="mb-0">
            <li>1,00 a 1,50 → Excelente</li>
            <li>1,51 a 2,50 → Bom</li>
            <li>2,51 a 3,50 → Atenção</li>
            <li>3,51 a 4,00 → Crítico</li>
        </ul>
    </div>

</div>

<script>

if (window.graficoSetorObj) {
    window.graficoSetorObj.destroy();
}

if (window.graficoPerguntaObj) {
    window.graficoPerguntaObj.destroy();
}

window.graficoSetorObj = new Chart(
    document.getElementById('graficoSetores'),
    {
        type: 'bar',
        data: {
            labels: <?= json_encode($setores) ?>,
            datasets: [{
                label: 'Média por Setor',
                data: <?= json_encode($mediasSetor) ?>,
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4
                }
            }
        }
    }
);

window.graficoPerguntaObj = new Chart(
    document.getElementById('graficoPerguntas'),
    {
        type: 'line',
        data: {
            labels: <?= json_encode($perguntas) ?>,
            datasets: [{
                label: 'Média por Pergunta',
                data: <?= json_encode($mediasPergunta) ?>,
                borderColor: '#0d6efd',
                backgroundColor: '#0d6efd',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4
                }
            }
        }
    }
);

</script>