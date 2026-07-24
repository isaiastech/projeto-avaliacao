$(document).ready(function () {

$(document).ready(function () {

    // ==========================================
    // MÁSCARA DINÂMICA DE TELEFONE
    // ==========================================
    $('#fone').mask('(00) 0000-0000', {

        onKeyPress: function (value, event, field, options) {

            // Remove tudo que não for número
            let telefone = value.replace(/\D/g, '');

            // Primeiro dígito após o DDD
            // Exemplo:
            // (49) 99999-9999
            //       ^
            //       primeiro dígito = 9
            let primeiroDigito = telefone.charAt(2);

            // Se o número começar com 9 após o DDD,
            // muda para máscara de celular
            if (primeiroDigito === '9') {

                $(field).mask('(00) 00000-0000', options);

            } else {

                // Caso contrário, telefone fixo
                $(field).mask('(00) 0000-0000', options);

            }

        }

    });


    // ==========================================
    // VALIDAÇÃO DO TELEFONE
    // ==========================================
    $('#fone').on('blur', function () {

        let telefone = $(this).val().replace(/\D/g, '');

        // Telefone não é obrigatório
        if (telefone.length === 0) {

            $(this).removeClass('is-valid is-invalid');

            return;

        }

        // Telefone válido:
        // 10 dígitos = fixo
        // 11 dígitos = celular
        if (telefone.length === 10 || telefone.length === 11) {

            $(this)
                .removeClass('is-invalid')
                .addClass('is-valid');

        } else {

            $(this)
                .removeClass('is-valid')
                .addClass('is-invalid');

        }

    });

});

  // ==========================================
  // CAPITALIZAÇÃO DO NOME DO HÓSPEDE
  // ==========================================
  $('#hospede').on('input', function () {

    let valor = $(this).val().toLowerCase();

    valor = valor.replace(
      /(^|\s)\S/g,
      function (letra) {
        return letra.toUpperCase();
      }
    );

    $(this).val(valor);

  });


  // ==========================================
  // APARTAMENTO - SOMENTE NÚMEROS
  // ==========================================
  $('#apto').on('input', function () {

    this.value = this.value.replace(/\D/g, '');

  });


  // ==========================================
  // VALIDAÇÃO VISUAL DOS CAMPOS
  // ==========================================
  $('form').on('submit', function (event) {

    let formulario = this;
    let formularioValido = true;

    $(formulario)
      .find('input[required], textarea[required], select[required]')
      .each(function () {

        if (!this.checkValidity()) {

          $(this)
            .removeClass('is-valid')
            .addClass('is-invalid');

          formularioValido = false;

        } else {

          $(this)
            .removeClass('is-invalid')
            .addClass('is-valid');

        }

      });


    // Impede envio se houver erro
    if (!formularioValido) {

      event.preventDefault();

      return false;

    }


    // ==========================================
    // LOADING NO BOTÃO DE ENVIO
    // ==========================================
    let botao = $(formulario).find('button[type="submit"]');

    botao
      .prop('disabled', true)
      .html(
        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' +
        'Enviando avaliação...'
      );

  });


  // ==========================================
  // REMOVER ERRO AO DIGITAR
  // ==========================================
  $('input, textarea, select').on('input change', function () {

    if (this.checkValidity()) {

      $(this)
        .removeClass('is-invalid')
        .addClass('is-valid');

    } else {

      $(this)
        .removeClass('is-valid');

    }

  });


  // ==========================================
  // VALIDAÇÃO DO TELEFONE
  // ==========================================
  $('#fone').on('blur', function () {

    let telefone = $(this).val().replace(/\D/g, '');

    // Telefone não é obrigatório
    if (telefone.length === 0) {

      $(this).removeClass('is-valid is-invalid');

      return;

    }

    // 10 dígitos = fixo
    // 11 dígitos = celular
    if (telefone.length === 10 || telefone.length === 11) {

      $(this)
        .removeClass('is-invalid')
        .addClass('is-valid');

    } else {

      $(this)
        .removeClass('is-valid')
        .addClass('is-invalid');

    }

  });

});

