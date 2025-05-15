<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "alice";
$password = "cpd@sorento";
$dbname = "task_management_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Pegar o número da página atual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Verificar se foi enviado um termo de busca
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Consulta com filtro por "condicao = 'aberta'"
$sql = "SELECT * FROM ordens_servico 
        WHERE condicao = 'aberta' AND 
              (numero_serie LIKE '%$searchTerm%' OR 
               numero_os LIKE '%$searchTerm%' OR 
               responsavel LIKE '%$searchTerm%')
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$ordens_servico = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ordens_servico[] = $row;
    }
}

// Contagem total filtrada
$sql_total = "SELECT COUNT(*) AS total FROM ordens_servico 
              WHERE condicao = 'aberta' AND 
                    (numero_serie LIKE '%$searchTerm%' OR 
                     numero_os LIKE '%$searchTerm%' OR 
                     responsavel LIKE '%$searchTerm%')";

$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 85%;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin-top: 15px;
        }
        .button-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
        .pagination a {
            padding: 8px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
            cursor: pointer;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .form-container {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container input, .form-container textarea, .form-container select {
            width: 300px;
            padding: 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin: 10px 0;
            transition: border-color 0.3s;
        }
        .form-container input:focus, .form-container textarea:focus, .form-container select:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-container label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 8px;
        }
        .form-container textarea {
            height: 100px;
        }
        #statusMessage {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: green;
        }
        .pagination-container {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-btn {
            margin: 0 8px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .pagination-btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .pagination-btn:active {
            transform: translateY(1px);
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 50px;
        }

        /* Estilo do campo de pesquisa */
        .search-container input[type="text"] {
            width: 100%;
            max-width: 500px;
            padding: 12px 40px 12px 20px; /* Espaçamento para o ícone e o texto */
            border-radius: 50px; /* Bordas arredondadas */
            border: 2px solid #ccc;
            font-size: 16px;
            background-color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Estilo quando o campo de busca é focado */
        .search-container input[type="text"]:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 8px rgba(78, 115, 223, 0.4);
        }

        .search-container input[type="text"]::placeholder {
            color: #aaa;
            font-style: italic;
        }

        /* Estilo do ícone de pesquisa */
        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #999;
        }
    </style>
</head>

<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="container">
                <h4 class="title">Ordens de Serviço Cadastradas</h4>
                <!-- Campo de Busca -->
                <div class="search-container">
                    <input type="text" name="search" id="searchInput" placeholder="Buscar por descrição..." value="<?php echo htmlspecialchars($searchTerm); ?>" onkeyup="searchData()" />
                </div>
                <div class="button-container">
                    <button onclick="window.location.href='nova_os.php'">Nova OS</button>
                    <button onclick="window.location.href='finalizadas_os.php'">Finalizadas</button>
                </div>

                <!-- Tabela de resultados -->
                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Equipamento</th>
                            <th>Secretaria</th>
                            <th>Setor</th>
                            <th>Responsável</th>
                            <th>Número Série</th>
                            <th>Número OS</th>
                            <th>Solicitante</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>Descrição</th>
                            <th>Data de Criação</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <?php
                        if (!empty($ordens_servico)) {
                            foreach ($ordens_servico as $item) {
                                echo "<tr onclick=\"preencherFormulario({$item['id']}, '{$item['descricao']}')\">";
                                echo "<td>{$item['categoria']}</td>";
                                echo "<td>{$item['equipamento']}</td>";
                                echo "<td>{$item['secretaria']}</td>";
                                echo "<td>{$item['setor']}</td>";
                                echo "<td>{$item['responsavel']}</td>";
                                echo "<td>{$item['numero_serie']}</td>";
                                echo "<td>{$item['numero_os']}</td>";
                                echo "<td>{$item['solicitante']}</td>";
                                echo "<td>{$item['prioridade']}</td>";
                                echo "<td>{$item['condicao']}</td>";
                                echo "<td>{$item['descricao']}</td>";
                                // Formatar data para mostrar apenas dia, mês e ano
                                $data_criacao = date("d/m/Y", strtotime($item['data_criacao']));
                                echo "<td>{$data_criacao}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>Nenhuma ordem de serviço encontrada.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="pagination-container">
                    <?php
                    // Exibir a paginação
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href=\"?page={$i}&search={$searchTerm}\" class=\"pagination-btn\">{$i}</a>";
                    }
                    ?>
                </div>
                
                <div class="form-container">
                    <h5>Registrar Número da OS</h5>
                    <br>
                    <label for="descricaoProblema">Descrição do Problema</label>
                    <textarea id="descricaoProblema" readonly placeholder="Descrição do problema selecionado"></textarea>

                    <label for="numeroOs">Número da OS</label>
                    <input type="text" id="numeroOs" placeholder="Digite o número da OS" />

                    <label for="statusOs">Alteração dos Status</label>
                    <select id="statusOs">
                        <option value="pendente">Pendente</option>
                        <option value="em andamento">Em Andamento</option>
                        <option value="finalizado">Finalizado</option>
                    </select>


                    <div class="button-container">
                        <button onclick="registrarNumeroOS()">Registrar</button>
                    </div>
                </div>

                <div id="statusMessage"></div>
            </div>
        </section>
    </div>

    <input type="hidden" id="idChamado" />

    <script>
    let currentPage = 1;
    const itemsPerPage = 6;
    
    function searchData() {
    const searchTerm = document.getElementById('searchInput').value;
    const page = currentPage; // Usar a página atual para a pesquisa
    const xhr = new XMLHttpRequest();

    // Alterar a URL para garantir que a busca seja feita apenas pelo número de série, número da OS ou responsável
    xhr.open('GET', 'search_chamados_os_andamento.php?search=' + searchTerm + '&page=' + page, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            // Atualizar os resultados da tabela
            const resultsTable = document.getElementById('resultsTable');
            resultsTable.innerHTML = response.html;

            // Atualizar a paginação
            const paginationContainer = document.querySelector('.pagination-container');
            paginationContainer.innerHTML = response.pagination;
        }
    };

    xhr.send();
}


    // Função para preencher o formulário com os dados da OS
    function preencherFormulario(id, descricao, numeroOs) {
        document.getElementById('descricaoProblema').value = descricao;
        document.getElementById('numeroOs').value = numeroOs;  // Preenche o campo com o número da OS
        document.getElementById('idChamado').value = id; // Define o ID do chamado
    }

    // Função para registrar o número da OS
    function registrarNumeroOS() {
    const numeroOs = document.getElementById('numeroOs').value;
    const descricaoProblema = document.getElementById('descricaoProblema').value;
    const condicao = document.getElementById('statusOs').value;
    const id = document.getElementById('idChamado').value;

    if (numeroOs === '') {
        alert('Por favor, preencha o número da OS.');
        return;
    }

    const data = {
        numero_os: numeroOs,
        id: id,
        status: condicao
    };

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_os_andamento.php', true); // Aqui o nome do arquivo atualizado
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);

            if (response.success) {
                document.getElementById('statusMessage').innerText = 'OS registrada com sucesso!';
                document.getElementById('numeroOs').value = '';
                document.getElementById('descricaoProblema').value = '';
                document.getElementById('statusOs').value = 'pendente';
            } else {
                document.getElementById('statusMessage').innerText = response.error;
            }
        }
    };

    xhr.send(JSON.stringify(data));
}
    </script>

</body>
</html>
