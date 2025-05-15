<?php
include "DB_connection.php"; // Inclui a conexão com o banco

// Verifica se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepara e executa a exclusão do registro na tabela chamados
    $sql = "DELETE FROM chamados WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    // Tenta executar a exclusão
    if ($stmt->execute()) {
        // Se a exclusão for bem-sucedida, redireciona de volta para a página de consulta
        header("Location: consulta.php"); // Altere "consulta.php" para o nome correto da sua página
        exit();
    } else {
        // Se a exclusão falhar, exibe uma mensagem de erro
        echo "Erro ao excluir o registro.";
    }
} else {
    // Se o ID não for encontrado, redireciona para a página de consulta
    header("Location: consulta.php");
    exit();
}
?>
