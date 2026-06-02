<?php

namespace class\data;

use Exception;

class Usuario
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function listar(): array
    {
        $sql = "SELECT
                    id,
                    nome,
                    email,
                    nivel,
                    ativo,
                    data_cadastro
                FROM usuarios
                ORDER BY nome";

        $result = $this->db->getResultFromQuery($sql);

        $usuarios = [];

        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";

        $result = $this->db->getResultFromQuery($sql, [$id]);

        return $result->fetch_assoc() ?: null;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE email = ?";

        $result = $this->db->getResultFromQuery($sql, [$email]);

        return $result->fetch_assoc() ?: null;
    }

    public function cadastrar(
        string $nome,
        string $email,
        string $senha,
        string $nivel = 'user'
    ): bool {

        if ($this->buscarPorEmail($email)) {
            throw new Exception('E-mail já cadastrado.');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (
                    nome,
                    email,
                    senha,
                    nivel,
                    ativo
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    1
                )";

        return $this->db->execute(
            $sql,
            [
                $nome,
                $email,
                $senhaHash,
                $nivel
            ]
        );
    }

    public function editar(
        int $id,
        string $nome,
        string $email,
        string $nivel,
        int $ativo
    ): bool {

        $sql = "UPDATE usuarios
                SET
                    nome = ?,
                    email = ?,
                    nivel = ?,
                    ativo = ?
                WHERE id = ?";

        return $this->db->execute(
            $sql,
            [
                $nome,
                $email,
                $nivel,
                $ativo,
                $id
            ]
        );
    }

    public function alterarSenha(
        int $id,
        string $senha
    ): bool {

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios
                SET senha = ?
                WHERE id = ?";

        return $this->db->execute(
            $sql,
            [
                $senhaHash,
                $id
            ]
        );
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM usuarios
                WHERE id = ?";

        return $this->db->execute(
            $sql,
            [$id]
        );
    }

    public function login(
        string $email,
        string $senha
    ): ?array {

        $usuario = $this->buscarPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if ((int)$usuario['ativo'] !== 1) {
            return null;
        }

        if (!password_verify($senha, $usuario['senha'])) {
            return null;
        }

        return $usuario;
    }
    public function gerarTokenRecuperacao(string $email): ?string
{
    $usuario = $this->buscarPorEmail($email);

    if (!$usuario) {
        return null;
    }

    $token = bin2hex(random_bytes(32));
    $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $sql = "INSERT INTO recuperacao_senha
            (
                usuario_id,
                token,
                expiracao
            )
            VALUES
            (
                ?,
                ?,
                ?
            )";

    $this->db->execute(
        $sql,
        [
            $usuario['id'],
            $token,
            $expiracao
        ]
    );

    return $token;
}

public function validarToken(string $token): ?array
{
    $sql = "SELECT *
            FROM recuperacao_senha
            WHERE token = ?
            AND utilizado = 0
            AND expiracao > NOW()";

    $result = $this->db->getResultFromQuery(
        $sql,
        [$token]
    );

    return $result->fetch_assoc() ?: null;
}
public function redefinirSenha(
    string $token,
    string $novaSenha
): bool {

    $dados = $this->validarToken($token);

    if (!$dados) {
        throw new Exception('Token inválido ou expirado.');
    }

    $senhaHash = password_hash(
        $novaSenha,
        PASSWORD_DEFAULT
    );

    $this->db->execute(
        "UPDATE usuarios
         SET senha = ?
         WHERE id = ?",
        [
            $senhaHash,
            $dados['usuario_id']
        ]
    );

    $this->db->execute(
        "UPDATE recuperacao_senha
         SET utilizado = 1
         WHERE id = ?",
        [
            $dados['id']
        ]
    );

    return true;
}

}