<?php
// Iniciar a sessão, se necessário
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectar ao banco de dados
include('db_connection.php'); // Inclua a conexão com o banco de dados

// Obter os parâmetros da requisição AJAX
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$currentFilter = isset($_GET['condicao']) ? $_GET['condicao'] : ''; // Filtro de condição

$offset = ($page - 1) * $limit;

// Consultar os dados filtrados
$sql = "SELECT * FROM ordens_servico WHERE 
            (numero_serie LIKE '%$searchTerm%' OR 
             numero_os LIKE '%$searchTerm%' OR 
             responsavel LIKE '%$searchTerm%')";

// Adicionar filtro de condição, se aplicável
if ($currentFilter != '') {
    $sql .= " AND condicao = '$currentFilter'";
}

$sql .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Criar a resposta em HTML para a tabela
$html = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr onclick=\"selectEquipment('{$row['categoria']}', '{$row['equipamento']}', '{$row['secretaria']}', '{$row['setor']}', '{$row['responsavel']}', '{$row['numero_serie']}', '{$row['motivo_os']}')\">";
        $html .= "<td>{$row['categoria']}</td>";
        $html .= "<td>{$row['equipamento']}</td>";
        $html .= "<td>{$row['secretaria']}</td>";
        $html .= "<td>{$row['setor']}</td>";
        $html .= "<td>{$row['responsavel']}</td>";
        $html .= "<td>{$row['numero_serie']}</td>";
        $html .= "</tr>";
    }
} else {
    $html = "<tr><td colspan='6'>Nenhum resultado encontrado.</td></tr>";
}

// Calcular o número total de páginas
$totalSql = "SELECT COUNT(*) AS total FROM ordens_servico WHERE 
             (numero_serie LIKE '%$searchTerm%' OR 
              numero_os LIKE '%$searchTerm%' OR 
              responsavel LIKE '%$searchTerm%')";
if ($currentFilter != '') {
    $totalSql .= " AND condicao = '$currentFilter'";
}

$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

// Gerar a resposta de paginação
$pagination = '';
if ($totalPages > 1) {
    for ($i = 1; $i <= $totalPages; $i++) {
        $pagination .= "<button onclick='changePage($i)'>$i</button>";
    }
}

echo json_encode([
    'data' => $html,
    'totalPages' => $totalPages,
    'pagination' => $pagination
]);

$conn->close();
?>
