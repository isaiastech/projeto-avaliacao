<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../../vendor/autoload.php';

session_start();


/*
|--------------------------------------------------------------------------
| TOKEN CSRF
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {

  $_SESSION['csrf_token'] =
    bin2hex(random_bytes(32));
}


/*
|--------------------------------------------------------------------------
| ITENS DA AVALIAÇÃO
|--------------------------------------------------------------------------
*/

$itens = [

  'cafe_manha' => [
    'titulo' => 'Café da manhã',
    'icone' => '☕',
    'descricao' => 'Como você avalia nosso café da manhã?'
  ],

  'colchao' => [
    'titulo' => 'Colchão',
    'icone' => '🛏️',
    'descricao' => 'Como você avalia o conforto do colchão?'
  ],

  'travesseiro' => [
    'titulo' => 'Travesseiro',
    'icone' => '🛌',
    'descricao' => 'Como você avalia o conforto do travesseiro?'
  ],

  'limpeza' => [
    'titulo' => 'Limpeza',
    'icone' => '🧹',
    'descricao' => 'Como você avalia a limpeza do ambiente?'
  ],

  'frigobar' => [
    'titulo' => 'Frigobar',
    'icone' => '🧊',
    'descricao' => 'Como você avalia o frigobar?'
  ],

  'chuveiro_aquecimento' => [
    'titulo' => 'Aquecimento do chuveiro',
    'icone' => '🚿',
    'descricao' => 'Como você avalia o aquecimento do chuveiro?'
  ],

  'chuveiro_ducha' => [
    'titulo' => 'Ducha do chuveiro',
    'icone' => '🚿',
    'descricao' => 'Como você avalia a ducha do chuveiro?'
  ],

  'ar_condicionado' => [
    'titulo' => 'Ar condicionado',
    'icone' => '❄️',
    'descricao' => 'Como você avalia o ar condicionado?'
  ],

  'roupa_cama' => [
    'titulo' => 'Roupa de cama',
    'icone' => '🛏️',
    'descricao' => 'Como você avalia a roupa de cama?'
  ],

  'internet' => [
    'titulo' => 'Internet',
    'icone' => '🌐',
    'descricao' => 'Como você avalia a qualidade da internet?'
  ],

  'bar' => [
    'titulo' => 'Bar',
    'icone' => '🍹',
    'descricao' => 'Como você avalia nosso serviço de bar?'
  ],

  'atendimento' => [
    'titulo' => 'Atendimento',
    'icone' => '🙋',
    'descricao' => 'Como você avalia nosso atendimento?'
  ],

  'reserva' => [
    'titulo' => 'Reserva',
    'icone' => '📋',
    'descricao' => 'Como você avalia o processo de reserva?'
  ],

  'recepcao' => [
    'titulo' => 'Recepção',
    'icone' => '🛎️',
    'descricao' => 'Como você avalia nossa recepção?'
  ],

  'camareira' => [
    'titulo' => 'Camareira',
    'icone' => '🧹',
    'descricao' => 'Como você avalia o serviço de camareira?'
  ],

  'garcom' => [
    'titulo' => 'Garçom',
    'icone' => '🍽️',
    'descricao' => 'Como você avalia nosso atendimento de garçom?'
  ]

];


/*
|--------------------------------------------------------------------------
| OPÇÕES
|--------------------------------------------------------------------------
*/

$opcoes = [

  'otimo' => [
    'texto' => 'Ótimo',
    'emoji' => '😍'
  ],

  'bom' => [
    'texto' => 'Bom',
    'emoji' => '😊'
  ],

  'satisfatorio' => [
    'texto' => 'Satisfatório',
    'emoji' => '😐'
  ],

  'ruim' => [
    'texto' => 'Ruim',
    'emoji' => '😞'
  ]

];

?>
<!DOCTYPE html>

<html lang="pt-BR">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

  <meta
    name="description"
    content="Avaliação da experiência do hóspede">

  <meta
    name="theme-color"
    content="#250352">

  <title>Avaliação do Hóspede</title>


  <!-- Favicon -->

  <link
    rel="icon"
    type="image/png"
    href="/icons/icons/favicon-32x32.png">


  <!-- Bootstrap -->

  <link
    rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">


  <!-- Font Awesome -->

  <script
    src="https://kit.fontawesome.com/21a7183a5f.js"
    crossorigin="anonymous"></script>


  <!-- CSS -->

  <link
    rel="stylesheet"
    href="css/avaliacao.css?v=<?= filemtime(__DIR__ . '/css/avaliacao.css') ?>">

</head>


<body>


  <!-- =========================================================
     CABEÇALHO
========================================================= -->

  <header class="topo">

    <div class="topo-conteudo">

      <div class="logo-circulo">

        <i class="fas fa-star"></i>

      </div>


      <div>

        <h1>Avaliação do Hóspede</h1>

        <p>
          Sua opinião é muito importante para nós.
        </p>

      </div>

    </div>

  </header>



  <!-- =========================================================
     CONTEÚDO
========================================================= -->

  <main class="container avaliacao-container">


    <!-- ALERTA DE SUCESSO -->

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>

      <div
        class="alert alert-success alert-dismissible fade show"
        role="alert">

        <i class="fas fa-check-circle mr-2"></i>

        <?= htmlspecialchars(
          $_SESSION['mensagem_sucesso']
        ) ?>

        <button
          type="button"
          class="close"
          data-dismiss="alert">

          <span>&times;</span>

        </button>

      </div>

      <?php
      unset($_SESSION['mensagem_sucesso']);
      ?>

    <?php endif; ?>


    <!-- ALERTA DE ERRO -->

    <?php if (isset($_SESSION['mensagem_erro'])): ?>

      <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert">

        <i class="fas fa-exclamation-circle mr-2"></i>

        <?= htmlspecialchars(
          $_SESSION['mensagem_erro']
        ) ?>

        <button
          type="button"
          class="close"
          data-dismiss="alert">

          <span>&times;</span>

        </button>

      </div>

      <?php
      unset($_SESSION['mensagem_erro']);
      ?>

    <?php endif; ?>



    <!-- =====================================================
         FORMULÁRIO
    ====================================================== -->

    <form
      action="salvar_avaliacao.php"
      method="POST"
      id="formAvaliacao"
      novalidate>


      <!-- CSRF -->

      <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
                  $_SESSION['csrf_token']
                ) ?>">


      <!-- =================================================
             PROGRESSO
        ================================================== -->

      <div class="progresso-container">

        <div class="progresso-info">

          <span id="etapaTitulo">
            Seus dados
          </span>

          <span id="contadorEtapa">
            1 de <?= count($itens) ?>
          </span>

        </div>


        <div class="progress">

          <div
            id="barraProgresso"
            class="progress-bar"
            role="progressbar"
            style="width: 5%"></div>

        </div>

      </div>



      <!-- =================================================
             ETAPA 1 - DADOS
        ================================================== -->

      <section
        class="etapa ativa"
        data-etapa="0"
        data-titulo="Seus dados">

        <div class="card-etapa">

          <div class="icone-etapa">

            👤

          </div>

          <h2>
            Conte-nos um pouco sobre você
          </h2>

          <p class="descricao-etapa">

            Preencha seus dados para iniciarmos
            sua avaliação.

          </p>


          <!-- HÓSPEDE -->

          <div class="form-group">

            <label for="hospede">

              <i class="fas fa-user"></i>

              Nome completo

            </label>

            <input
              type="text"
              class="form-control"
              id="hospede"
              name="hospede"
              placeholder="Digite seu nome completo"
              autocomplete="name"
              required>

            <div class="invalid-feedback">

              Informe seu nome completo.

            </div>

          </div>


          <!-- APARTAMENTO -->

          <div class="form-group">

            <label for="apto">

              <i class="fas fa-bed"></i>

              Apartamento

            </label>

            <input
              type="text"
              class="form-control"
              id="apto"
              name="apto"
              placeholder="Ex.: 101"
              inputmode="numeric"
              pattern="[0-9]+"
              maxlength="5"
              required>

            <div class="invalid-feedback">

              Informe o número do apartamento.

            </div>

          </div>


          <!-- TELEFONE -->

          <div class="form-group">

            <label for="fone">

              <i class="fas fa-phone"></i>

              Telefone

              <span class="opcional">
                Opcional
              </span>

            </label>

            <input
              type="tel"
              class="form-control"
              id="fone"
              name="fone"
              placeholder="(00) 00000-0000"
              maxlength="15"
              inputmode="numeric"
              autocomplete="tel">

          </div>


          <!-- EMAIL -->

          <div class="form-group">

            <label for="email">

              <i class="fas fa-envelope"></i>

              E-mail

              <span class="opcional">
                Opcional
              </span>

            </label>

            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="exemplo@email.com"
              autocomplete="email">

            <div class="invalid-feedback">

              Informe um e-mail válido.

            </div>

          </div>


          <!-- DATA -->

          <div class="form-group">

            <label for="data_avaliacao">

              <i class="fas fa-calendar"></i>

              Data da avaliação

            </label>

            <input
              type="date"
              class="form-control"
              id="data_avaliacao"
              name="data_avaliacao"
              value="<?= date('Y-m-d') ?>"
              required>

          </div>


          <button
            type="button"
            class="btn btn-principal btn-block btn-proximo">

            Começar avaliação

            <i class="fas fa-arrow-right ml-2"></i>

          </button>

        </div>

      </section>



      <!-- =================================================
             ETAPAS DE AVALIAÇÃO
        ================================================== -->

      <?php

      $numero = 1;

      foreach ($itens as $nome => $item):

      ?>

        <section
          class="etapa etapa-avaliacao"
          data-etapa="<?= $numero ?>"
          data-titulo="<?= htmlspecialchars(
                          $item['titulo']
                        ) ?>">

          <div class="card-etapa">

            <div class="icone-etapa">

              <?= $item['icone'] ?>

            </div>


            <span class="numero-item">

              Item <?= $numero ?> de <?= count($itens) ?>

            </span>


            <h2>

              <?= htmlspecialchars(
                $item['titulo']
              ) ?>

            </h2>


            <p class="descricao-etapa">

              <?= htmlspecialchars(
                $item['descricao']
              ) ?>

            </p>


            <div class="opcoes-avaliacao">

              <?php foreach (
                $opcoes as $valor => $opcao
              ): ?>

                <label
                  class="opcao-avaliacao">

                  <input
                    type="radio"
                    name="<?= $nome ?>"
                    value="<?= $valor ?>"
                    required>


                  <span class="opcao-conteudo">

                    <span class="opcao-emoji">

                      <?= $opcao['emoji'] ?>

                    </span>


                    <span class="opcao-texto">

                      <?= $opcao['texto'] ?>

                    </span>


                    <span class="opcao-check">

                      <i class="fas fa-check"></i>

                    </span>

                  </span>

                </label>

              <?php endforeach; ?>

            </div>


            <div class="navegacao">

              <button
                type="button"
                class="btn btn-anterior">

                <i class="fas fa-arrow-left"></i>

                Anterior

              </button>


              <button
                type="button"
                class="btn btn-proximo"
                disabled>

                Próximo

                <i class="fas fa-arrow-right"></i>

              </button>

            </div>

          </div>

        </section>


      <?php

        $numero++;

      endforeach;

      ?>



      <!-- =================================================
             SUGESTÕES
        ================================================== -->

      <section
        class="etapa"
        data-etapa="<?= count($itens) + 1 ?>"
        data-titulo="Sugestões">

        <div class="card-etapa">

          <div class="icone-etapa">

            💬

          </div>


          <h2>

            Quer deixar uma sugestão?

          </h2>


          <p class="descricao-etapa">

            Conte para nós como podemos
            melhorar sua experiência.

          </p>


          <div class="form-group">

            <textarea
              class="form-control campo-sugestoes"
              id="sugestoes"
              name="sugestoes"
              rows="6"
              maxlength="2000"
              placeholder="Digite aqui seus elogios, sugestões ou observações..."></textarea>


            <div class="contador-caracteres">

              <span id="contadorCaracteres">
                0
              </span>

              / 2000

            </div>

          </div>


          <div class="navegacao">

            <button
              type="button"
              class="btn btn-anterior">

              <i class="fas fa-arrow-left"></i>

              Anterior

            </button>


            <button
              type="button"
              class="btn btn-principal btn-confirmar">

              Revisar avaliação

              <i class="fas fa-check ml-2"></i>

            </button>

          </div>

        </div>

      </section>



      <!-- =================================================
             CONFIRMAÇÃO
        ================================================== -->

      <section
        class="etapa"
        data-etapa="<?= count($itens) + 2 ?>"
        data-titulo="Finalizar">

        <div class="card-etapa">

          <div class="icone-etapa">

            ✅

          </div>


          <h2>

            Tudo pronto!

          </h2>


          <p class="descricao-etapa">

            Confira seus dados e envie sua avaliação.

          </p>


          <div
            id="resumoAvaliacao"
            class="resumo-avaliacao">

          </div>


          <div class="navegacao">

            <button
              type="button"
              class="btn btn-anterior">

              <i class="fas fa-arrow-left"></i>

              Voltar

            </button>


            <button
              type="submit"
              class="btn btn-enviar"
              id="btnEnviar">

              <span class="texto-enviar">

                <i class="fas fa-paper-plane mr-2"></i>

                Enviar avaliação

              </span>


              <span
                class="texto-loading"
                style="display:none;">

                <span
                  class="spinner-border spinner-border-sm mr-2"></span>

                Enviando...

              </span>

            </button>

          </div>

        </div>

      </section>


    </form>

  </main>



  <!-- =========================================================
     RODAPÉ
========================================================= -->

  <footer class="rodape">

    <p>

      Sua opinião nos ajuda a melhorar. ❤️

    </p>

    <small>

      Hotel Jaraguá © <?= date('Y') ?>

    </small>

  </footer>



  <!-- jQuery -->

  <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


  <!-- jQuery Mask -->

  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


  <!-- Bootstrap -->

  <script
    src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>


  <!-- JavaScript -->

  <script
    src="js/avaliacao.js?v=<?= filemtime(__DIR__ . '/js/avaliacao.js') ?>"></script>


</body>

</html>