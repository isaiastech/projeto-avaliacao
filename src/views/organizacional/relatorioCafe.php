<?php

require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| VERIFICA LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['usuario_id'])) {
    exit('Acesso negado');
}

/*
|--------------------------------------------------------------------------
| VERIFICA NÍVEL DE ACESSO
|--------------------------------------------------------------------------
*/
if (
    !isset($_SESSION['nivel']) ||
    !in_array(
        $_SESSION['nivel'],
        ['admin', 'gerente'],
        true
    )
) {
    exit('Acesso negado');
}

use class\data\Database;
/*
|--------------------------------------------------------------------------
| CONEXÃO
|--------------------------------------------------------------------------
*/

$db = new Database();
/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO DA PAGINAÇÃO
|--------------------------------------------------------------------------
*/
$limite = 20;
$pagina = isset($_GET['pagina'])
    ? (int) $_GET['pagina']
    : 1;
$pagina = max(1, $pagina);
/*
|--------------------------------------------------------------------------
| RECEBE OS FILTROS
|--------------------------------------------------------------------------
*/
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$apto = trim(
    $_GET['apto'] ?? ''
);
/*
|--------------------------------------------------------------------------
| VALIDA DATA INICIAL
|--------------------------------------------------------------------------
*/
if (!empty($dataInicio)) {

    $data = DateTime::createFromFormat(
        'Y-m-d',
        $dataInicio
    );

    if (
        !$data ||
        $data->format('Y-m-d') !== $dataInicio
    ) {

        $dataInicio = '';

    }
}
/*
|--------------------------------------------------------------------------
| VALIDA DATA FINAL
|--------------------------------------------------------------------------
*/
if (!empty($dataFim)) {

    $data = DateTime::createFromFormat(
        'Y-m-d',
        $dataFim
    );

    if (
        !$data ||
        $data->format('Y-m-d') !== $dataFim
    ) {

        $dataFim = '';

    }

}
/*
|--------------------------------------------------------------------------
| MONTA OS FILTROS SQL
|--------------------------------------------------------------------------
*/
$where = [];
$params = [];
/*
|--------------------------------------------------------------------------
| FILTRO DATA INICIAL
|--------------------------------------------------------------------------
*/
if (!empty($dataInicio)) {

    $where[] =
        "data_avaliacao >= ?";

    $params[] =
        $dataInicio;
}
/*
|--------------------------------------------------------------------------
| FILTRO DATA FINAL
|--------------------------------------------------------------------------
*/
if (!empty($dataFim)) {

    $where[] =
        "data_avaliacao <= ?";

    $params[] =
        $dataFim;
}
/*
|--------------------------------------------------------------------------
| FILTRO APARTAMENTO
|--------------------------------------------------------------------------
*/
if (!empty($apto)) {

    $where[] =
        "apto LIKE ?";

    $params[] =
        '%' . $apto . '%';
}
/*
|--------------------------------------------------------------------------
| MONTA WHERE
|--------------------------------------------------------------------------
*/
$whereSql = '';
if (!empty($where)) {

    $whereSql =
        'WHERE ' .
        implode(
            ' AND ',
            $where
        );
}
/*
|--------------------------------------------------------------------------
| TOTAL DE REGISTROS
|--------------------------------------------------------------------------
|
| Esta consulta retorna somente a quantidade.
|
*/
$sqlTotal = "

    SELECT
        COUNT(*) AS total

    FROM
        avaliacoes_hospedes

    $whereSql

";
$resultTotal =
    $db->getResultFromQuery(
        $sqlTotal,
        $params
    );
$rowTotal =
    $resultTotal->fetch_assoc();
$totalRegistros =
    (int) (
        $rowTotal['total']
        ?? 0
    );
/*
|--------------------------------------------------------------------------
| TOTAL DE PÁGINAS
|--------------------------------------------------------------------------
*/
$totalPaginas = max(
    1,
    (int) ceil(
        $totalRegistros /
        $limite
    )
);
/*
|--------------------------------------------------------------------------
| CORRIGE PÁGINA
|--------------------------------------------------------------------------
*/
if (
    $pagina >
    $totalPaginas
) {

    $pagina =
        $totalPaginas;

}
/*
|--------------------------------------------------------------------------
| OFFSET
|--------------------------------------------------------------------------
*/
$offset =
    (
        $pagina - 1
    ) *
    $limite;
/*
|--------------------------------------------------------------------------
| ESTATÍSTICAS DO CAFÉ DA MANHÃ
|--------------------------------------------------------------------------
*/
$sqlEstatisticasCafe = "

    SELECT

        SUM(
            cafe_manha = 'otimo'
        ) AS otimo,

        SUM(
            cafe_manha = 'bom'
        ) AS bom,

        SUM(
            cafe_manha = 'satisfatorio'
        ) AS satisfatorio,

        SUM(
            cafe_manha = 'ruim'
        ) AS ruim

    FROM
        avaliacoes_hospedes

    $whereSql

";
$resultEstatisticasCafe =
    $db->getResultFromQuery(
        $sqlEstatisticasCafe,
        $params
    );
$estatisticasCafe =
    $resultEstatisticasCafe
        ->fetch_assoc();
$totalOtimo =
    (int) (
        $estatisticasCafe[
            'otimo'
        ] ?? 0
    );
$totalBom =
    (int) (
        $estatisticasCafe[
            'bom'
        ] ?? 0
    );
$totalSatisfatorio =
    (int) (
        $estatisticasCafe[
            'satisfatorio'
        ] ?? 0
    );
$totalRuim =
    (int) (
        $estatisticasCafe[
            'ruim'
        ] ?? 0
    );
/*
|--------------------------------------------------------------------------
| CAMPOS DE AVALIAÇÃO
|--------------------------------------------------------------------------
*/
$camposAvaliacao = [

    'cafe_manha' =>
        'Café da Manhã',

    'colchao' =>
        'Colchão',

    'travesseiro' =>
        'Travesseiro',

    'limpeza' =>
        'Limpeza',

    'frigobar' =>
        'Frigobar',

    'chuveiro_aquecimento' =>
        'Chuveiro / Aquecimento',

    'chuveiro_ducha' =>
        'Chuveiro / Ducha',

    'ar_condicionado' =>
        'Ar-Condicionado',

    'roupa_cama' =>
        'Roupa de Cama',

    'internet' =>
        'Internet',

    'bar' =>
        'Bar',

    'atendimento' =>
        'Atendimento',

    'reserva' =>
        'Reserva',

    'recepcao' =>
        'Recepção',

    'camareira' =>
        'Camareira',

    'garcom' =>
        'Garçom'
];
/*
|--------------------------------------------------------------------------
| ESTATÍSTICAS POR CRITÉRIO
|--------------------------------------------------------------------------
*/
$estatisticas = [];
foreach (
    $camposAvaliacao
    as $campo =>
    $nome
) {


    $sql = "

        SELECT

            SUM(
                $campo = 'otimo'
            ) AS otimo,

            SUM(
                $campo = 'bom'
            ) AS bom,

            SUM(
                $campo = 'satisfatorio'
            ) AS satisfatorio,

            SUM(
                $campo = 'ruim'
            ) AS ruim

        FROM
            avaliacoes_hospedes

        $whereSql

    ";


    $resultado =
        $db->getResultFromQuery(
            $sql,
            $params
        );


    $dados =
        $resultado->fetch_assoc();


    $estatisticas[
        $campo
    ] = [

        'nome' =>
            $nome,

        'otimo' =>
            (int) (
                $dados[
                    'otimo'
                ] ?? 0
            ),

        'bom' =>
            (int) (
                $dados[
                    'bom'
                ] ?? 0
            ),

        'satisfatorio' =>
            (int) (
                $dados[
                    'satisfatorio'
                ] ?? 0
            ),

        'ruim' =>
            (int) (
                $dados[
                    'ruim'
                ] ?? 0
            )

    ];

}
/*
|--------------------------------------------------------------------------
| BUSCA OS REGISTROS DA PÁGINA
|--------------------------------------------------------------------------
|
| IMPORTANTE:
|
| O LIMIT e OFFSET são inteiros validados pelo PHP.
| Não são enviados como parâmetros preparados.
|
*/
$sqlAvaliacoes = "

    SELECT

        id,

        hospede,

        apto,

        fone,

        email,

        data_avaliacao,

        cafe_manha,

        colchao,

        travesseiro,

        limpeza,

        frigobar,

        chuveiro_aquecimento,

        chuveiro_ducha,

        ar_condicionado,

        roupa_cama,

        internet,

        bar,

        atendimento,

        reserva,

        recepcao,

        camareira,

        garcom,

        sugestoes,

        criado_em

    FROM
        avaliacoes_hospedes

    $whereSql

    ORDER BY

        data_avaliacao DESC,

        criado_em DESC

    LIMIT
        $limite

    OFFSET
        $offset

";
$resultAvaliacoes =
    $db->getResultFromQuery(
        $sqlAvaliacoes,
        $params
    );
/*
|--------------------------------------------------------------------------
| FUNÇÃO PARA EXIBIR AVALIAÇÃO
|--------------------------------------------------------------------------
*/
function labelAvaliacao(
    ?string $valor
): string {


    switch ($valor) {


        case 'otimo':

            return '

                <span
                    class="
                        badge
                        bg-success
                    "
                >

                    <i
                        class="
                            fas
                            fa-star
                        "
                    ></i>

                    Ótimo

                </span>

            ';


        case 'bom':

            return '

                <span
                    class="
                        badge
                        bg-primary
                    "
                >

                    <i
                        class="
                            fas
                            fa-thumbs-up
                        "
                    ></i>

                    Bom

                </span>

            ';


        case 'satisfatorio':

            return '

                <span
                    class="
                        badge
                        bg-warning
                        text-dark
                    "
                >

                    Satisfatório

                </span>

            ';


        case 'ruim':

            return '

                <span
                    class="
                        badge
                        bg-danger
                    "
                >

                    <i
                        class="
                            fas
                            fa-exclamation-triangle
                        "
                    ></i>

                    Ruim

                </span>

            ';


        default:

            return '-';


    }

}
/*
|--------------------------------------------------------------------------
| GERA URL DA PAGINAÇÃO
|--------------------------------------------------------------------------
*/
function urlPagina(
    int $pagina
): string {


    $parametros = [];


    if (
        isset(
            $_GET[
                'data_inicio'
            ]
        ) &&
        $_GET[
            'data_inicio'
        ] !== ''
    ) {

        $parametros[
            'data_inicio'
        ] =
            $_GET[
                'data_inicio'
            ];

    }


    if (
        isset(
            $_GET[
                'data_fim'
            ]
        ) &&
        $_GET[
            'data_fim'
        ] !== ''
    ) {

        $parametros[
            'data_fim'
        ] =
            $_GET[
                'data_fim'
            ];

    }


    if (
        isset(
            $_GET[
                'apto'
            ]
        ) &&
        $_GET[
            'apto'
        ] !== ''
    ) {

        $parametros[
            'apto'
        ] =
            $_GET[
                'apto'
            ];

    }


    $parametros[
        'pagina'
    ] =
        $pagina;


    return
        'relatorioCafe.php?' .
        http_build_query(
            $parametros
        );

}

?>
<div class="container-fluid py-4">
<!-- =====================================================
     CABEÇALHO
====================================================== -->
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">
        <i class="fas fa-coffee text-primary"></i>
            Relatório de Avaliações do Café
      </h2>
      <p class="text-muted mb-0">Avaliações realizadas pelos hóspedes</p>
    </div>
  </div>
<!-- =====================================================
     FILTROS
====================================================== -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
      <i class="fas fa-filter"></i>
        Filtros
    </div>
  <div class="card-body">
    <form onsubmit="event.preventDefault();
      filtrarRelatorioCafe(this);">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label"> Data Inicial</label>
        <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>">
      </div>
    <div class="col-md-3">
      <label class="form-label">Data Final</label>
      <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim)?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Apartamento</label>
      <input type="text" name="apto" class="form-control" placeholder="Ex: 101" value="<?= htmlspecialchars($apto) ?>">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-primary me-2">
        <i class="fas fa-search"></i>
          Filtrar 
      </button>
      <button type="button" class="btn btn-outline-secondary" onclick="carregarPagina('relatorioCafe.php')" >

                        Limpar

                    </button>


                </div>


            </div>


        </form>


    </div>


</div>



<!-- =====================================================
     INDICADORES
====================================================== -->


<div
    class="
        row
        g-3
        mb-4
    "
>


    <div
        class="
            col-md-3
        "
    >


        <div
            class="
                card
                shadow-sm
                text-center
                h-100
            "
        >


            <div
                class="
                    card-body
                "
            >


                <i
                    class="
                        fas
                        fa-clipboard-list
                        fa-2x
                        text-primary
                    "
                ></i>


                <h6
                    class="
                        text-muted
                        mt-2
                    "
                >

                    Total de Avaliações

                </h6>


                <h2
                    class="
                        fw-bold
                    "
                >

                    <?= $totalRegistros ?>

                </h2>


            </div>


        </div>


    </div>



    <div
        class="
            col-md-3
        "
    >


        <div
            class="
                card
                shadow-sm
                text-center
                h-100
            "
        >


            <div
                class="
                    card-body
                "
            >


                <i
                    class="
                        fas
                        fa-star
                        fa-2x
                        text-success
                    "
                ></i>


                <h6
                    class="
                        text-muted
                        mt-2
                    "
                >

                    Ótimo - Café da Manhã

                </h6>


                <h2
                    class="
                        fw-bold
                        text-success
                    "
                >

                    <?= $totalOtimo ?>

                </h2>


            </div>


        </div>


    </div>



    <div
        class="
            col-md-3
        "
    >


        <div
            class="
                card
                shadow-sm
                text-center
                h-100
            "
        >


            <div
                class="
                    card-body
                "
            >


                <i
                    class="
                        fas
                        fa-thumbs-up
                        fa-2x
                        text-primary
                    "
                ></i>


                <h6
                    class="
                        text-muted
                        mt-2
                    "
                >

                    Bom - Café da Manhã

                </h6>


                <h2
                    class="
                        fw-bold
                        text-primary
                    "
                >

                    <?= $totalBom ?>

                </h2>


            </div>


        </div>


    </div>



    <div
        class="
            col-md-3
        "
    >


        <div
            class="
                card
                shadow-sm
                text-center
                h-100
            "
        >


            <div
                class="
                    card-body
                "
            >


                <i
                    class="
                        fas
                        fa-exclamation-triangle
                        fa-2x
                        text-danger
                    "
                ></i>


                <h6
                    class="
                        text-muted
                        mt-2
                    "
                >

                    Ruim - Café da Manhã

                </h6>


                <h2
                    class="
                        fw-bold
                        text-danger
                    "
                >

                    <?= $totalRuim ?>

                </h2>


            </div>


        </div>


    </div>


</div>



<!-- =====================================================
     RESUMO DOS CRITÉRIOS
====================================================== -->


<div
    class="
        card
        shadow-sm
        mb-4
    "
>


    <div
        class="
            card-header
            bg-dark
            text-white
        "
    >

        <i
            class="
                fas
                fa-chart-bar
            "
        ></i>

        Resumo das Avaliações

    </div>


    <div
        class="
            card-body
        "
    >


        <div
            class="
                table-responsive
            "
        >


            <table
                class="
                    table
                    table-hover
                    align-middle
                "
            >


                <thead>


                    <tr>


                        <th>
                            Item
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Ótimo
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Bom
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Satisfatório
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Ruim
                        </th>


                    </tr>


                </thead>


                <tbody>


                <?php foreach (
                    $estatisticas
                    as $estatistica
                ): ?>


                    <tr>


                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $estatistica[
                                        'nome'
                                    ]
                                ) ?>

                            </strong>

                        </td>


                        <td
                            class="
                                text-center
                            "
                        >

                            <span
                                class="
                                    badge
                                    bg-success
                                "
                            >

                                <?= $estatistica[
                                    'otimo'
                                ] ?>

                            </span>

                        </td>


                        <td
                            class="
                                text-center
                            "
                        >

                            <span
                                class="
                                    badge
                                    bg-primary
                                "
                            >

                                <?= $estatistica[
                                    'bom'
                                ] ?>

                            </span>

                        </td>


                        <td
                            class="
                                text-center
                            "
                        >

                            <span
                                class="
                                    badge
                                    bg-warning
                                    text-dark
                                "
                            >

                                <?= $estatistica[
                                    'satisfatorio'
                                ] ?>

                            </span>

                        </td>


                        <td
                            class="
                                text-center
                            "
                        >

                            <span
                                class="
                                    badge
                                    bg-danger
                                "
                            >

                                <?= $estatistica[
                                    'ruim'
                                ] ?>

                            </span>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    </div>


</div>



<!-- =====================================================
     TABELA PRINCIPAL
====================================================== -->


<div
    class="
        card
        shadow-sm
    "
>


    <div
        class="
            card-header
            bg-dark
            text-white
        "
    >


        <div
            class="
                d-flex
                justify-content-between
                align-items-center
            "
        >


            <span>

                <i
                    class="
                        fas
                        fa-users
                    "
                ></i>

                Avaliações dos Hóspedes

            </span>


            <span
                class="
                    badge
                    bg-light
                    text-dark
                "
            >

                <?= $totalRegistros ?>

                registro(s)

            </span>


        </div>


    </div>


    <div
        class="
            card-body
        "
    >


        <div
            class="
                table-responsive
            "
        >


            <table
                class="
                    table
                    table-hover
                    align-middle
                "
            >


                <thead>


                    <tr>


                        <th>
                            Data
                        </th>


                        <th>
                            Hóspede
                        </th>


                        <th>
                            Apto
                        </th>


                        <th>
                            Telefone
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Nota Geral
                        </th>


                        <th>
                            Sugestão
                        </th>


                        <th
                            class="
                                text-center
                            "
                        >
                            Ações
                        </th>


                    </tr>


                </thead>


                <tbody>


                <?php if (
                    $totalRegistros === 0
                ): ?>


                    <tr>


                        <td
                            colspan="7"
                            class="
                                text-center
                                text-muted
                                py-5
                            "
                        >


                            <i
                                class="
                                    fas
                                    fa-info-circle
                                    fa-2x
                                    mb-3
                                "
                            ></i>


                            <br>


                            Nenhuma avaliação encontrada.


                        </td>


                    </tr>


                <?php else: ?>


                <?php while (
                    $avaliacao =
                    $resultAvaliacoes
                        ->fetch_assoc()
                ): ?>


                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | VALORES PARA CÁLCULO DA NOTA
                    |--------------------------------------------------------------------------
                    */

                    $valoresNota = [

                        'otimo' =>
                            4,

                        'bom' =>
                            3,

                        'satisfatorio' =>
                            2,

                        'ruim' =>
                            1

                    ];


                    $somaNotas =
                        0;


                    $quantidadeNotas =
                        0;


                    foreach (
                        $camposAvaliacao
                        as $campo =>
                        $nome
                    ) {


                        $valor =
                            $avaliacao[
                                $campo
                            ] ?? null;


                        if (
                            isset(
                                $valoresNota[
                                    $valor
                                ]
                            )
                        ) {


                            $somaNotas +=
                                $valoresNota[
                                    $valor
                                ];


                            $quantidadeNotas++;


                        }


                    }


                    $notaGeral =
                        $quantidadeNotas > 0

                            ? $somaNotas /
                              $quantidadeNotas

                            : 0;


                    /*
                    |--------------------------------------------------------------------------
                    | COR DA NOTA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $notaGeral >= 3.5
                    ) {


                        $classeNota =
                            'bg-success';


                    } elseif (
                        $notaGeral >= 2.5
                    ) {


                        $classeNota =
                            'bg-primary';


                    } elseif (
                        $notaGeral >= 1.5
                    ) {


                        $classeNota =
                            'bg-warning text-dark';


                    } else {


                        $classeNota =
                            'bg-danger';


                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ID DO MODAL
                    |--------------------------------------------------------------------------
                    */

                    $modalId =
                        'modalAvaliacao_' .
                        (int)
                        $avaliacao[
                            'id'
                        ];

                    ?>


                    <!-- LINHA DA TABELA -->


                    <tr>


                        <!-- DATA -->


                        <td>


                            <?= date(
                                'd/m/Y',
                                strtotime(
                                    $avaliacao[
                                        'data_avaliacao'
                                    ]
                                )
                            ) ?>


                        </td>



                        <!-- HÓSPEDE -->


                        <td>


                            <strong>


                                <?= htmlspecialchars(
                                    $avaliacao[
                                        'hospede'
                                    ]
                                ) ?>


                            </strong>


                        </td>



                        <!-- APTO -->


                        <td>


                            <span
                                class="
                                    badge
                                    bg-secondary
                                "
                            >


                                <?= htmlspecialchars(
                                    $avaliacao[
                                        'apto'
                                    ]
                                ) ?>


                            </span>


                        </td>



                        <!-- TELEFONE -->


                        <td>


                            <?= htmlspecialchars(
                                $avaliacao[
                                    'fone'
                                ] ?? '-'
                            ) ?>


                        </td>



                        <!-- NOTA -->


                        <td
                            class="
                                text-center
                            "
                        >


                            <span
                                class="
                                    badge
                                    <?= $classeNota ?>
                                "
                                style="
                                    font-size:
                                    0.95rem;
                                "
                            >


                                <i
                                    class="
                                        fas
                                        fa-star
                                    "
                                ></i>


                                <?= number_format(
                                    $notaGeral,
                                    1,
                                    ',',
                                    '.'
                                ) ?>


                            </span>


                        </td>



                        <!-- SUGESTÃO -->


                        <td>


                        <?php if (
                            !empty(
                                $avaliacao[
                                    'sugestoes'
                                ]
                            )
                        ): ?>


                            <span
                                class="
                                    text-success
                                "
                            >


                                <i
                                    class="
                                        fas
                                        fa-comment-dots
                                    "
                                ></i>


                                Sim


                            </span>


                        <?php else: ?>


                            <span
                                class="
                                    text-muted
                                "
                            >

                                Não

                            </span>


                        <?php endif; ?>


                        </td>



                        <!-- AÇÃO -->


                        <td
                            class="
                                text-center
                            "
                        >


                            <button
                                type="button"
                                class="
                                    btn
                                    btn-sm
                                    btn-primary
                                "
                                data-bs-toggle="modal"
                                data-bs-target="#<?= $modalId ?>"
                            >


                                <i
                                    class="
                                        fas
                                        fa-eye
                                    "
                                ></i>


                                Ver avaliação


                            </button>


                        </td>


                    </tr>



                    <!-- =================================================
                         MODAL
                    ================================================== -->


                    <div
                        class="
                            modal
                            fade
                        "
                        id="<?= $modalId ?>"
                        tabindex="-1"
                        aria-hidden="true"
                    >


                        <div
                            class="
                                modal-dialog
                                modal-lg
                                modal-dialog-scrollable
                            "
                        >


                            <div
                                class="
                                    modal-content
                                "
                            >


                                <!-- CABEÇALHO -->


                                <div
                                    class="
                                        modal-header
                                        bg-primary
                                        text-white
                                    "
                                >


                                    <h5
                                        class="
                                            modal-title
                                        "
                                    >


                                        <i
                                            class="
                                                fas
                                                fa-clipboard-check
                                            "
                                        ></i>


                                        Avaliação do Hóspede


                                    </h5>


                                    <button
                                        type="button"
                                        class="
                                            btn-close
                                            btn-close-white
                                        "
                                        data-bs-dismiss="modal"
                                    ></button>


                                </div>



                                <!-- CORPO -->


                                <div
                                    class="
                                        modal-body
                                    "
                                >


                                    <!-- DADOS DO HÓSPEDE -->


                                    <div
                                        class="
                                            card
                                            bg-light
                                            mb-4
                                        "
                                    >


                                        <div
                                            class="
                                                card-body
                                            "
                                        >


                                            <div
                                                class="
                                                    row
                                                    g-3
                                                "
                                            >


                                                <div
                                                    class="
                                                        col-md-6
                                                    "
                                                >


                                                    <small
                                                        class="
                                                            text-muted
                                                        "
                                                    >

                                                        Hóspede

                                                    </small>


                                                    <div
                                                        class="
                                                            fw-bold
                                                        "
                                                    >

                                                        <?= htmlspecialchars(
                                                            $avaliacao[
                                                                'hospede'
                                                            ]
                                                        ) ?>


                                                    </div>


                                                </div>



                                                <div
                                                    class="
                                                        col-md-3
                                                    "
                                                >


                                                    <small
                                                        class="
                                                            text-muted
                                                        "
                                                    >

                                                        Apartamento

                                                    </small>


                                                    <div
                                                        class="
                                                            fw-bold
                                                        "
                                                    >

                                                        <?= htmlspecialchars(
                                                            $avaliacao[
                                                                'apto'
                                                            ]
                                                        ) ?>


                                                    </div>


                                                </div>



                                                <div
                                                    class="
                                                        col-md-3
                                                    "
                                                >


                                                    <small
                                                        class="
                                                            text-muted
                                                        "
                                                    >

                                                        Data

                                                    </small>


                                                    <div
                                                        class="
                                                            fw-bold
                                                        "
                                                    >

                                                        <?= date(
                                                            'd/m/Y',
                                                            strtotime(
                                                                $avaliacao[
                                                                    'data_avaliacao'
                                                                ]
                                                            )
                                                        ) ?>


                                                    </div>


                                                </div>



                                                <div
                                                    class="
                                                        col-md-6
                                                    "
                                                >


                                                    <small
                                                        class="
                                                            text-muted
                                                        "
                                                    >

                                                        Telefone

                                                    </small>


                                                    <div>


                                                        <?= htmlspecialchars(
                                                            $avaliacao[
                                                                'fone'
                                                            ] ?? '-'
                                                        ) ?>


                                                    </div>


                                                </div>



                                                <div
                                                    class="
                                                        col-md-6
                                                    "
                                                >


                                                    <small
                                                        class="
                                                            text-muted
                                                        "
                                                    >

                                                        E-mail

                                                    </small>


                                                    <div>


                                                        <?= htmlspecialchars(
                                                            $avaliacao[
                                                                'email'
                                                            ] ?? '-'
                                                        ) ?>


                                                    </div>


                                                </div>


                                            </div>


                                        </div>


                                    </div>



                                    <!-- ITENS AVALIADOS -->


                                    <h5
                                        class="
                                            mb-3
                                        "
                                    >


                                        <i
                                            class="
                                                fas
                                                fa-star
                                                text-warning
                                            "
                                        ></i>


                                        Itens Avaliados


                                    </h5>



                                    <div
                                        class="
                                            row
                                            g-3
                                        "
                                    >


                                    <?php foreach (
                                        $camposAvaliacao
                                        as $campo =>
                                        $nome
                                    ): ?>


                                        <div
                                            class="
                                                col-md-6
                                            "
                                        >


                                            <div
                                                class="
                                                    border
                                                    rounded
                                                    p-3
                                                    h-100
                                                "
                                            >


                                                <div
                                                    class="
                                                        d-flex
                                                        justify-content-between
                                                        align-items-center
                                                        gap-2
                                                    "
                                                >


                                                    <span
                                                        class="
                                                            fw-semibold
                                                        "
                                                    >


                                                        <?= htmlspecialchars(
                                                            $nome
                                                        ) ?>


                                                    </span>


                                                    <?= labelAvaliacao(
                                                        $avaliacao[
                                                            $campo
                                                        ]
                                                    ) ?>


                                                </div>


                                            </div>


                                        </div>


                                    <?php endforeach; ?>


                                    </div>



                                    <!-- NOTA GERAL -->


                                    <div
                                        class="
                                            alert
                                            alert-light
                                            border
                                            mt-4
                                            text-center
                                        "
                                    >


                                        <strong>


                                            Nota Geral


                                        </strong>


                                        <div
                                            class="
                                                mt-2
                                            "
                                        >


                                            <span
                                                class="
                                                    badge
                                                    <?= $classeNota ?>
                                                "
                                                style="
                                                    font-size:
                                                    1.1rem;
                                                "
                                            >


                                                <i
                                                    class="
                                                        fas
                                                        fa-star
                                                    "
                                                ></i>


                                                <?= number_format(
                                                    $notaGeral,
                                                    1,
                                                    ',',
                                                    '.'
                                                ) ?>


                                                / 4,0


                                            </span>


                                        </div>


                                    </div>



                                    <!-- SUGESTÕES -->


                                    <div
                                        class="
                                            mt-4
                                        "
                                    >


                                        <h5>


                                            <i
                                                class="
                                                    fas
                                                    fa-comment-dots
                                                    text-primary
                                                "
                                            ></i>


                                            Sugestões e Observações


                                        </h5>


                                        <div
                                            class="
                                                border
                                                rounded
                                                p-3
                                                bg-light
                                            "
                                        >


                                        <?php if (
                                            !empty(
                                                $avaliacao[
                                                    'sugestoes'
                                                ]
                                            )
                                        ): ?>


                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $avaliacao[
                                                        'sugestoes'
                                                    ]
                                                )
                                            ) ?>


                                        <?php else: ?>


                                            <span
                                                class="
                                                    text-muted
                                                "
                                            >


                                                Nenhuma sugestão
                                                informada.


                                            </span>


                                        <?php endif; ?>


                                        </div>


                                    </div>


                                </div>



                                <!-- RODAPÉ -->


                                <div
                                    class="
                                        modal-footer
                                    "
                                >


                                    <button
                                        type="button"
                                        class="
                                            btn
                                            btn-secondary
                                        "
                                        data-bs-dismiss="modal"
                                    >


                                        Fechar


                                    </button>


                                </div>


                            </div>


                        </div>


                    </div>


                <?php endwhile; ?>


                <?php endif; ?>


                </tbody>


            </table>


        </div>



        <!-- =================================================
             PAGINAÇÃO
        ================================================== -->


        <?php if (
            $totalPaginas > 1
        ): ?>


            <nav
                aria-label="
                    Paginação das avaliações
                "
            >


                <ul
                    class="
                        pagination
                        justify-content-center
                        mt-4
                    "
                >


                    <!-- PRIMEIRA -->


                    <li
                        class="
                            page-item
                            <?= $pagina <= 1
                                ? 'disabled'
                                : '' ?>
                        "
                    >


                        <a
                            class="
                                page-link
                            "
                            href="#"
                            onclick="
                                event.preventDefault();

                                <?php if (
                                    $pagina > 1
                                ): ?>

                                carregarPagina(
                                    '<?= urlPagina(
                                        1
                                    ) ?>'
                                );

                                <?php endif; ?>
                            "
                        >


                            Primeira


                        </a>


                    </li>



                    <!-- ANTERIOR -->


                    <li
                        class="
                            page-item
                            <?= $pagina <= 1
                                ? 'disabled'
                                : '' ?>
                        "
                    >


                        <a
                            class="
                                page-link
                            "
                            href="#"
                            onclick="
                                event.preventDefault();

                                <?php if (
                                    $pagina > 1
                                ): ?>

                                carregarPagina(
                                    '<?= urlPagina(
                                        $pagina - 1
                                    ) ?>'
                                );

                                <?php endif; ?>
                            "
                        >


                            <i
                                class="
                                    fas
                                    fa-chevron-left
                                "
                            ></i>


                        </a>


                    </li>



                    <?php

                    $inicio =
                        max(
                            1,
                            $pagina - 2
                        );


                    $fim =
                        min(
                            $totalPaginas,
                            $pagina + 2
                        );

                    ?>


                    <?php for (
                        $i = $inicio;
                        $i <= $fim;
                        $i++
                    ): ?>


                        <li
                            class="
                                page-item
                                <?= $i === $pagina
                                    ? 'active'
                                    : '' ?>
                            "
                        >


                            <a
                                class="
                                    page-link
                                "
                                href="#"
                                onclick="
                                    event.preventDefault();

                                    carregarPagina(
                                        '<?= urlPagina(
                                            $i
                                        ) ?>'
                                    );
                                "
                            >


                                <?= $i ?>


                            </a>


                        </li>


                    <?php endfor; ?>



                    <!-- PRÓXIMA -->


                    <li
                        class="
                            page-item
                            <?= $pagina >= $totalPaginas
                                ? 'disabled'
                                : '' ?>
                        "
                    >


                        <a
                            class="
                                page-link
                            "
                            href="#"
                            onclick="
                                event.preventDefault();

                                <?php if (
                                    $pagina <
                                    $totalPaginas
                                ): ?>

                                carregarPagina(
                                    '<?= urlPagina(
                                        $pagina + 1
                                    ) ?>'
                                );

                                <?php endif; ?>
                            "
                        >


                            <i
                                class="
                                    fas
                                    fa-chevron-right
                                "
                            ></i>


                        </a>


                    </li>



                    <!-- ÚLTIMA -->


                    <li
                        class="
                            page-item
                            <?= $pagina >= $totalPaginas
                                ? 'disabled'
                                : '' ?>
                        "
                    >


                        <a
                            class="
                                page-link
                            "
                            href="#"
                            onclick="
                                event.preventDefault();

                                <?php if (
                                    $pagina <
                                    $totalPaginas
                                ): ?>

                                carregarPagina(
                                    '<?= urlPagina(
                                        $totalPaginas
                                    ) ?>'
                                );

                                <?php endif; ?>
                            "
                        >


                            Última


                        </a>


                    </li>


                </ul>


            </nav>


        <?php endif; ?>



        <!-- INFORMAÇÃO DA PAGINAÇÃO -->


        <?php if (
            $totalRegistros > 0
        ): ?>


            <?php

            $registroInicial =
                $offset + 1;


            $registroFinal =
                min(
                    $offset + $limite,
                    $totalRegistros
                );

            ?>


            <p
                class="
                    text-center
                    text-muted
                    mb-0
                "
            >


                Exibindo


                <strong>

                    <?= $registroInicial ?>

                </strong>


                até


                <strong>

                    <?= $registroFinal ?>

                </strong>


                de


                <strong>

                    <?= $totalRegistros ?>

                </strong>


                avaliações


            </p>


        <?php endif; ?>


    </div>


</div>
</div>

<!-- =====================================================
     CSS
====================================================== -->

<style>

    #conteudo-organizacional
    .card {

        border-radius:
            12px;

        border:
            none;

    }


    #conteudo-organizacional
    .card-header {

        border-radius:
            12px
            12px
            0
            0;

    }


    #conteudo-organizacional
    .table {

        vertical-align:
            middle;

    }


    #conteudo-organizacional
    .table th {

        white-space:
            nowrap;

    }


    #conteudo-organizacional
    .badge {

        font-size:
            0.85rem;

    }


    #conteudo-organizacional
    .modal {

        z-index:
            1060;

    }


    #conteudo-organizacional
    .modal-backdrop {

        z-index:
            1050;

    }


    #conteudo-organizacional
    .modal-content {

        border:
            none;

        border-radius:
            15px;

        overflow:
            hidden;

    }


    #conteudo-organizacional
    .modal-header {

        border:
            none;

    }


    #conteudo-organizacional
    .modal-body
    .border {

        transition:
            all
            0.2s
            ease;

    }


    #conteudo-organizacional
    .modal-body
    .border:hover {

        box-shadow:
            0
            3px
            10px
            rgba(
                0,
                0,
                0,
                0.08
            );

        transform:
            translateY(
                -2px
            );

    }


    @media print {


        nav,
        footer,
        .no-print,
        .btn,
        form {

            display:
                none !important;

        }


        body {

            background:
                white;

        }


        .card {

            box-shadow:
                none !important;

        }


    }
</style>

<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script>


/*
|--------------------------------------------------------------------------
| FILTRAR RELATÓRIO
|--------------------------------------------------------------------------
*/

function filtrarRelatorioCafe(
    form
)
{


    const params =
        new URLSearchParams(
            new FormData(
                form
            )
        );


    /*
    |--------------------------------------------------------------------------
    | SEMPRE COMEÇA NA PRIMEIRA PÁGINA
    |--------------------------------------------------------------------------
    */

    params.set(
        'pagina',
        '1'
    );


    const url =
        'relatorioCafe.php?' +
        params.toString();


    carregarPagina(
        url
    );


}


/*
|--------------------------------------------------------------------------
| FECHAR MODAL AO RECARREGAR PÁGINA
|--------------------------------------------------------------------------
|
| Como o conteúdo é carregado via AJAX,
| removemos possíveis backdrops antigos.
|
*/

document
    .querySelectorAll(
        '.modal-backdrop'
    )
    .forEach(
        elemento => elemento.remove()
    );


</script>
</div>