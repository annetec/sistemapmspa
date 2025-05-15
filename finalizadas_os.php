<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "alice";
$password = "cpd@sorento";
$dbname = "task_management_db";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Pegar o número da página atual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Número de itens por página
$offset = ($page - 1) * $limit;

// Consulta para pegar os dados da tabela ordens_servico com status "finalizado" e paginação
// Consulta para pegar todos os dados com status "finalizada"
$sql = "SELECT * FROM ordens_servico WHERE condicao = ?";
$stmt = $conn->prepare($sql);
$status = 'finalizada';
$stmt->bind_param('s', $status);
$stmt->execute();
$result = $stmt->get_result();

// Criar o array de ordens_servico
$ordens_servico = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ordens_servico[] = $row;
    }
}


// Pegar o total de itens para calcular o número de páginas
$sql_total = "SELECT COUNT(*) AS total FROM ordens_servico WHERE condicao = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param('s', $status);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

$conn->close(); // Fechar a conexão com o banco
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Serviço Finalizadas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos básicos (mesmo do código anterior) */
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
        .search-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    #search {
        width: 50%;
        padding: 12px 20px;
        font-size: 16px;
        border: 2px solid #007bff;
        border-radius: 25px;
        outline: none;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    #search:focus {
        border-color: #0056b3;
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);
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
               
                <h4 class="title">Ordens de Serviço Finalizadas</h4>
                               
                <!-- Botões para "Nova OS" e "Finalizadas" -->
                <div class="button-container">
                    <button onclick="window.location.href='nova_os.php'">Nova OS</button>
                    <button onclick="window.location.href='andamento_os.php'">Em andamento</button>
                </div>
                <br>
                <div class="search-container">
                    <input type="text" id="search" placeholder="🔍 Pesquisar OS..." onkeyup="searchOS()">
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
                            <th>Condicao</th>
                            <th>Descrição</th>
                            <th>Data de Criação</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <?php
                        if (!empty($ordens_servico)) {
                            foreach ($ordens_servico as $item) {
                                echo "<tr>";
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
                                // Formatar a data de criação
                                $data_criacao = date("d/m/Y", strtotime($item['data_criacao']));
                                echo "<td>{$data_criacao}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>Nenhuma ordem de serviço finalizada encontrada.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <!-- Paginação -->
                <div class="pagination" id="pagination">
                    <?php
                    if ($page > 1) {
                        echo "<a href='finalizadas_os.php?page=" . ($page - 1) . "'>Anterior</a>";
                    }
                    if ($page < $total_pages) {
                        echo "<a href='finalizadas_os.php?page=" . ($page + 1) . "'>Próxima Página</a>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
    <script>
    function searchOS() {
        let searchTerm = document.getElementById("search").value;

        $.ajax({
            url: "finalizadas_os_back.php", // Arquivo PHP para buscar os resultados
            type: "GET",
            data: { search: searchTerm },
            success: function(response) {
                $("#resultsTable").html(response); // Atualiza a tabela com os novos resultados
            }
        });
    }
</script>

    <div class="footer">
        <p>Direitos Reservados - T.I - PMSPA 2025</p>
    </div>
    <div class="datetime">
        <p><?= date('d/m/Y H:i:s'); ?></p>
    </div>
</body>
</html>
