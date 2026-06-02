<?php

namespace class\data;

use mysqli;
use Exception;

class Database
{
    private mysqli $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $envPath = '/var/www/config/.env';

        if (!file_exists($envPath)) {
            throw new Exception(
                "Erro: O arquivo de configuração '.env' não foi encontrado em {$envPath}."
            );
        }

        $env = parse_ini_file($envPath);

        if (!$env) {
            throw new Exception(
                "Erro: Não foi possível carregar o arquivo de configuração '.env'."
            );
        }

        $this->conn = new mysqli(
            $env['host'],
            $env['username'],
            $env['password'],
            $env['database']
        );

        if ($this->conn->connect_error) {
            throw new Exception(
                "Erro de conexão: " . $this->conn->connect_error
            );
        }

        $this->conn->set_charset('utf8mb4');
    }

    private function getParamTypes(array $params): string
    {
        $types = '';

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }

        return $types;
    }

    /**
     * SELECT
     */
    public function getResultFromQuery(string $sql, array $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Erro ao preparar a consulta: " . $this->conn->error
            );
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao executar a consulta: " . $stmt->error
            );
        }

        return $stmt->get_result();
    }

    /**
     * INSERT / UPDATE / DELETE
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Erro ao preparar a consulta: " . $this->conn->error
            );
        }

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao executar a consulta: " . $stmt->error
            );
        }

        return true;
    }

    /**
     * Retorna o último ID inserido
     */
    public function getLastInsertId(): int
    {
        return $this->conn->insert_id;
    }

    /**
     * Retorna conexão mysqli
     */
    public function getConnection(): mysqli
    {
        return $this->conn;
    }

    public function close(): void
    {
        if (isset($this->conn)) {
            $this->conn->close();
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}