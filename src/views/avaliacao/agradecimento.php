<?php

date_default_timezone_set('America/Sao_Paulo');

session_start();

/*
|--------------------------------------------------------------------------
| MENSAGEM DE SUCESSO
|--------------------------------------------------------------------------
*/

$mensagem = $_SESSION['mensagem_sucesso']
    ?? 'Sua avaliação foi registrada com sucesso.';

/*
|--------------------------------------------------------------------------
| REMOVE A MENSAGEM DA SESSÃO
|--------------------------------------------------------------------------
*/

unset($_SESSION['mensagem_sucesso']);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="theme-color"
        content="#250352">

    <meta
        name="description"
        content="Obrigado por avaliar sua experiência no Hotel Jaraguá Real.">

    <title>
        Obrigado pela sua avaliação
    </title>


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="icon"
        type="image/png"
        href="/icons/icons/favicon-32x32.png">


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <script
        src="https://kit.fontawesome.com/21a7183a5f.js"
        crossorigin="anonymous">
    </script>


    <!-- =====================================================
         CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="css/agradecimento.css">

</head>


<body>


    <!-- =========================================================
         CONTEÚDO PRINCIPAL
    ========================================================== -->

    <main class="agradecimento-container">


        <!-- =====================================================
             CARD
        ====================================================== -->

        <div class="agradecimento-card">


            <!-- =================================================
                 ÍCONE DE SUCESSO
            ================================================== -->

            <div class="icone-sucesso">

                <i class="fas fa-check"></i>

            </div>


            <!-- =================================================
                 TÍTULO
            ================================================== -->

            <h1>

                Obrigado! ❤️

            </h1>


            <!-- =================================================
                 MENSAGEM PRINCIPAL
            ================================================== -->

            <p class="mensagem-principal">

                <?= htmlspecialchars(
                    $mensagem,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>


            <!-- =================================================
                 MENSAGEM
            ================================================== -->

            <p class="mensagem-texto">

                Sua opinião é muito importante para nós
                e nos ajuda a melhorar cada vez mais
                a experiência dos nossos hóspedes.

            </p>


            <p class="mensagem-texto">

                Esperamos receber você novamente
                em breve!

            </p>


            <!-- =================================================
                 DIVISOR
            ================================================== -->

            <div class="divisor"></div>


            <!-- =================================================
                 AVALIAÇÃO NO GOOGLE
            ================================================== -->

            <div class="google-avaliacao">


                <h2 class="site-titulo">

                    ⭐ Gostou da sua experiência?

                </h2>


                <p class="site-descricao">

                    Sua avaliação no Google é muito importante
                    para nós e ajuda outros hóspedes a conhecerem
                    o Hotel Jaraguá Real.

                </p>


                <a
                    href="https://g.page/r/CQVQ10nKzo7wEAE/review"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-google">

                    <i class="fab fa-google mr-2"></i>

                    Avaliar o Hotel no Google

                </a>


            </div>


            <!-- =================================================
                 DIVISOR
            ================================================== -->

            <div class="divisor"></div>


            <!-- =================================================
                 SITE DO HOTEL
            ================================================== -->

            <h2 class="site-titulo">

                Quer conhecer mais sobre nós?

            </h2>


            <p class="site-descricao">

                Acesse o site do Hotel Jaraguá Real
                e conheça nossas acomodações,
                serviços e tudo o que preparamos
                para você.

            </p>


            <!-- =================================================
                 BOTÃO DO SITE
            ================================================== -->

            <a
                href="https://hoteljaraguareal.com.br/"
                target="_blank"
                rel="noopener noreferrer"
                class="btn-site">

                <i class="fas fa-globe mr-2"></i>

                Conheça nosso site

            </a>


            <!-- =================================================
                 MARCA
            ================================================== -->

            <div class="marca">

                Hotel Jaraguá

            </div>


        </div>
        <!-- FIM DO CARD -->


    </main>


    <!-- =========================================================
         RODAPÉ
    ========================================================== -->

    <footer class="rodape">


        <p>

            Obrigado por fazer parte da nossa história. ❤️

        </p>


        <small>

            Hotel Jaraguá © <?= date('Y') ?>

        </small>


    </footer>


</body>

</html>
```
