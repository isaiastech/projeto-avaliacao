<?php

namespace class\data;

use Exception;

class Avaliacao
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Verifica se já avaliou no mês
     */
    public function jaAvaliou(
        int $avaliadorId,
        int $avaliadoId,
        int $ano,
        int $mes
    ): bool {

        $sql = "
            SELECT id
            FROM avaliacoes
            WHERE avaliador_id = ?
            AND avaliado_id = ?
            AND ano = ?
            AND mes = ?
        ";

        $result = $this->db->getResultFromQuery(
            $sql,
            [
                $avaliadorId,
                $avaliadoId,
                $ano,
                $mes
            ]
        );

        return $result->num_rows > 0;
    }

    /**
     * Salvar avaliação
     */
    public function salvar(
        int $avaliadorId,
        int $avaliadoId,
        array $respostas
    ): bool {

        if ($avaliadorId === $avaliadoId) {
            throw new Exception(
                'Você não pode avaliar a si mesmo.'
            );
        }

        $ano = date('Y');
        $mes = date('m');

        if (
            $this->jaAvaliou(
                $avaliadorId,
                $avaliadoId,
                $ano,
                $mes
            )
        ) {
            throw new Exception(
                'Você já avaliou este colaborador neste mês.'
            );
        }

        $sql = "
            INSERT INTO avaliacoes
            (
                avaliador_id,
                avaliado_id,
                ano,
                mes
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ";

        $this->db->execute(
            $sql,
            [
                $avaliadorId,
                $avaliadoId,
                $ano,
                $mes
            ]
        );

        $avaliacaoId = $this->db->getLastInsertId();

        foreach ($respostas as $questaoId => $nota) {

            $sql = "
                INSERT INTO avaliacao_respostas
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

            $this->db->execute(
                $sql,
                [
                    $avaliacaoId,
                    $questaoId,
                    $nota
                ]
            );
        }

        return true;
    }

    /**
     * Média geral do colaborador
     */
    public function mediaUsuario(
        int $usuarioId,
        int $ano,
        int $mes
    ): float {

        $sql = "
            SELECT AVG(ar.nota) media
            FROM avaliacao_respostas ar
            INNER JOIN avaliacoes a
                ON a.id = ar.avaliacao_id
            WHERE a.avaliado_id = ?
            AND a.ano = ?
            AND a.mes = ?
        ";

        $result = $this->db->getResultFromQuery(
            $sql,
            [
                $usuarioId,
                $ano,
                $mes
            ]
        );

        $dados = $result->fetch_assoc();

        return (float)($dados['media'] ?? 0);
    }

    /**
     * Ranking mensal
     */
    public function rankingMensal(
        int $ano,
        int $mes
    ): array {

        $sql = "
            SELECT
                u.id,
                u.nome,
                ROUND(AVG(ar.nota),2) media
            FROM usuarios u
            INNER JOIN avaliacoes a
                ON a.avaliado_id = u.id
            INNER JOIN avaliacao_respostas ar
                ON ar.avaliacao_id = a.id
            WHERE a.ano = ?
            AND a.mes = ?
            GROUP BY u.id
            ORDER BY media DESC
        ";

        $result = $this->db->getResultFromQuery(
            $sql,
            [
                $ano,
                $mes
            ]
        );

        $ranking = [];

        while ($row = $result->fetch_assoc()) {
            $ranking[] = $row;
        }

        return $ranking;
    }
}