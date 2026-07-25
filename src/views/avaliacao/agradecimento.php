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
    crossorigin="anonymous"></script>


  <!-- =====================================================
         CSS
    ====================================================== -->

  <style>
    /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

    * {

      box-sizing: border-box;

    }


    /*
        |--------------------------------------------------------------------------
        | BODY
        |--------------------------------------------------------------------------
        */

    body {

      margin: 0;

      min-height: 100vh;

      font-family:
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        Roboto,
        Arial,
        sans-serif;

      background:
        linear-gradient(135deg,
          #250352 0%,
          #4b147d 50%,
          #7437a6 100%);

      display: flex;

      flex-direction: column;

    }


    /*
        |--------------------------------------------------------------------------
        | CONTAINER
        |--------------------------------------------------------------------------
        */

    .agradecimento-container {

      width: 100%;

      max-width: 650px;

      margin: auto;

      padding:
        30px 20px;

      display: flex;

      align-items: center;

      justify-content: center;

    }


    /*
        |--------------------------------------------------------------------------
        | CARD
        |--------------------------------------------------------------------------
        */

    .agradecimento-card {

      width: 100%;

      background: #ffffff;

      border-radius: 24px;

      padding:
        45px 35px;

      text-align: center;

      box-shadow:
        0 15px 50px rgba(0,
          0,
          0,
          0.25);

    }


    /*
        |--------------------------------------------------------------------------
        | ÍCONE DE SUCESSO
        |--------------------------------------------------------------------------
        */

    .icone-sucesso {

      width: 90px;

      height: 90px;

      margin:
        0 auto 25px;

      border-radius: 50%;

      background: #198754;

      color: #ffffff;

      display: flex;

      align-items: center;

      justify-content: center;

      font-size: 42px;

      box-shadow:
        0 8px 20px rgba(25,
          135,
          84,
          0.30);

      animation:
        aparecer 0.6s ease forwards;

    }


    /*
        |--------------------------------------------------------------------------
        | ANIMAÇÃO
        |--------------------------------------------------------------------------
        */

    @keyframes aparecer {

      from {

        opacity: 0;

        transform:
          scale(0.5);

      }

      to {

        opacity: 1;

        transform:
          scale(1);

      }

    }


    /*
        |--------------------------------------------------------------------------
        | TÍTULO
        |--------------------------------------------------------------------------
        */

    .agradecimento-card h1 {

      margin:
        0 0 15px;

      color: #250352;

      font-size: 32px;

      font-weight: 700;

    }


    /*
        |--------------------------------------------------------------------------
        | MENSAGEM PRINCIPAL
        |--------------------------------------------------------------------------
        */

    .mensagem-principal {

      color: #333333;

      font-size: 19px;

      font-weight: 600;

      line-height: 1.5;

      margin-bottom: 15px;

    }


    /*
        |--------------------------------------------------------------------------
        | TEXTO
        |--------------------------------------------------------------------------
        */

    .mensagem-texto {

      color: #6c757d;

      font-size: 15px;

      line-height: 1.7;

      margin-bottom: 10px;

    }


    /*
        |--------------------------------------------------------------------------
        | DIVISOR
        |--------------------------------------------------------------------------
        */

    .divisor {

      width: 60px;

      height: 3px;

      margin:
        30px auto;

      background: #7437a6;

      border-radius: 10px;

    }


    /*
        |--------------------------------------------------------------------------
        | TÍTULO DO SITE
        |--------------------------------------------------------------------------
        */

    .site-titulo {

      color: #250352;

      font-size: 21px;

      font-weight: 700;

      margin-bottom: 10px;

    }


    /*
        |--------------------------------------------------------------------------
        | TEXTO DO SITE
        |--------------------------------------------------------------------------
        */

    .site-descricao {

      color: #6c757d;

      font-size: 15px;

      line-height: 1.6;

      margin-bottom: 25px;

    }


    /*
        |--------------------------------------------------------------------------
        | BOTÃO DO SITE
        |--------------------------------------------------------------------------
        */

    .btn-site {

      width: 100%;

      min-height: 55px;

      display: flex;

      align-items: center;

      justify-content: center;

      padding:
        12px 25px;

      background: #250352;

      border:
        2px solid #250352;

      border-radius: 12px;

      color: #ffffff;

      font-size: 17px;

      font-weight: 600;

      text-decoration: none;

      transition:
        all 0.2s ease;

    }


    .btn-site:hover {

      background: #3b0875;

      border-color: #3b0875;

      color: #ffffff;

      text-decoration: none;

      transform:
        translateY(-2px);

      box-shadow:
        0 8px 20px rgba(37,
          3,
          82,
          0.25);

    }


    /*
        |--------------------------------------------------------------------------
        | BOTÃO ÍCONE
        |--------------------------------------------------------------------------
        */

    .btn-site i {

      font-size: 20px;

    }


    /*
        |--------------------------------------------------------------------------
        | MARCA
        |--------------------------------------------------------------------------
        */

    .marca {

      margin-top: 30px;

      color: #250352;

      font-size: 14px;

      font-weight: 600;

    }


    /*
        |--------------------------------------------------------------------------
        | RODAPÉ
        |--------------------------------------------------------------------------
        */

    .rodape {

      width: 100%;

      padding:
        20px 15px;

      text-align: center;

      color: rgba(255,
          255,
          255,
          0.85);

    }


    .rodape p {

      margin:
        0 0 5px;

      font-size: 14px;

    }


    .rodape small {

      font-size: 12px;

      opacity: 0.7;

    }


    /*
        |--------------------------------------------------------------------------
        | CELULAR
        |--------------------------------------------------------------------------
        */

    @media (max-width: 576px) {

      .agradecimento-container {

        padding:
          20px 12px;

      }


      .agradecimento-card {

        padding:
          35px 22px;

        border-radius: 20px;

      }


      .icone-sucesso {

        width: 75px;

        height: 75px;

        font-size: 34px;

        margin-bottom: 20px;

      }


      .agradecimento-card h1 {

        font-size: 27px;

      }


      .mensagem-principal {

        font-size: 17px;

      }


      .mensagem-texto {

        font-size: 14px;

      }


      .site-titulo {

        font-size: 19px;

      }


      .site-descricao {

        font-size: 14px;

      }


      .btn-site {

        min-height: 52px;

        font-size: 16px;

      }

    }
  </style>

</head>


<body>


  <!-- =========================================================
     CONTEÚDO PRINCIPAL
========================================================= -->

  <main class="agradecimento-container">


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
             MENSAGEM
        ================================================== -->

      <p class="mensagem-principal">

        <?= htmlspecialchars(
          $mensagem,
          ENT_QUOTES,
          'UTF-8'
        ) ?>

      </p>



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
             SITE
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
             BOTÃO SITE
        ================================================== -->

      <a
        href="https://hoteljaraguareal.com.br/"
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

  </main>



  <!-- =========================================================
     RODAPÉ
========================================================= -->

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