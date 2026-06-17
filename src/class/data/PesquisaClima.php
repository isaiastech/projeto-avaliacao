<?php

namespace class\data;

class PesquisaClima
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Valida respostas
     */
 public function validar(array $dados): array
{
    $erros = [];

    if (empty($dados['setor'])) {
        $erros[] = 'Selecione um setor.';
    }

    if (!isset($dados['respostas'])) {
        $erros[] = 'Nenhuma resposta enviada.';
    }

    return $erros;
}
    /**
     * Salva respostas
     */
 public function salvar(array $dados, ?int $usuarioId = null): bool
{
    $sql = "
        INSERT INTO respostas_clima
        (
            usuario_id,
            setor,
            pergunta_id,
            resposta
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ";

    foreach ($dados['respostas'] as $perguntaId => $resposta) {

        $resposta = trim($resposta);

        if ($resposta === '') {
            $resposta = null;
        }

        $this->db->execute(
            $sql,
            [
                $usuarioId,
                $dados['setor'],
                (int)$perguntaId,
                $resposta
            ]
        );
    }

    return true;
}
    /**
     * Verifica se o usuário já respondeu
     */
public function jaRespondeu(int $usuarioId): bool
{
    $sql = "
        SELECT id
        FROM respostas_clima
        WHERE usuario_id = ?
        LIMIT 1
    ";

    $result = $this->db->getResultFromQuery(
        $sql,
        [$usuarioId]
    );

    return $result->num_rows > 0;
}
}