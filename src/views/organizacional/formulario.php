<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use class\data\Database;

$db = new Database();

$stmt = $db->getResultFromQuery("
    SELECT *
    FROM perguntas
    WHERE ativo = 1
    ORDER BY id
");
?>
<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow border-0">

                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        Pesquisa de Clima e Bem-Estar Organizacional
                    </h3>
                </div>

                <div class="card-body">

                    <div class="alert alert-info">
                        <h5>Instruções</h5>
                        <p class="mb-1">
                            Responda com sinceridade. Esta pesquisa é confidencial.
                        </p>

                        <ul class="mb-0">
                            <li><strong>1</strong> - Sempre</li>
                            <li><strong>2</strong> - Com Frequência</li>
                            <li><strong>3</strong> - Raramente</li>
                            <li><strong>4</strong> - Nunca</li>
                        </ul>
                    </div>

                    <form method="post" action="salvar_pesquisa.php">

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Setor
                            </label>

                            <select
                                name="setor"
                                class="form-select"
                                required>

                                <option value="">
                                    Selecione o setor
                                </option>

                                <option>Recepção/Reservas</option>
                                <option>Governança/Limpeza</option>
                                <option>Cozinha/Salão</option>
                                <option>Administrativo</option>
                                <option>Manutenção</option>
                                <option>Outros</option>

                            </select>
                        </div>

                        <?php while($row = $stmt->fetch_assoc()): ?>

                            <div class="card mb-3 border-light shadow-sm">

                                <div class="card-body">

                                    <div class="d-flex align-items-start">

                                        <span class="badge bg-primary me-3">
                                            <?= $row['id'] ?>
                                        </span>

                                        <div class="flex-grow-1">

                                            <label class="fw-semibold">
                                                <?= htmlspecialchars($row['pergunta']) ?>
                                            </label>

                                            <?php if($row['tipo'] == 'objetiva'): ?>

                                                <select
                                                    name="respostas[<?= $row['id'] ?>]"
                                                    class="form-select mt-2"
                                                    required>

                                                    <option value="">
                                                        Selecione uma opção
                                                    </option>

                                                    <option value="1">
                                                        01 - Sempre
                                                    </option>

                                                    <option value="2">
                                                        02 - Com Frequência
                                                    </option>

                                                    <option value="3">
                                                        03 - Raramente
                                                    </option>

                                                    <option value="4">
                                                        04 - Nunca
                                                    </option>

                                                </select>

                                            <?php else: ?>

                                                <textarea
                                                    name="respostas[<?= $row['id'] ?>]"
                                                    class="form-control mt-2"
                                                    rows="4"
                                                    placeholder="Digite sua resposta..."></textarea>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endwhile; ?>

                        <div class="text-end">

                            <button
                                type="submit"
                                class="btn btn-success btn-lg">

                                Enviar Pesquisa

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>