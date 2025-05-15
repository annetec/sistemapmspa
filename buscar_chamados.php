<?php
// Incluir o arquivo de configuração do banco de dados
include('config_chamados.php');

// Verificar se há pesquisa
if (isset($_GET['nserie'])) {
    $nserie = $_GET['nserie'];

    // Definir o número da página (caso não tenha página, inicia com 1)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit; // Calcular o deslocamento para a página atual

    // Consultar os chamados com base no número de série e limitar resultados
    $sql = "SELECT * FROM chamados WHERE 
            numero_serie LIKE '%$nserie%' OR
            secretaria LIKE '%$nserie%' OR
            responsavel LIKE '%$nserie%' OR
            setor LIKE '%$nserie%'
            LIMIT $limit OFFSET $offset";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="styled-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Equipamento</th>';
        echo '<th>Categoria</th>';
        echo '<th>Número de Série</th>';
        echo '<th>Secretaria</th>';
        echo '<th>Responsável</th>';
        echo '<th>Setor</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr class="clickable-row">';
            echo '<td>' . htmlspecialchars($row['equipamento']) . '</td>';
            echo '<td>' . htmlspecialchars($row['categoria']) . '</td>';
            echo '<td><a href="javascript:void(0);" onclick="selectNumeroSerie(\'' . htmlspecialchars($row['numero_serie']) . '\')">' . htmlspecialchars($row['numero_serie']) . '</a></td>';
            echo '<td>' . htmlspecialchars($row['secretaria']) . '</td>';
            echo '<td>' . htmlspecialchars($row['responsavel']) . '</td>';
            echo '<td>' . htmlspecialchars($row['setor']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Nenhum chamado encontrado.</p>';
    }

    // Contar o total de resultados para calcular a paginação
    $sql_total = "SELECT COUNT(*) AS total FROM chamados WHERE 
                  numero_serie LIKE '%$nserie%' OR
                  secretaria LIKE '%$nserie%' OR
                  responsavel LIKE '%$nserie%' OR
                  setor LIKE '%$nserie%'";
    $result_total = $conn->query($sql_total);
    $total_row = $result_total->fetch_assoc();
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $limit);

    // Exibir links de paginação
    if ($total_pages > 1) {
        echo '<div class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?nserie=$nserie&page=$i'>$i</a> ";
        }
        echo '</div>';
    }

} else {
    echo '<p>Por favor, insira um número de série para buscar.</p>';
}
?>

<style>
    /* Estilos aprimorados para a tabela */
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    /* Cabeçalho da tabela com fundo azul */
    .styled-table thead {
        background-color: #007bff; /* Cor de fundo azul */
        color: white;
    }

    .styled-table th, .styled-table td {
        padding: 12px;
        text-align: left;
    }

    .styled-table th {
        font-size: 16px;
        font-weight: bold;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #ddd;
    }

    .styled-table tbody tr:hover {
        background-color: #f2f2f2;
        cursor: pointer;
    }

    .styled-table td {
        font-size: 14px;
        color: #555;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f9f9f9;
    }

    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #007bff;
    }

    /* Estilo para a navegação de página */
    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        margin: 0 10px;
        padding: 8px 15px;
        text-decoration: none;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
    }

    .pagination a:hover {
        background-color: #0056b3;
    }

    /* Estilo para a label do número de série */
    #numero-serie-selecionado {
        margin-top: 20px;
        font-size: 16px;
        font-weight: bold;
    }
</style>
