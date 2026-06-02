<?php

namespace class\mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Exception as BaseException;

class Mail
{
    public function enviar(
        string $destinatario,
        string $nome,
        string $assunto,
        string $html
    ): bool {

        $env = parse_ini_file('/var/www/config/.env');

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = $env['mail_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $env['mail_username'];
            $mail->Password = $env['mail_password'];
            $mail->SMTPSecure = $env['mail_encryption'];
            $mail->Port = (int)$env['mail_port'];

            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                $env['mail_from'],
                $env['mail_from_name']
            );

            $mail->addAddress(
                $destinatario,
                $nome
            );

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $html;

            return $mail->send();

        } catch (Exception $e) {

            throw new BaseException(
                'Erro ao enviar e-mail: ' . $e->getMessage()
            );
        }
    }
}