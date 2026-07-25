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
| VALIDAÇÃO DO FORMATO DA DATA
|--------------------------------------------------------------------------
*/

$dataValida = DateTime::createFromFormat(
    'Y-m-d',
    $data_avaliacao
);


if (
    !$dataValida ||
    $dataValida->format('Y-m-d') !== $data_avaliacao
) {

    $_SESSION['mensagem_erro'] =
        'A data da avaliação é inválida.';

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
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
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


    if (
        !in_array(
            $valor,
            $opcoesValidas,
            true
        )
    ) {

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
    | A tabela avaliacoes_hospedes possui:
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
    | A coluna id é AUTO_INCREMENT.
    | A coluna criado_em possui DEFAULT CURRENT_TIMESTAMP.
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


    /*
    |--------------------------------------------------------------------------
    | PARÂMETROS
    |--------------------------------------------------------------------------
    |
    | Exatamente 22 parâmetros para os 22 "?"
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

    $resultado = $database->execute(

        $sql,

        $params

    );


    /*
    |--------------------------------------------------------------------------
    | VERIFICAÇÃO
    |--------------------------------------------------------------------------
    */

    if (!$resultado) {

        throw new Exception(
            'Não foi possível inserir a avaliação no banco de dados.'
        );

    }


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
    | MENSAGEM DE SUCESSO
    |--------------------------------------------------------------------------
    */

    $_SESSION['mensagem_sucesso'] =

        'Obrigado! Sua avaliação foi registrada com sucesso.';


    /*
    |--------------------------------------------------------------------------
    | REDIRECIONA PARA AGRADECIMENTO
    |--------------------------------------------------------------------------
    */

    header(
        'Location: agradecimento.php'
    );

    exit;


} catch (Throwable $e) {


    /*
    |--------------------------------------------------------------------------
    | REGISTRA ERRO NO LOG
    |--------------------------------------------------------------------------
    |
    | O hóspede não verá detalhes internos do banco.
    |
    */

    error_log(

        'Erro ao salvar avaliação do hóspede: ' .

        $e->getMessage()

    );


    /*
    |--------------------------------------------------------------------------
    | MENSAGEM PARA O USUÁRIO
    |--------------------------------------------------------------------------
    */

    $_SESSION['mensagem_erro'] =

        'Não foi possível registrar sua avaliação. Tente novamente.';


    /*
    |--------------------------------------------------------------------------
    | REDIRECIONA PARA O FORMULÁRIO
    |--------------------------------------------------------------------------
    */

    header(
        'Location: index.php'
    );

    exit;

}