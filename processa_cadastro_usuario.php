<?php
session_start();

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Credenciais do banco de dados
    $servername = "localhost";
    $username = "alice";
    $password = "cpd@sorento";
    $dbname = "task_management_db";

    // Conexão com o banco de dados
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }

    // Receber dados do formulário
    $full_name = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['login'];
    $password = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Atribuir "employee" ou "funcionario" como valor para o campo 'role'
    $role = $_POST['perfil'];

    // Validar se as senhas são iguais
    if ($password !== $confirmar_senha) {
        $_SESSION['error'] = "As senhas não coincidem.";
        header('Location: cadastro_usuario.php');
        exit();
    }

    // Validar o formato do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "O e-mail fornecido não é válido.";
        header('Location: cadastro_usuario.php');
        exit();
    }

    // Verificar se o login ou e-mail já existe no banco de dados
    $query = "SELECT * FROM users WHERE email = :email OR username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "O e-mail ou login já estão cadastrados.";
        header('Location: cadastro_usuario.php');
        exit();
    }

    // Inserir os dados no banco de dados
    $query = "INSERT INTO users (full_name, username, password, email, role) VALUES (:full_name, :username, :password, :email, :role)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT)); // Senha segura com hash
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);

    try {
        // Executar a inserção no banco de dados
        $stmt->execute();
        
        // Enviar o e-mail de confirmação para o usuário
        $subject = "Cadastro Realizado com Sucesso";
        $message = "Olá, $full_name!\n\nSeu cadastro foi realizado com sucesso no sistema interno da PMSPA.\n\nAtenciosamente,\nEquipe PMSPA";
        $headers = "From: no-reply@pmspa.com.br\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Enviar e-mail
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['success'] = "Usuário cadastrado com sucesso! E-mail de confirmação enviado.";
        } else {
            $_SESSION['error'] = "Erro ao enviar o e-mail de confirmação.";
        }

        // Redirecionar para página de login
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        // Caso ocorra algum erro na inserção
        $_SESSION['error'] = "Erro ao cadastrar usuário: " . $e->getMessage();
        header('Location: cadastro_usuario.php');
        exit();
    }
}
?>
