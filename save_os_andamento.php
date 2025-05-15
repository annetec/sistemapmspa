<?php
// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "alice";
$password = "cpd@sorento";
$dbname = "task_management_db";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber os dados via POST (enviados pelo AJAX)
$data = json_decode(file_get_contents("php://input"), true);

// Verificar se os dados estão corretos
if (isset($data['id'], $data['numero_os'], $data['status'])) {
    $id = $data['id'];
    $numero_os = $data['numero_os'];
    $status = $data['status'];

    // Consultar para garantir que a OS com esse ID existe (opcional)
    $sql_check = "SELECT * FROM ordens_servico WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Atualizar a OS
        $sql_update = "UPDATE ordens_servico SET numero_os = ?, condicao = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $numero_os, $status, $id);

        if ($stmt_update->execute()) {
            // Resposta de sucesso
            echo json_encode(["success" => true, "message" => "OS atualizada com sucesso."]);
        } else {
            // Resposta de erro caso a atualização falhe
            echo json_encode(["success" => false, "error" => "Erro ao atualizar a OS."]);
        }

        $stmt_update->close();
    } else {
        // Caso não encontre a OS com o ID
        echo json_encode(["success" => false, "error" => "OS não encontrada."]);
    }

    $stmt_check->close();
} else {
    // Caso os dados estejam incompletos ou errados
    echo json_encode(["success" => false, "error" => "Dados incompletos ou inválidos."]);
}

// Fechar a conexão
$conn->close();
?>
