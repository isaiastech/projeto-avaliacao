<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use class\data\Database;


/*
|--------------------------------------------------------------------------
| SOMENTE POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
*/

if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {

    $_SESSION['mensagem_erro'] =
        'Sessão inválida. Atualize a página e tente novamente.';

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| DADOS DO HÓSPEDE
|--------------------------------------------------------------------------
*/

$hospede = trim(
    $_POST['hospede'] ?? ''
);

$apto = preg_replace(
    '/[^0-9]/',
    '',
    $_POST['apto'] ?? ''
);

$fone = trim(
    $_POST['fone'] ?? ''
);

$email = trim(
    $_POST['email'] ?? ''
);

$data_avaliacao = trim(
    $_POST['data_avaliacao'] ?? ''
);

$sugestoes = trim(
    $_POST['sugestoes'] ?? ''
);


/*
|--------------------------------------------------------------------------
| ITENS DA AVALIAÇÃO
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| OPÇÕES VÁLIDAS
|--------------------------------------------------------------------------
*/

$opcoesValidas = [
    'otimo',
    'bom',
    'satisfatorio',
    'ruim'
];


/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DO NOME
|--------------------------------------------------------------------------
*/

if ($hospede === '') {

    $_SESSION['mensagem_erro'] =
        'Informe seu nome completo.';

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DO APARTAMENTO
|--------------------------------------------------------------------------
*/

if ($apto === '') {

    $_SESSION['mensagem_erro'] =
        'Informe o número do apartamento.';

    header('Location: index.php');
    exit;

}

if (!preg_match('/^[0-9]+$/', $apto)) {

    $_SESSION['mensagem_erro'] =
        'O apartamento deve conter apenas números.';

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DA DATA
|--------------------------------------------------------------------------
*/

if ($data_avaliacao === '') {

    $_SESSION['mensagem_erro'] =
        'Informe a data da avaliação.';

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DO E-MAIL
|--------------------------------------------------------------------------
*/

if (
    !empty($email) &&
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    $_SESSION['mensagem_erro'] =
        'Informe um e-mail válido.';

    header('Location: index.php');
    exit;

}


/*
|--------------------------------------------------------------------------
| VALIDAÇÃO DAS AVALIAÇÕES
|--------------------------------------------------------------------------
*/

$avaliacoes = [];

foreach ($itens as $item) {

    $valor = $_POST[$item] ?? '';

    if (!in_array($valor, $opcoesValidas, true)) {

        $_SESSION['mensagem_erro'] =
            'Por favor, avalie todos os itens.';

        header('Location: index.php');
        exit;

    }

    $avaliacoes[$item] = $valor;

}


/*
|--------------------------------------------------------------------------
| SALVAR NO BANCO
|--------------------------------------------------------------------------
*/

try {

    $database = new Database();


    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    |
    | 22 COLUNAS
    |
    | 1  hospede
    | 2  apto
    | 3  fone
    | 4  email
    | 5  data_avaliacao
    | 6  cafe_manha
    | 7  colchao
    | 8  travesseiro
    | 9  limpeza
    | 10 frigobar
    | 11 chuveiro_aquecimento
    | 12 chuveiro_ducha
    | 13 ar_condicionado
    | 14 roupa_cama
    | 15 internet
    | 16 bar
    | 17 atendimento
    | 18 reserva
    | 19 recepcao
    | 20 camareira
    | 21 garcom
    | 22 sugestoes
    |
    |--------------------------------------------------------------------------
    */

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
        ) VALUES (
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


    /*
    |--------------------------------------------------------------------------
    | PARÂMETROS
    |--------------------------------------------------------------------------
    |
    | EXATAMENTE 22 PARÂMETROS
    |
    */

    $params = [
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
    ];


    /*
    |--------------------------------------------------------------------------
    | EXECUTA INSERT
    |--------------------------------------------------------------------------
    */

    $database->execute(
        $sql,
        $params
    );


    /*
    |--------------------------------------------------------------------------
    | SUCESSO
    |--------------------------------------------------------------------------
    */

    $_SESSION['mensagem_sucesso'] =
        'Obrigado! Sua avaliação foi registrada com sucesso.';


    /*
    |--------------------------------------------------------------------------
    | NOVO TOKEN CSRF
    |--------------------------------------------------------------------------
    */

    $_SESSION['csrf_token'] =
        bin2hex(
            random_bytes(32)
        );


    /*
    |--------------------------------------------------------------------------
    | REDIRECIONA
    |--------------------------------------------------------------------------
    */

    header('Location: index.php');
    exit;


} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | LOG
    |--------------------------------------------------------------------------
    */

    error_log(
        'Erro ao salvar avaliação do hóspede: ' .
        $e->getMessage()
    );


    /*
    |--------------------------------------------------------------------------
    | MENSAGEM
    |--------------------------------------------------------------------------
    */

    $_SESSION['mensagem_erro'] =
        'Não foi possível registrar sua avaliação. Tente novamente.';


    header('Location: index.php');
    exit;

}