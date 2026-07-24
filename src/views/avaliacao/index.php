<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$itens = [
    'cafe_manha' => '☕ Café da manhã',
    'colchao' => '🛏️ Colchão',
    'travesseiro' => '🛌 Travesseiro',
    'limpeza' => '🧹 Limpeza',
    'frigobar' => '🧊 Frigobar',
    'chuveiro_aquecimento' => '🚿 Chuveiro (aquecimento)',
    'chuveiro_ducha' => '🚿 Chuveiro (ducha)',
    'ar_condicionado' => '❄️ Ar condicionado',
    'roupa_cama' => '🛏️ Roupa de cama',
    'internet' => '🌐 Internet',
    'bar' => '🍹 Bar',
    'atendimento' => '🙋 Atendimento',
    'reserva' => '📋 Reserva',
    'recepcao' => '🛎️ Recepção',
    'camareira' => '🧹 Camareira',
    'garcom' => '🍽️ Garçom'
];

$opcoes = [
    'otimo' => '😍 Ótimo',
    'bom' => '😊 Bom',
    'satisfatorio' => '😐 Satisfatório',
    'ruim' => '😞 Ruim'
];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <meta
        name="description"
        content="Avalie sua experiência conosco"
    >

    <meta
        name="theme-color"
        content="#250352"
    >

    <title>Avaliação do Hóspede</title>

    <!-- Favicon -->
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="/icons/icons/favicon-32x32.png"
    >

    <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
    >

    <!-- Font Awesome -->
    <script
        src="https://kit.fontawesome.com/21a7183a5f.js"
        crossorigin="anonymous"
    ></script>

    <!-- CSS personalizado -->
    <link
        rel="stylesheet"
        href="css/avaliacao.css?v=<?= filemtime(__DIR__ . '/css/avaliacao.css') ?>"
    >

</head>

<body>

<nav
    class="navbar navbar-dark"
    style="background-color: #250352;"
>

    <div class="container">

        <div class="d-flex align-items-center">

            <img
                src="/images/image-painel.png"
                class="rounded-circle shadow-sm"
                width="50"
                height="50"
                alt="Logo"
            >

            <div class="ml-3">

                <div class="font-weight-bold text-white">
                    Avaliação do Hóspede
                </div>

                <small class="text-light">
                    Sua opinião é muito importante para nós
                </small>

            </div>

        </div>

    </div>

</nav>


<main>

<div class="container my-4">

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle mr-2"></i>

            <?= htmlspecialchars($_SESSION['mensagem_sucesso']) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >
                <span>&times;</span>
            </button>

        </div>

        <?php unset($_SESSION['mensagem_sucesso']); ?>

    <?php endif; ?>


    <?php if (isset($_SESSION['mensagem_erro'])): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle mr-2"></i>

            <?= htmlspecialchars($_SESSION['mensagem_erro']) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >
                <span>&times;</span>
            </button>

        </div>

        <?php unset($_SESSION['mensagem_erro']); ?>

    <?php endif; ?>


    <!-- TÍTULO -->

    <div class="text-center mb-4">

        <h1 class="h3 font-weight-bold">

            ☕ Avalie sua experiência

        </h1>

        <p class="text-muted">

            Sua opinião é muito importante para nós.

        </p>

    </div>


    <!-- FORMULÁRIO -->

    <form
        action="salvar_avaliacao.php"
        method="POST"
        id="formAvaliacao"
    >

        <!-- CSRF -->

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>"
        >


        <!-- DADOS DO HÓSPEDE -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <i class="fas fa-user mr-2"></i>

                Dados do Hóspede

            </div>


            <div class="card-body">

                <div class="form-row">


                    <!-- HÓSPEDE -->

                    <div class="form-group col-md-6">

                        <label for="hospede">

                            👤 Nome completo

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="hospede"
                            name="hospede"
                            placeholder="Digite seu nome completo"
                            autocomplete="name"
                            maxlength="150"
                            required
                        >

                    </div>


                    <!-- APARTAMENTO -->

                    <div class="form-group col-md-3">

                        <label for="apto">

                            🏨 Apartamento

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
                            required
                        >

                    </div>


                    <!-- DATA -->

                    <div class="form-group col-md-3">

                        <label for="data_avaliacao">

                            📅 Data

                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="data_avaliacao"
                            name="data_avaliacao"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >

                    </div>


                    <!-- TELEFONE -->

                    <div class="form-group col-md-6">

                        <label for="fone">

                            📱 Telefone

                        </label>

                        <input
                            type="tel"
                            class="form-control"
                            id="fone"
                            name="fone"
                            placeholder="(00) 00000-0000"
                            maxlength="15"
                            inputmode="numeric"
                            autocomplete="tel"
                        >

                    </div>


                    <!-- E-MAIL -->

                    <div class="form-group col-md-6">

                        <label for="email">

                            📧 E-mail

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="exemplo@email.com"
                            autocomplete="email"
                            maxlength="150"
                        >

                    </div>

                </div>

            </div>

        </div>


        <!-- AVALIAÇÃO -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <i class="fas fa-star mr-2"></i>

                Avalie nossos serviços

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    <i class="fas fa-info-circle mr-2"></i>

                    Selecione uma opção para cada item.

                </div>


                <?php foreach ($itens as $nome => $descricao): ?>

                    <div class="card mb-3 avaliacao-item">

                        <div class="card-header font-weight-bold">

                            <?= htmlspecialchars($descricao) ?>

                        </div>


                        <div class="card-body py-3">

                            <div class="form-row">

                                <?php foreach ($opcoes as $valor => $texto): ?>

                                    <div class="col-6 col-md-3 mb-2 mb-md-0">

                                        <div class="custom-control custom-radio">

                                            <input
                                                type="radio"
                                                class="custom-control-input"
                                                id="<?= $nome ?>_<?= $valor ?>"
                                                name="<?= $nome ?>"
                                                value="<?= $valor ?>"
                                                required
                                            >

                                            <label
                                                class="custom-control-label"
                                                for="<?= $nome ?>_<?= $valor ?>"
                                            >

                                                <?= $texto ?>

                                            </label>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- SUGESTÕES -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <i class="fas fa-comment-alt mr-2"></i>

                Deixe suas sugestões

            </div>


            <div class="card-body">

                <div class="form-group mb-0">

                    <label for="sugestoes">

                        Suas sugestões, elogios ou observações

                    </label>

                    <textarea
                        class="form-control"
                        id="sugestoes"
                        name="sugestoes"
                        rows="5"
                        maxlength="2000"
                        placeholder="Digite aqui suas sugestões..."
                    ></textarea>

                </div>

            </div>

        </div>


        <!-- BOTÃO -->

        <div class="text-center mb-4">

            <button
                type="submit"
                class="btn btn-primary btn-lg px-5"
                id="btnEnviar"
            >

                <i class="fas fa-paper-plane mr-2"></i>

                <span id="textoBotao">
                    Enviar avaliação
                </span>

            </button>

        </div>

    </form>

</div>

</main>


<footer>

    <div
        class="text-center p-3 text-white"
        style="background-color: #250352;"
    >

        isaiasTech © <?= date('Y') ?>

    </div>

</footer>


<!-- jQuery -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- jQuery Mask -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<!-- Popper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>


<!-- Bootstrap -->

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>


<!-- JavaScript -->

<script
    src="js/avaliacao.js?v=<?= filemtime(__DIR__ . '/js/avaliacao.js') ?>"
></script>


</body>

</html>