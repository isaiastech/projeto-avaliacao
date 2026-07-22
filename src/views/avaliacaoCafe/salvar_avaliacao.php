<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use class\data\Database;

date_default_timezone_set('America/Sao_Paulo');


// ============================================================
// VERIFICA SE O FORMULÁRIO FOI ENVIADO VIA POST
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


// ============================================================
// DADOS DO HÓSPEDE
// ============================================================

$hospede = trim($_POST['hospede'] ?? '');

// Normaliza espaços
$hospede = preg_replace('/\s+/', ' ', $hospede);

// Primeira letra de cada nome em maiúscula
$hospede = mb_convert_case(
    mb_strtolower($hospede, 'UTF-8'),
    MB_CASE_TITLE,
    'UTF-8'
);

$apto = trim($_POST['apto'] ?? '');

// Remove qualquer caractere que não seja número
$apto = preg_replace('/\D/', '', $apto);

if (!preg_match('/^[0-9]+$/', $apto)) {
    die('O número do apartamento deve conter apenas números.');
}

$fone = trim($_POST['fone'] ?? '');

// Mantém apenas números
$fone_numeros = preg_replace('/\D/', '', $fone);

// Valida telefone brasileiro com 10 ou 11 dígitos
if (
    !empty($fone_numeros) &&
    !preg_match('/^[0-9]{10,11}$/', $fone_numeros)
) {
    die('O número de telefone informado é inválido.');
}

// Reaplica a máscara antes de salvar
if (strlen($fone_numeros) === 11) {

    $fone = sprintf(
        '(%s) %s-%s',
        substr($fone_numeros, 0, 2),
        substr($fone_numeros, 2, 5),
        substr($fone_numeros, 7, 4)
    );

} elseif (strlen($fone_numeros) === 10) {

    $fone = sprintf(
        '(%s) %s-%s',
        substr($fone_numeros, 0, 2),
        substr($fone_numeros, 2, 4),
        substr($fone_numeros, 6, 4)
    );

}

$email = trim($_POST['email'] ?? '');

$data_avaliacao = trim($_POST['data_avaliacao'] ?? '');

$sugestoes = trim($_POST['sugestoes'] ?? '');


// ============================================================
// ITENS DA AVALIAÇÃO
// ============================================================

$itens = [

    'cafe_manha',
    'colchao',
    'travesseiro',
    'limpeza',
    'frigobar',
    'chuveiro_aquecimento',
    'chuveiro_ducha',
    'ar_condicionado',
    'roupa_cama',
    'internet',
    'bar',
    'atendimento',
    'reserva',
    'recepcao',
    'camareira',
    'garcom'

];


// ============================================================
// OPÇÕES PERMITIDAS
// ============================================================

$opcoes_permitidas = [

    'otimo',
    'bom',
    'satisfatorio',
    'ruim'

];


// ============================================================
// VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS
// ============================================================

if (
    empty($hospede) ||
    empty($apto) ||
    empty($data_avaliacao)
) {

    die('Preencha todos os campos obrigatórios.');

}


// ============================================================
// VALIDAÇÃO DO E-MAIL
// ============================================================

if (
    !empty($email) &&
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    die('O endereço de e-mail informado é inválido.');

}


// ============================================================
// VALIDAÇÃO DA DATA
// ============================================================

$data = DateTime::createFromFormat(
    'Y-m-d',
    $data_avaliacao
);

if (
    !$data ||
    $data->format('Y-m-d') !== $data_avaliacao
) {

    die('A data informada é inválida.');

}


// ============================================================
// RECEBE E VALIDA AS AVALIAÇÕES
// ============================================================

$avaliacoes = [];

foreach ($itens as $item) {

    $valor = trim($_POST[$item] ?? '');


    if (
        !in_array(
            $valor,
            $opcoes_permitidas,
            true
        )
    ) {

        die(
            'É necessário informar uma avaliação válida para todos os itens.'
        );

    }


    $avaliacoes[$item] = $valor;

}


// ============================================================
// CONEXÃO COM O BANCO
// ============================================================

try {

    $db = new Database();

    $mysqli = $db->getConnection();


    // ========================================================
    // SQL
    // ========================================================

    $sql = "

        INSERT INTO avaliacoes_hospedes (

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

            sugestoes

        )

        VALUES (

            ?,
            ?,
            ?,
            ?,
            ?,

            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,

            ?

        )

    ";


    // ========================================================
    // PREPARED STATEMENT
    // ========================================================

    $stmt = $mysqli->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            'Erro ao preparar a consulta: ' .
            $mysqli->error
        );

    }


    // ========================================================
    // PARÂMETROS
    // ========================================================

    $stmt->bind_param(
    'ssssssssssssssssssssss',

        $hospede,

        $apto,

        $fone,

        $email,

        $data_avaliacao,

        $avaliacoes['cafe_manha'],

        $avaliacoes['colchao'],

        $avaliacoes['travesseiro'],

        $avaliacoes['limpeza'],

        $avaliacoes['frigobar'],

        $avaliacoes['chuveiro_aquecimento'],

        $avaliacoes['chuveiro_ducha'],

        $avaliacoes['ar_condicionado'],

        $avaliacoes['roupa_cama'],

        $avaliacoes['internet'],

        $avaliacoes['bar'],

        $avaliacoes['atendimento'],

        $avaliacoes['reserva'],

        $avaliacoes['recepcao'],

        $avaliacoes['camareira'],

        $avaliacoes['garcom'],

        $sugestoes

    );


    // ========================================================
    // EXECUTA
    // ========================================================

    if (!$stmt->execute()) {

        throw new Exception(
            'Erro ao salvar avaliação: ' .
            $stmt->error
        );

    }


    // ========================================================
    // FECHA
    // ========================================================

    $stmt->close();

    $mysqli->close();


    // ========================================================
    // REDIRECIONA
    // ========================================================

   session_start();

$_SESSION['mensagem_sucesso'] = 'Avaliação enviada com sucesso! Obrigado pela sua participação.';

header('Location: index.php');

exit;


} catch (Exception $e) {

    echo '

        <!DOCTYPE html>

        <html lang="pt-BR">

        <head>

            <meta charset="UTF-8">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1"
            >

            <title>Erro</title>

            <link
                rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
            >

        </head>

        <body>

            <div class="container mt-5">

                <div class="alert alert-danger">

                    <h4 class="alert-heading">

                        Erro ao salvar avaliação

                    </h4>

                    <p>

                        Não foi possível salvar a avaliação.

                    </p>

                    <hr>

                    <p class="mb-0">

                        ' . htmlspecialchars(
                            $e->getMessage()
                        ) . '

                    </p>

                </div>

                <a
                    href="avaliacao.php"
                    class="btn btn-primary"
                >

                    Voltar

                </a>

            </div>

        </body>

        </html>

    ';

}

