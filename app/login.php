<?php
// Habilitar a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sessão
session_start();

// Incluir a conexão com o banco de dados
include "../DB_connection.php";

// Verificar se os campos de login foram preenchidos
if (isset($_POST['user_name']) && isset($_POST['password'])) {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    // Alterar a consulta SQL para refletir a estrutura da tabela
    try {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bindParam(1, $user_name);
        $stmt->execute();
        
        // Verificar se o usuário existe no banco de dados
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar se a senha está correta
            if (password_verify($password, $user['password'])) {
                // Login bem-sucedido, armazenar os dados na sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];  // Garantir que o papel do usuário está armazenado

                // Redirecionar para a página principal (index.php ou a página que você deseja)
                header("Location: ../index.php");
                exit();
            } else {
                // Senha incorreta
                header("Location: ../login.php?error=Senha incorreta");
                exit();
            }
        } else {
            // Usuário não encontrado
            header("Location: ../login.php?error=Usuário não encontrado");
            exit();
        }
    } catch (PDOException $e) {
        // Erro ao executar a consulta SQL
        echo "Erro ao consultar o banco de dados: " . $e->getMessage();
        exit();
    }
} else {
    // Se os campos não foram preenchidos
    header("Location: ../login.php?error=Preencha todos os campos");
    exit();
}
?>
