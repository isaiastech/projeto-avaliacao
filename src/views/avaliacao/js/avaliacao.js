$(document).ready(function () {

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

        this.value = this.value.replace(/[^0-9]/g, '');

    });


    /*
    |--------------------------------------------------------------------------
    | CAPITALIZAÇÃO DO NOME
    |--------------------------------------------------------------------------
    */

    $('#hospede').on('blur', function () {

        let nome = $(this).val().toLowerCase();

        nome = nome
            .split(' ')
            .filter(function (palavra) {
                return palavra.length > 0;
            })
            .map(function (palavra) {

                return palavra.charAt(0).toUpperCase() +
                    palavra.slice(1);

            })
            .join(' ');

        $(this).val(nome);

    });


    /*
    |--------------------------------------------------------------------------
    | ENVIO DO FORMULÁRIO
    |--------------------------------------------------------------------------
    */

    $('#formAvaliacao').on('submit', function () {

        const botao = $('#btnEnviar');

        botao
            .prop('disabled', true)
            .html(
                '<span class="spinner-border spinner-border-sm mr-2"></span>' +
                'Enviando avaliação...'
            );

    });


    /*
    |--------------------------------------------------------------------------
    | DATA NÃO PODE SER FUTURA
    |--------------------------------------------------------------------------
    */

    $('#data_avaliacao').on('change', function () {

        const hoje = new Date()
            .toISOString()
            .split('T')[0];

        if (this.value > hoje) {

            alert('A data da avaliação não pode ser futura.');

            this.value = hoje;

        }

    });

});