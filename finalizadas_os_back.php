<?php
$servername = "localhost";
$username = "alice";
$password = "cpd@sorento";
$dbname = "task_management_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $sql = "SELECT * FROM ordens_servico 
            WHERE condicao = 'finalizada' AND (
                categoria LIKE ? OR
                equipamento LIKE ? OR
                secretaria LIKE ? OR
                setor LIKE ? OR
                responsavel LIKE ? OR
                numero_serie LIKE ? OR
                numero_os LIKE ? OR
                solicitante LIKE ? OR
                prioridade LIKE ? OR
                descricao LIKE ?
            )";
    $likeSearch = '%' . $search . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssss',
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch,
        $likeSearch
    );
} else {
    $sql = "SELECT * FROM ordens_servico WHERE condicao = 'finalizada'";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($item = $result->fetch_assoc()) {
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
        echo "<td>" . date("d/m/Y", strtotime($item['data_criacao'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12'>Nenhuma ordem de serviço finalizada encontrada.</td></tr>";
}

$conn->close();
?>
