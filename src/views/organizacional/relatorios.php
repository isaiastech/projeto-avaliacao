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
| Resumo por Setor
|--------------------------------------------------------------------------
*/

$sqlResumo = "
SELECT
    r.setor,
    COUNT(r.id) total_respostas,
    ROUND(AVG(CAST(r.resposta AS UNSIGNED)),2) media,
    SUM(CASE WHEN r.resposta='1' THEN 1 ELSE 0 END) sempre,
    SUM(CASE WHEN r.resposta='2' THEN 1 ELSE 0 END) frequencia,
    SUM(CASE WHEN r.resposta='3' THEN 1 ELSE 0 END) raramente,
    SUM(CASE WHEN r.resposta='4' THEN 1 ELSE 0 END) nunca
FROM respostas_clima r
INNER JOIN perguntas p
    ON p.id = r.pergunta_id
WHERE p.tipo <> 'aberta'
GROUP BY r.setor
ORDER BY r.setor
";

$resumo = $db->getResultFromQuery($sqlResumo);

$sqlBlocos = "
SELECT
    p.bloco,
    COUNT(r.id) total_respostas,
    ROUND(
        AVG(
            CASE
                WHEN p.id IN (16,17,20)
                    THEN (5 - CAST(r.resposta AS UNSIGNED))
                ELSE CAST(r.resposta AS UNSIGNED)
            END
        ),
    2) AS media
FROM perguntas p
INNER JOIN respostas_clima r
    ON r.pergunta_id = p.id
WHERE p.tipo <> 'aberta'
GROUP BY p.bloco
ORDER BY p.bloco;
";

$blocos = $db->getResultFromQuery($sqlBlocos);

/*
|--------------------------------------------------------------------------
| Resumo por Pergunta
|--------------------------------------------------------------------------
*/


$sqlComentarios = "
SELECT
    p.pergunta,
    r.resposta,
    r.setor,
    r.data_resposta
FROM respostas_clima r
INNER JOIN perguntas p
    ON p.id = r.pergunta_id
WHERE p.tipo = 'aberta'
AND TRIM(r.resposta) <> ''
ORDER BY r.data_resposta DESC
";
$comentarios = $db->getResultFromQuery($sqlComentarios);



$sqlPerguntas = "
SELECT
    p.id,
    p.pergunta,
    COUNT(r.id) total,
    ROUND(AVG(CAST(r.resposta AS UNSIGNED)),2) media
FROM perguntas p
LEFT JOIN respostas_clima r
    ON r.pergunta_id = p.id
WHERE p.tipo <> 'aberta'
GROUP BY p.id, p.pergunta
ORDER BY p.id
";

$perguntas = $db->getResultFromQuery($sqlPerguntas);

?>

<div class="container-fluid">

    <div class="card shadow border-0 mb-4">

        <div class="card-header bg-success text-white">
            <h4 class="mb-0">
                Resumo por Setor
            </h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">

                        <tr>
                            <th>Setor</th>
                            <th>Total</th>
                            <th>Média</th>
                            <th>Sempre</th>
                            <th>Frequência</th>
                            <th>Raramente</th>
                            <th>Nunca</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php while($row = $resumo->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($row['setor']) ?>
                            </td>

                            <td>
                                <?= $row['total_respostas'] ?>
                            </td>

                            <td>

                                <?php

                                $media = $row['media'];

                                if ($media <= 1.5) {
                                    $cor = 'success';
                                } elseif ($media <= 2.5) {
                                    $cor = 'primary';
                                } elseif ($media <= 3.5) {
                                    $cor = 'warning';
                                } else {
                                    $cor = 'danger';
                                }

                                ?>

                                <span class="badge bg-<?= $cor ?>">
                                    <?= $media ?>
                                </span>

                            </td>

                            <td>
                                <span class="badge bg-success">
                                    <?= $row['sempre'] ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    <?= $row['frequencia'] ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?= $row['raramente'] ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-danger">
                                    <?= $row['nunca'] ?>
                                </span>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

</div>
<div class="card shadow border-0 mb-4">

    <div class="card-header bg-info text-white">
        <h4 class="mb-0">
            Resumo por Bloco
        </h4>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-striped table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>Bloco</th>
                        <th>Total Respostas</th>
                        <th>Média</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php while($row = $blocos->fetch_assoc()): ?>

                    <?php

                    $media = (float)$row['media'];

                    if ($media <= 1.50) {
                          $status = 'Excelente';
                          $cor = 'success';
                      } elseif ($media <= 2.50) {
                          $status = 'Bom';
                          $cor = 'primary';
                      } elseif ($media <= 3.50) {
                          $status = 'Atenção';
                          $cor = 'warning';
                      } else {
                          $status = 'Crítico';
                          $cor = 'danger';
                      }

                    ?>

                    <tr>

                        <td><?= htmlspecialchars($row['bloco']) ?></td>

                        <td><?= $row['total_respostas'] ?></td>

                        <td><?= number_format($media, 2, ',', '.') ?></td>

                        <td>
                            <span class="badge bg-<?= $cor ?>">
                                <?= $status ?>
                            </span>
                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<div class="card shadow border-0 mt-4">

    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">
            Comentários e Sugestões
        </h4>
    </div>

    <div class="card-body">

        <?php if($comentarios->num_rows > 0): ?>

            <div class="accordion" id="accordionComentarios">

                <?php
                $contador = 1;

                while($comentario = $comentarios->fetch_assoc()):
                ?>

                    <div class="accordion-item">

                        <h2 class="accordion-header"
                            id="heading<?= $contador ?>">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $contador ?>">

                                <?= htmlspecialchars($comentario['pergunta']) ?>

                                <span class="ms-3 badge bg-primary">
                                    <?= htmlspecialchars($comentario['setor']) ?>
                                </span>

                            </button>

                        </h2>

                        <div
                            id="collapse<?= $contador ?>"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionComentarios">

                            <div class="accordion-body">

                                <div class="mb-2">

                                    <strong>Resposta:</strong>

                                    <p class="mt-2 mb-3">
                                        <?= nl2br(htmlspecialchars($comentario['resposta'])) ?>
                                    </p>

                                </div>

                                <small class="text-muted">

                                    Data:

                                    <?= date(
                                        'd/m/Y ',
                                        strtotime($comentario['data_resposta'])
                                    ) ?>

                                </small>

                            </div>

                        </div>

                    </div>

                <?php
                    $contador++;
                endwhile;
                ?>

            </div>

        <?php else: ?>

            <div class="alert alert-info mb-0">
                Nenhum comentário encontrado.
            </div>

        <?php endif; ?>

    </div>

</div>
    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                Resumo por Pergunta
            </h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">

                        <tr>
                            <th>Pergunta</th>
                            <th>Qtd. Respostas</th>
                            <th>Média</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php while($row = $perguntas->fetch_assoc()): ?>

                        <?php

                        $media = $row['media'];

                        if ($media <= 1.5) {
                            $status = 'Predomínio de Sempre';
                            $cor = 'success';
                        } elseif ($media <= 2.5) {
                            $status = 'Predomínio de com Frequência';
                            $cor = 'primary';
                        } elseif ($media <= 3.5) {
                            $status = 'Predomínio de Raramente';
                            $cor = 'warning';
                        } else {
                            $status = 'Predomínio de Nunca';
                            $cor = 'danger';
                        }
                        ?>
                        <tr>

                           <td>
                              <strong><?= $row['id'] ?>.</strong>
                              <?= htmlspecialchars($row['pergunta']) ?>
                          </td>

                            <td>
                                <?= $row['total'] ?>
                            </td>

                            <td>
                                <?= $media ?>
                            </td>

                            <td>

                                <span class="badge bg-<?= $cor ?>">
                                    <?= $status ?>
                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
<div class="card shadow border-0 mt-4">

    <div class="card-header bg-secondary text-white">
        <h4 class="mb-0">
            Interpretação das Médias
        </h4>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Faixa</th>
                    <th>Interpretação</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>1,00 até 1,50</td>
                    <td><span class="badge bg-success">Excelente</span></td>
                </tr>

                <tr>
                    <td>1,51 até 2,50</td>
                    <td><span class="badge bg-primary">Bom</span></td>
                </tr>

                <tr>
                    <td>2,51 até 3,50</td>
                    <td><span class="badge bg-warning text-dark">Atenção</span></td>
                </tr>

                <tr>
                    <td>3,51 até 4,00</td>
                    <td><span class="badge bg-danger">Crítico</span></td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

</div>