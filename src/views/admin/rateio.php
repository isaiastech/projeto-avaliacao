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

$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');

$valorTotal = 0;
$dados = [];
$somaMedias = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mes = (int) $_POST['mes'];
    $ano = (int) $_POST['ano'];

   $valorTotal = str_replace('.', '', $_POST['valor_total']);
$valorTotal = str_replace(',', '.', $valorTotal);
$valorTotal = (float) $valorTotal;


    $valorTotal = (float) $valorTotal;

    $sql = "
    SELECT
        u.id,
        u.nome,
        ROUND(AVG(an.nota),2) AS media

    FROM usuarios u

    LEFT JOIN avaliacoes a
        ON a.avaliado_id = u.id
        AND a.mes = ?
        AND a.ano = ?

    LEFT JOIN avaliacao_notas an
        ON an.avaliacao_id = a.id

    GROUP BY u.id, u.nome

    HAVING media IS NOT NULL

    ORDER BY media DESC
    ";

    $ranking = $db->getResultFromQuery(
        $sql,
        [
            $mes,
            $ano
        ]
    );

    while ($row = $ranking->fetch_assoc()) {

        $dados[] = $row;

        $somaMedias += (float) $row['media'];
    }

    foreach ($dados as $key => $item) {

    $dados[$key]['percentual'] =
        ($item['media'] / $somaMedias) * 100;

    $dados[$key]['valor'] =
        ($item['media'] / $somaMedias) * $valorTotal;
}
}

?>

<!DOCTYPE html>

<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Rateio de Premiação</title>
<link rel="apple-touch-icon" sizes="180x180" href="/../icons/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/../icons/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/../icons/icons/favicon-16x16.png">
<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet">
<style>

    @media print {

        .no-print {
            display:none !important;
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
            href="dashboard_admin.php"
            class="btn btn-outline-light btn-sm">

            Voltar

        </a>

    </div>

</div>

</nav>

<div class="container-fluid mt-4">


<h2 class="mb-4">
    Rateio de Premiação
</h2>

<div class="card shadow mb-4 no-print">

    <div class="card-header bg-primary text-white">

        Calcular Rateio

    </div>

    <div class="card-body">

        <form method="POST">

            <div class="row">

                <div class="col-md-2">

                    <label>Mês</label>

                    <select
                        name="mes"
                        class="form-select">

                        <?php for($i=1;$i<=12;$i++): ?>

                            <option
                                value="<?= $i ?>"
                                <?= $i == $mes ? 'selected' : '' ?>>

                                <?= str_pad($i,2,'0',STR_PAD_LEFT) ?>

                            </option>

                        <?php endfor; ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label>Ano</label>

                    <input
                        type="number"
                        name="ano"
                        class="form-control"
                        value="<?= $ano ?>">

                </div>

                <div class="col-md-3">

                    <label>Valor Total (R$)</label>

                    <input
    type="text"
    name="valor_total"
    id="valor_total"
    class="form-control"
    required
    value="<?= $valorTotal > 0
        ? number_format($valorTotal, 2, ',', '.')
        : '' ?>">

                </div>

                <div class="col-md-2 align-self-end">

                    <button
                        class="btn btn-success">

                        Calcular

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<?php if (!empty($dados)): ?>

<div class="card shadow">

    <div class="card-header bg-success text-white">

        Resultado do Rateio

    </div>

    <div class="card-body">

        <div class="mb-3">

            <h5>

                Valor Total Distribuído:

                <span class="text-success">

                    R$
                    <?= number_format(
                        $valorTotal,
                        2,
                        ',',
                        '.'
                    ) ?>

                </span>

            </h5>

        </div>

        <table
            class="table table-striped table-hover">

            <thead class="table-dark">

                <tr>

                    <th>Posição</th>
                    <th>Colaborador</th>
                    <th>Média</th>
                    <th>%</th>
                    <th>Valor</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $posicao = 1;

            foreach ($dados as $item):

            ?>

                <tr>

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

                        <?= htmlspecialchars(
                            $item['nome']
                        ) ?>

                    </td>

                    <td>

                        <?= number_format(
                            $item['media'],
                            2,
                            ',',
                            '.'
                        ) ?>

                    </td>

                    <td>

                        <?= number_format(
                            $item['percentual'],
                            2,
                            ',',
                            '.'
                        ) ?>%

                    </td>

                    <td>

                        <strong>

                            R$

                            <?= number_format(
                                $item['valor'],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </td>

                </tr>

            <?php

                $posicao++;

            endforeach;

            ?>

            </tbody>

        </table>

        <button
            onclick="window.print()"
            class="btn btn-primary no-print">

            Imprimir Rateio

        </button>

    </div>

</div>

<?php endif; ?>


</div>

<footer class="mt-5">


<div
    class="text-center text-white p-3"
    style="background-color:#250352;">

    isaiasTech © <?= date('Y') ?>

</div>

</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(function(){

    $('#valor_total').mask(
        '000.000.000.000.000,00',
        {
            reverse: true
        }
    );

});
</script>
</body>

</html>
