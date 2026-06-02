<?php

namespace class\data;

use Exception;

class Questoes
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Retorna todas as questões
     */
    public function listar(): array
    {
        $sql = "SELECT *
                FROM questoes
                ORDER BY id";

        $result = $this->db->getResultFromQuery($sql);

        $dados = [];

        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }

        return $dados;
    }

    /**
     * Busca uma questão pelo ID
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT *
                FROM questoes
                WHERE id = ?";

        $result = $this->db->getResultFromQuery($sql, [$id]);

        return $result->fetch_assoc() ?: null;
    }

    /**
     * Retorna apenas o nome da questão
     */
    public function getNome(int $id): ?string
    {
        $questao = $this->buscarPorId($id);

        return $questao['questoes'] ?? null;
    }
}