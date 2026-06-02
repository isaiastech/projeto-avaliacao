<?php

namespace class\data;

use class\mail\Mail;

class RecuperacaoSenha
{
    private Database $db;
    private Usuario $usuario;

    public function __construct()
    {
        $this->db = new Database();
        $this->usuario = new Usuario();
    }

    public function enviarLink(string $email): bool
    {
        $usuario = $this->usuario->buscarPorEmail($email);

        if (!$usuario) {
            return false;
        }

        $token = bin2hex(random_bytes(32));

        $expiracao = date(
            'Y-m-d H:i:s',
            strtotime('+1 hour')
        );

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

        $link = sprintf(
            'http://localhost:8081/views/redefinir_senha.php?token=%s',
            $token
        );

        $html = "
            <h2>Recuperação de Senha</h2>

            <p>Olá {$usuario['nome']},</p>

            <p>Clique no link abaixo para redefinir sua senha:</p>

            <p>
                <a href='{$link}'>
                    Redefinir Senha
                </a>
            </p>

            <p>O link expira em 1 hora.</p>
        ";

        $mail = new Mail();

        return $mail->enviar(
            $usuario['email'],
            $usuario['nome'],
            'Recuperação de Senha',
            $html
        );
    }
}