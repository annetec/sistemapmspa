<?php
// Incluir o arquivo de configuração do banco de dados
include('config_chamados.php');

// Consultar os usuários com o papel "employee"
$sql = "SELECT id, full_name FROM users WHERE role = 'employee'";
$result = $conn->query($sql);

// Verificar se a consulta falhou
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);  // Exibe o erro real da consulta
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Interno - Chamados</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Mantém o estilo de layout anterior */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 60%;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        h4 {
            text-align: center;
            color: #333;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        .row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .col {
            flex: 1;
            min-width: 280px;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            transition: 0.3s;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Estilo para o campo de busca */
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-input {
            padding: 10px;
            width: 80%;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title text-center">Consulta de Chamados</h4>
            <div class="container">
                
                <!-- Espaço de busca de chamados -->
                <div class="search-container">
                    <input type="text" id="searchNumeroSerie" class="search-input" placeholder="Buscar por Nº de Série" onkeyup="searchData()">
                </div>

                <!-- Aqui os resultados da pesquisa serão exibidos -->
                <div id="results"></div>
                <br>
                <br>
                <form action="processa_chamado.php" method="POST" class="shadow p-4 bg-light rounded">
                    <!-- Campo Número de Série (com a label bloqueada) -->
                    <div class="row">
                        <div class="col form-group">
                        <label for="numeroSerieLabel">Número de Série:</label>
                        <label id="numeroSerieLabel" style="font-weight: bold; color: #007bff; background-color: #f0f0f0; padding: 5px 10px;"></label>
                        <!-- Aqui é exibido o número de série -->

                        </div>
                    </div>
                    <br>
                    <!-- Campo Data do Chamado -->
                    <div class="row">
                        <div class="col form-group">
                            <label for="data_chamado">Data do Chamado:</label>
                            <input type="date" id="data_chamado" name="data_chamado" class="form-control" required>
                        </div>
                    </div>

                    <!-- Tipo de Chamado e Descrição -->
                     <BR>
                    <div class="row">
                        <div class="col form-group">
                            <label for="tipo_chamado">Tipo de Chamado:</label>
                            <select id="tipo_chamado" name="tipo_chamado" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Suporte">Suporte</option>
                                <option value="Instalação">Instalação</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="col form-group">
                            <label for="descricao_chamado">Descrição do Chamado:</label>
                            <textarea id="descricao_chamado" name="descricao_chamado" class="form-control" rows="4" placeholder="Descreva o problema ou a solicitação" required></textarea>
                        </div>
                    </div>

                    <!-- Técnico Responsável -->
                    <div class="row">
                        <div class="col form-group">
                            <label for="tecnico">Técnico Responsável:</label>
                            <select id="tecnico" name="tecnico" class="form-control" required>
                                <option value="">Selecione um técnico</option>
                                <?php
                                // Verificar se a consulta retornou resultados
                                if ($result->num_rows > 0) {
                                    // Preencher a caixa de seleção com os dados dos técnicos
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '">' . $row['full_name'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Nenhum técnico encontrado</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="btn-container">
                        <button type="submit" class="btn btn-success">Salvar Chamado</button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Função chamada quando o campo de busca é alterado
        function searchData() {
            var searchValue = document.getElementById('searchNumeroSerie').value;

            // Verifica se o campo de busca não está vazio
            if (searchValue.length > 0) {
                // Cria a requisição AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'buscar_chamados.php?nserie=' + searchValue, true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Exibe os resultados dentro do elemento com id 'results'
                        document.getElementById('results').innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            } else {
                // Limpa os resultados se o campo de busca estiver vazio
                document.getElementById('results').innerHTML = '';
            }
        }

        // Função para preencher o número de série na label ao clicar em um item
        function selectNumeroSerie(nserie) {
            // Preenche o campo "Número de Série"
            document.getElementById('numeroSerieLabel').textContent = nserie;
        }
    </script>
</body>
</html>
