<?php
// Iniciar a sessão
session_start();

// Definir as configurações do banco de dados
$servername = "localhost";
$username = "alice";
$password = "cpd@sorento";
$dbname = "task_management_db";

try {
    // Criando a conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Definir o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Caso haja erro na conexão, retorna uma mensagem de erro
    echo json_encode(['success' => false, 'error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Verificar se os dados foram recebidos via POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados foram recebidos corretamente
if (!isset($data['motivoOS']) || !isset($data['solicitante']) || !isset($data['prioridade']) || !isset($data['condicao']) || !isset($data['descricao']) || !isset($data['categoria']) || !isset($data['equipamento']) || !isset($data['secretaria']) || !isset($data['setor']) || !isset($data['responsavel']) || !isset($data['numeroSerie']) || !isset($data['dataAbertura'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

// Extrair dados recebidos
$motivo_os = $data['motivoOS'];
$solicitante = $data['solicitante'];
$prioridade = $data['prioridade'];
$condicao = $data['condicao'];  // Condição (status) da OS
$descricao = $data['descricao'];
$categoria = $data['categoria'];
$equipamento = $data['equipamento'];
$secretaria = $data['secretaria'];
$setor = $data['setor'];
$responsavel = $data['responsavel'];
$numero_serie = $data['numeroSerie'];
$data_abertura = $data['dataAbertura'];

// Converter a data de abertura do formato d/m/Y para Y-m-d
$data_abertura = DateTime::createFromFormat('d/m/Y', $data_abertura)->format('Y-m-d');

// Inserir os dados na tabela ordens_servico, com numero_os como NULL
$sql = "INSERT INTO ordens_servico (numero_os, motivo_os, solicitante, prioridade, condicao, descricao, categoria, equipamento, secretaria, setor, responsavel, numero_serie, data_abertura)
        VALUES (NULL, :motivo_os, :solicitante, :prioridade, :condicao, :descricao, :categoria, :equipamento, :secretaria, :setor, :responsavel, :numero_serie, :data_abertura)";

// Preparando a consulta
$stmt = $pdo->prepare($sql);

// Associar os parâmetros da consulta com os dados recebidos
$stmt->bindParam(':motivo_os', $motivo_os);
$stmt->bindParam(':solicitante', $solicitante);
$stmt->bindParam(':prioridade', $prioridade);
$stmt->bindParam(':condicao', $condicao);  // Bind para o status da OS
$stmt->bindParam(':descricao', $descricao);
$stmt->bindParam(':categoria', $categoria);
$stmt->bindParam(':equipamento', $equipamento);
$stmt->bindParam(':secretaria', $secretaria);
$stmt->bindParam(':setor', $setor);
$stmt->bindParam(':responsavel', $responsavel);
$stmt->bindParam(':numero_serie', $numero_serie);
$stmt->bindParam(':data_abertura', $data_abertura);

// Tentar executar a consulta
try {
    $stmt->execute();
    
    // Retorna sucesso
    echo json_encode(['success' => true, 'numero_os' => NULL]);

} catch (PDOException $e) {
    // Caso haja erro na execução da consulta, retorna um erro
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar a Ordem de Serviço: ' . $e->getMessage()]);
}
?>
