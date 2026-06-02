<?php

require_once __DIR__ . '/../vendor/autoload.php';

use class\data\Database;

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /index.php');
    exit;
}

try {

    $db = new Database();

    $id = $_SESSION['usuario_id'];

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    $usuario = $db->getResultFromQuery(
        "SELECT * FROM usuarios WHERE id = ?",
        [$id]
    )->fetch_assoc();

    $existe = $db->getResultFromQuery(
        "SELECT id
         FROM usuarios
         WHERE email = ?
         AND id <> ?",
        [$email, $id]
    );

    if ($existe->num_rows > 0) {
        throw new Exception(
            'Este e-mail já está sendo utilizado.'
        );
    }

    if (!empty($novaSenha)) {

        if (
            !password_verify(
                $senhaAtual,
                $usuario['senha']
            )
        ) {
            throw new Exception(
                'Senha atual incorreta.'
            );
        }

        if ($novaSenha !== $confirmarSenha) {
            throw new Exception(
                'As senhas não conferem.'
            );
        }

        $hash = password_hash(
            $novaSenha,
            PASSWORD_DEFAULT
        );

        $db->execute(
            "UPDATE usuarios
             SET nome = ?,
                 email = ?,
                 senha = ?
             WHERE id = ?",
            [
                $nome,
                $email,
                $hash,
                $id
            ]
        );

    } else {

        $db->execute(
            "UPDATE usuarios
             SET nome = ?,
                 email = ?
             WHERE id = ?",
            [
                $nome,
                $email,
                $id
            ]
        );
    }

    $_SESSION['nome'] = $nome;

    $_SESSION['sucesso'] =
        'Perfil atualizado com sucesso.';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

header('Location: dashboard.php');
exit;