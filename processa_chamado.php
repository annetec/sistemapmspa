<?php
// Incluir o arquivo de configuração do banco de dados
include('config_chamados.php');

// Verificar se os dados foram enviados pelo formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $numero_serie = $_POST['numero_serie'];
    $data_chamado = $_POST['data_chamado'];
    $tipo_chamado = $_POST['tipo_chamado'];
    $descricao_problema = $_POST['descricao_chamado']; // Alterado para 'descricao_problema'
    $tecnico = $_POST['tecnico'];
    $comentario = $_POST['comentario']; // Se tiver um campo para comentário
    $usuario = $_POST['usuario']; // Caso tenha um campo de usuário
    $patrimonio = $_POST['patrimonio']; // Caso tenha um campo de patrimônio
    $status = $_POST['status']; // Caso tenha um campo de status

    // A coluna 'data_criacao' pode ser preenchida automaticamente com a data e hora atual (se necessário)
    $data_criacao = date("Y-m-d H:i:s");

    // Construa a consulta SQL para inserir os dados na tabela
    $sql = "INSERT INTO cadastro_chamados 
            (data_chamado, tipo_chamado, descricao_problema, tecnico, comentario, numero_serie, usuario, patrimonio, status, data_criacao) 
            VALUES ('$data_chamado', '$tipo_chamado', '$descricao_problema', '$tecnico', '$comentario', '$numero_serie', '$usuario', '$patrimonio', '$status', '$data_criacao')";

    // Execute a consulta
    if ($conn->query($sql) === TRUE) {
        echo "Chamado registrado com sucesso!";
    } else {
        echo "Erro ao registrar chamado: " . $conn->error;
    }
}
?>
