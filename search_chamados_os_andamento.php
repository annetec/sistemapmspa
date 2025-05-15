<?php
// Verifica se a sessão foi iniciada
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

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Número de itens por página
$offset = ($page - 1) * $limit;

// Consulta para buscar dados filtrados
$sql = "SELECT * FROM ordens_servico WHERE 
            (numero_serie LIKE '%$searchTerm%' OR 
             numero_os LIKE '%$searchTerm%' OR 
             responsavel LIKE '%$searchTerm%')
            LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Verificar se há resultados
if ($result->num_rows > 0) {
    // Criar o array de ordens_servico
    $ordens_servico = [];
    while ($row = $result->fetch_assoc()) {
        $ordens_servico[] = $row;
    }

    // Gerar HTML para os resultados
    $html = '';
    foreach ($ordens_servico as $item) {
        $html .= "<tr onclick=\"preencherFormulario({$item['id']}, '{$item['descricao']}')\">";
        $html .= "<td>{$item['categoria']}</td>";
        $html .= "<td>{$item['equipamento']}</td>";
        $html .= "<td>{$item['secretaria']}</td>";
        $html .= "<td>{$item['setor']}</td>";
        $html .= "<td>{$item['responsavel']}</td>";
        $html .= "<td>{$item['numero_serie']}</td>";
        $html .= "<td>{$item['numero_os']}</td>";
        $html .= "<td>{$item['solicitante']}</td>";
        $html .= "<td>{$item['prioridade']}</td>";
        $html .= "<td>{$item['condicao']}</td>";
        $html .= "<td>{$item['descricao']}</td>";
        $data_criacao = date("d/m/Y", strtotime($item['data_criacao']));
        $html .= "<td>{$data_criacao}</td>";
        $html .= "</tr>";
    }

    // Pegar o total de itens para calcular o número de páginas
    $sql_total = "SELECT COUNT(*) AS total FROM ordens_servico WHERE 
                  (numero_serie LIKE '%$searchTerm%' OR 
                   numero_os LIKE '%$searchTerm%' OR 
                   responsavel LIKE '%$searchTerm%')";
    $total_result = $conn->query($sql_total);
    $total_row = $total_result->fetch_assoc();
    $total_items = $total_row['total'];
    $total_pages = ceil($total_items / $limit);

    // Gerar a paginação
    $pagination = '';
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination .= "<a href=\"?page={$i}&search={$searchTerm}\" class=\"pagination-btn\">{$i}</a>";
    }

    // Retornar a resposta em JSON
    echo json_encode([
        'html' => $html,
        'pagination' => $pagination
    ]);
} else {
    // Caso não haja resultados
    echo json_encode([
        'html' => "<tr><td colspan='12'>Nenhuma ordem de serviço encontrada.</td></tr>",
        'pagination' => ''
    ]);
}

$conn->close(); // Fechar a conexão com o banco
?>