<?php

require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use class\data\PesquisaClima;

$pesquisa = new PesquisaClima();

$erros = $pesquisa->validar($_POST);

if (!empty($erros)) {

    $_SESSION['erro'] = implode('<br>', $erros);

    header('Location: index.php');
    exit;
}

$usuarioId = $_SESSION['usuario']['id'] ?? null;

if ($usuarioId && $pesquisa->jaRespondeu($usuarioId)) {

    $_SESSION['erro'] = 'Você já respondeu esta pesquisa.';

    header('Location: index.php');
    exit;
}

$pesquisa->salvar($_POST, $usuarioId);

$_SESSION['sucesso'] = 'Pesquisa enviada com sucesso!';

header('Location: index.php');
exit;
