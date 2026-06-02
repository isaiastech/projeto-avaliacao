<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\Database;

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Requisição inválida.');
    }

    $avaliadorId = (int) $_SESSION['usuario_id'];
    $avaliadoId  = (int) ($_POST['avaliado_id'] ?? 0);
    $notas       = $_POST['nota'] ?? [];

    // Não pode avaliar a si mesmo
    if ($avaliadorId === $avaliadoId) {
        throw new Exception(
            'Você não pode avaliar a si mesmo.'
        );
    }

    if (empty($notas)) {
        throw new Exception(
            'Nenhuma nota foi informada.'
        );
    }

    $db = new Database();

    $mes = date('m');
    $ano = date('Y');

    // =====================================================
    // VERIFICA TOTAL DE QUESTÕES
    // =====================================================

    $sql = "
        SELECT COUNT(*) AS total
        FROM questoes
    ";

    $totalQuestoes = $db->getResultFromQuery($sql)
        ->fetch_assoc()['total'];

    if (count($notas) != $totalQuestoes) {

        throw new Exception(
            'Todas as questões devem ser respondidas.'
        );

    }

    // =====================================================
    // VERIFICA SE AS QUESTÕES EXISTEM
    // =====================================================

    $sql = "
        SELECT id
        FROM questoes
    ";

    $resultQuestoes = $db->getResultFromQuery($sql);

    $idsValidos = [];

    while ($q = $resultQuestoes->fetch_assoc()) {

        $idsValidos[] = (int) $q['id'];

    }

    foreach ($notas as $questaoId => $nota) {

        if (!in_array((int)$questaoId, $idsValidos)) {

            throw new Exception(
                'Questão inválida detectada.'
            );

        }

    }

    // =====================================================
    // VALIDA NOTAS
    // =====================================================

    foreach ($notas as $nota) {

        $nota = (int) $nota;

        if ($nota < 1 || $nota > 5) {

            throw new Exception(
                'Foi encontrada uma nota inválida.'
            );

        }

    }

    // =====================================================
    // BUSCA DADOS DO AVALIADO
    // =====================================================

    $sql = "
        SELECT
            id,
            nome,
            nivel,
            ativo
        FROM usuarios
        WHERE id = ?
        LIMIT 1
    ";

    $result = $db->getResultFromQuery(
        $sql,
        [$avaliadoId]
    );

    if ($result->num_rows === 0) {

        throw new Exception(
            'Usuário não encontrado.'
        );

    }

    $usuarioAvaliado = $result->fetch_assoc();

    // =====================================================
    // REGRAS DE PERMISSÃO
    // =====================================================

    // Avaliador só pode avaliar usuários comuns

   if (
    $_SESSION['nivel'] === 'avaliador'
    && !in_array(
        $usuarioAvaliado['nivel'],
        ['user', 'avaliador']
    )
) {

    throw new Exception(
        'Você não possui permissão para avaliar este colaborador.'
    );

}
    // Não permite avaliar usuários inativos

    if ((int)$usuarioAvaliado['ativo'] !== 1) {

        throw new Exception(
            'Este colaborador está inativo.'
        );

    }

    // =====================================================
    // VERIFICA DUPLICIDADE
    // =====================================================

    $sql = "
        SELECT id
        FROM avaliacoes
        WHERE avaliador_id = ?
        AND avaliado_id = ?
        AND mes = ?
        AND ano = ?
        LIMIT 1
    ";

    $existe = $db->getResultFromQuery(
        $sql,
        [
            $avaliadorId,
            $avaliadoId,
            $mes,
            $ano
        ]
    );

    if ($existe->num_rows > 0) {

        throw new Exception(
            'Você já avaliou este colaborador neste mês.'
        );

    }

    // =====================================================
    // SALVA AVALIAÇÃO
    // =====================================================

    $sql = "
        INSERT INTO avaliacoes
        (
            avaliador_id,
            avaliado_id,
            mes,
            ano
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
    ";

    $db->execute(
        $sql,
        [
            $avaliadorId,
            $avaliadoId,
            $mes,
            $ano
        ]
    );

    $avaliacaoId = $db->getLastInsertId();

    // =====================================================
    // SALVA NOTAS
    // =====================================================

    foreach ($notas as $questaoId => $nota) {

        $sql = "
            INSERT INTO avaliacao_notas
            (
                avaliacao_id,
                questao_id,
                nota
            )
            VALUES
            (
                ?,
                ?,
                ?
            )
        ";

        $db->execute(
            $sql,
            [
                $avaliacaoId,
                (int) $questaoId,
                (int) $nota
            ]
        );

    }

    $_SESSION['sucesso'] =
        'Avaliação enviada com sucesso.';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();

}

header('Location: /views/dashboard.php');
exit;