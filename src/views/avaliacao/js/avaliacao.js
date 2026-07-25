$(document).ready(function () {

  /*
  |--------------------------------------------------------------------------
  | VARIÁVEIS
  |--------------------------------------------------------------------------
  */

  const form = $('#formAvaliacao');

  const etapas = $('.etapa');

  const totalEtapas = etapas.length;

  let etapaAtual = 0;


  /*
  |--------------------------------------------------------------------------
  | MÁSCARA DE TELEFONE
  |--------------------------------------------------------------------------
  */

  $('#fone').mask('(00) 00000-0000');


  /*
  |--------------------------------------------------------------------------
  | APARTAMENTO SOMENTE NÚMEROS
  |--------------------------------------------------------------------------
  */

  $('#apto').on('input', function () {

    this.value = this.value.replace(
      /[^0-9]/g,
      ''
    );

  });


  /*
  |--------------------------------------------------------------------------
  | NOME - CAPITALIZAÇÃO
  |--------------------------------------------------------------------------
  */

  $('#hospede').on('input', function () {

    let valor = this.value
      .toLowerCase()
      .replace(/\s+/g, ' ');

    this.value = valor;

  });


  /*
  |--------------------------------------------------------------------------
  | MOSTRAR ETAPA
  |--------------------------------------------------------------------------
  */

  function mostrarEtapa(numero) {

    if (
      numero < 0 ||
      numero >= totalEtapas
    ) {

      return;

    }


    /*
    |--------------------------------------------------------------
    | REMOVE ETAPA ATUAL
    |--------------------------------------------------------------
    */

    etapas.removeClass('ativa');


    /*
    |--------------------------------------------------------------
    | MOSTRA NOVA ETAPA
    |--------------------------------------------------------------
    */

    const etapa = etapas.eq(numero);

    etapa.addClass('ativa');


    /*
    |--------------------------------------------------------------
    | ATUALIZA VARIÁVEL
    |--------------------------------------------------------------
    */

    etapaAtual = numero;


    /*
    |--------------------------------------------------------------
    | ATUALIZA PROGRESSO
    |--------------------------------------------------------------
    */

    atualizarProgresso();


    /*
    |--------------------------------------------------------------
    | VOLTA PARA O TOPO
    |--------------------------------------------------------------
    */

    window.scrollTo({

      top: 0,

      behavior: 'smooth'

    });

  }


  /*
  |--------------------------------------------------------------------------
  | ATUALIZA PROGRESSO
  |--------------------------------------------------------------------------
  */

  function atualizarProgresso() {

    const etapa = etapas.eq(etapaAtual);

    const titulo =
      etapa.data('titulo') || 'Avaliação';


    /*
    |--------------------------------------------------------------
    | ELEMENTOS
    |--------------------------------------------------------------
    */

    $('#etapaTitulo').text(titulo);


    /*
    |--------------------------------------------------------------
    | PROGRESSO
    |--------------------------------------------------------------
    */

    let progresso =
      ((etapaAtual + 1) / totalEtapas) * 100;


    $('#barraProgresso').css(
      'width',
      progresso + '%'
    );


    /*
    |--------------------------------------------------------------
    | CONTADOR
    |--------------------------------------------------------------
    */

    if (etapaAtual === 0) {

      $('#contadorEtapa').text(
        'Dados pessoais'
      );

    } else if (
      etapaAtual <= 16
    ) {

      $('#contadorEtapa').text(
        etapaAtual + ' de 16'
      );

    } else if (
      etapaAtual === 17
    ) {

      $('#contadorEtapa').text(
        'Sugestões'
      );

    } else {

      $('#contadorEtapa').text(
        'Finalização'
      );

    }

  }


  /*
  |--------------------------------------------------------------------------
  | VALIDAR DADOS DO HÓSPEDE
  |--------------------------------------------------------------------------
  */

  function validarDados() {

    let valido = true;


    /*
    |--------------------------------------------------------------
    | NOME
    |--------------------------------------------------------------
    */

    const hospede =
      $('#hospede').val().trim();


    if (hospede === '') {

      $('#hospede')
        .addClass('is-invalid')
        .focus();

      valido = false;

    } else {

      $('#hospede')
        .removeClass('is-invalid')
        .addClass('is-valid');

    }


    /*
    |--------------------------------------------------------------
    | APARTAMENTO
    |--------------------------------------------------------------
    */

    const apto =
      $('#apto').val().trim();


    if (
      apto === '' ||
      !/^[0-9]+$/.test(apto)
    ) {

      $('#apto')
        .addClass('is-invalid')
        .focus();

      valido = false;

    } else {

      $('#apto')
        .removeClass('is-invalid')
        .addClass('is-valid');

    }


    /*
    |--------------------------------------------------------------
    | EMAIL
    |--------------------------------------------------------------
    */

    const email =
      $('#email').val().trim();


    if (
      email !== '' &&
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    ) {

      $('#email')
        .addClass('is-invalid')
        .focus();

      valido = false;

    } else {

      $('#email')
        .removeClass('is-invalid');

    }


    return valido;

  }


  /*
  |--------------------------------------------------------------------------
  | BOTÃO COMEÇAR AVALIAÇÃO
  |--------------------------------------------------------------------------
  */

  $('.btn-proximo').on(
    'click',
    function () {

      /*
      |----------------------------------------------------------
      | PRIMEIRA ETAPA
      |----------------------------------------------------------
      */

      if (
        etapaAtual === 0
      ) {

        if (!validarDados()) {

          return;

        }

      }


      /*
      |----------------------------------------------------------
      | VERIFICA RADIO
      |----------------------------------------------------------
      */

      const etapa =
        etapas.eq(etapaAtual);


      const radio =
        etapa.find(
          'input[type="radio"]'
        );


      if (
        radio.length > 0
      ) {

        if (
          !radio.is(':checked')
        ) {

          return;

        }

      }


      /*
      |----------------------------------------------------------
      | AVANÇA
      |----------------------------------------------------------
      */

      mostrarEtapa(
        etapaAtual + 1
      );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | BOTÃO ANTERIOR
  |--------------------------------------------------------------------------
  */

  $('.btn-anterior').on(
    'click',
    function () {

      mostrarEtapa(
        etapaAtual - 1
      );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | SELECIONAR AVALIAÇÃO
  |--------------------------------------------------------------------------
  */

  $('.opcao-avaliacao input').on(
    'change',
    function () {

      const etapa =
        $(this).closest('.etapa');


      /*
      |----------------------------------------------------------
      | ATIVA OPÇÃO
      |----------------------------------------------------------
      */

      etapa
        .find('.opcao-avaliacao')
        .removeClass('selecionada');


      $(this)
        .closest('.opcao-avaliacao')
        .addClass('selecionada');


      /*
      |----------------------------------------------------------
      | HABILITA PRÓXIMO
      |----------------------------------------------------------
      */

      etapa
        .find('.btn-proximo')
        .prop(
          'disabled',
          false
        );


      /*
      |----------------------------------------------------------
      | AVANÇA AUTOMATICAMENTE
      |----------------------------------------------------------
      */

      setTimeout(
        function () {

          mostrarEtapa(
            etapaAtual + 1
          );

        },
        350
      );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | CONTADOR DE SUGESTÕES
  |--------------------------------------------------------------------------
  */

  $('#sugestoes').on(
    'input',
    function () {

      $('#contadorCaracteres').text(
        this.value.length
      );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | CONFIRMAR AVALIAÇÃO
  |--------------------------------------------------------------------------
  */

  $('.btn-confirmar').on(
    'click',
    function () {

      gerarResumo();

      mostrarEtapa(
        etapaAtual + 1
      );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | GERAR RESUMO
  |--------------------------------------------------------------------------
  */

  function gerarResumo() {

    let html = '';


    /*
    |--------------------------------------------------------------
    | DADOS
    |--------------------------------------------------------------
    */

    html += `
            <div class="resumo-bloco">

                <h3>
                    <i class="fas fa-user"></i>
                    Seus dados
                </h3>

                <p>
                    <strong>Nome:</strong>
                    ${escapeHtml($('#hospede').val())}
                </p>

                <p>
                    <strong>Apartamento:</strong>
                    ${escapeHtml($('#apto').val())}
                </p>

            </div>
        `;


    /*
    |--------------------------------------------------------------
    | AVALIAÇÕES
    |--------------------------------------------------------------
    */

    html += `
            <div class="resumo-bloco">

                <h3>
                    <i class="fas fa-star"></i>
                    Suas avaliações
                </h3>
        `;


    $('.etapa-avaliacao').each(
      function () {

        const etapa =
          $(this);


        const titulo =
          etapa.data('titulo');


        const selecionado =
          etapa.find(
            'input[type="radio"]:checked'
          );


        if (
          selecionado.length
        ) {

          const texto =
            selecionado
              .closest(
                '.opcao-avaliacao'
              )
              .find(
                '.opcao-texto'
              )
              .text()
              .trim();


          html += `
                        <div class="resumo-item">

                            <span>
                                ${escapeHtml(titulo)}
                            </span>

                            <strong>
                                ${escapeHtml(texto)}
                            </strong>

                        </div>
                    `;

        }

      }
    );


    html += `
            </div>
        `;


    /*
    |--------------------------------------------------------------
    | SUGESTÃO
    |--------------------------------------------------------------
    */

    const sugestao =
      $('#sugestoes').val().trim();


    if (
      sugestao !== ''
    ) {

      html += `
                <div class="resumo-bloco">

                    <h3>
                        <i class="fas fa-comment"></i>
                        Sua sugestão
                    </h3>

                    <p class="resumo-sugestao">
                        ${escapeHtml(sugestao)}
                    </p>

                </div>
            `;

    }


    /*
    |--------------------------------------------------------------
    | INSERE RESUMO
    |--------------------------------------------------------------
    */

    $('#resumoAvaliacao').html(
      html
    );

  }


  /*
  |--------------------------------------------------------------------------
  | ESCAPAR HTML
  |--------------------------------------------------------------------------
  */

  function escapeHtml(text) {

    return $('<div>')
      .text(text)
      .html();

  }


  /*
  |--------------------------------------------------------------------------
  | SUBMIT
  |--------------------------------------------------------------------------
  */

  form.on(
    'submit',
    function () {

      const botao =
        $('#btnEnviar');


      botao.prop(
        'disabled',
        true
      );


      botao.find(
        '.texto-enviar'
      ).hide();


      botao.find(
        '.texto-loading'
      ).show();

    }
  );


  /*
  |--------------------------------------------------------------------------
  | REMOVE VALIDAÇÃO AO DIGITAR
  |--------------------------------------------------------------------------
  */

  $('input').on(
    'input',
    function () {

      $(this)
        .removeClass(
          'is-invalid'
        );

    }
  );


  /*
  |--------------------------------------------------------------------------
  | INICIALIZA
  |--------------------------------------------------------------------------
  */

  mostrarEtapa(0);

});