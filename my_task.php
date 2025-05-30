<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Interno</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 60%;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        h4 {
            text-align: center;
            color: #333;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        select.form-control {
            background-color: #fff;
        }
        .row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .col {
            flex: 1;
            min-width: 280px;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            transition: 0.3s;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <h4 class="title text-center">Novo Cadastro de Equipamento</h4>
            <div class="container">
                <form action="processa_cadastro.php" method="POST" class="shadow p-4 bg-light rounded">
                    <div class="row">
                        <div class="col form-group">
                            <label for="categoria">Categoria:</label>
                            <select id="categoria" name="categoria" class="form-control" required onchange="verificarRoteador()">
                                <option value="">Selecione...</option>
                                <option value="Computador">Computador</option>
                                <option value="Notebook">Notebook</option>
                                <option value="Impressora">Impressora</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Nobreak">Nobreak</option>
                                <option value="DockStation">DockStation</option>
                                <option value="Roteador">Roteador</option> <!-- Nova opção -->
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <p></p>
                        <div id="campoEquipamento" class="col form-group">
                        <label for="equipamento">Equipamento:</label>
                        <input type="text" id="equipamento" name="equipamento" class="form-control" required>
                     </div>
                    </div>
                    <script>
                        function verificarRoteador() {
                        var categoria = document.getElementById("categoria").value;
                        var camposRoteador = document.getElementById("camposRoteador");
                        var campoEquipamento = document.getElementById("campoEquipamento");
                        var campoNumeroSerie = document.getElementById("campoNumeroSerie");
                        var campoNomeComputador = document.getElementById("campoNomeComputador");
                        var campoEnderecoIP = document.getElementById("campoEnderecoIP");
                        var campoObservacao = document.getElementById("campoObservacao");

                        if (categoria === "Roteador") {
                            camposRoteador.style.display = "block";
                            campoEquipamento.style.display = "none";
                            campoNumeroSerie.style.display = "none";
                            campoNomeComputador.style.display = "none";
                            campoEnderecoIP.style.display = "none";
                            campoObservacao.style.display = "none";

                            document.getElementById("equipamento").disabled = true;
                            document.getElementById("numero_serie").disabled = true;
                            document.getElementById("nome_computador").disabled = true;
                            document.getElementById("ip").disabled = true;
                            document.getElementById("observacao").disabled = true;
                        } else {
                            camposRoteador.style.display = "none";
                            campoEquipamento.style.display = "block";
                            campoNumeroSerie.style.display = "block";
                            campoNomeComputador.style.display = "block";
                            campoEnderecoIP.style.display = "block";
                            campoObservacao.style.display = "block";

                            document.getElementById("equipamento").disabled = false;
                            document.getElementById("numero_serie").disabled = false;
                            document.getElementById("nome_computador").disabled = false;
                            document.getElementById("ip").disabled = false;
                            document.getElementById("observacao").disabled = false;
                        }
                    }


                        </script>    
                    <br>                
                    <div class="row">
                    <div id="campoNumeroSerie" class="col form-group">
                        <label for="numero_serie">Número de Série:</label>
                        <input type="text" id="numero_serie" name="numero_serie" class="form-control" required>
                    </div>
                        <div class="col form-group">
                            <label for="secretaria">Secretaria:</label>
                            <select id="secretaria" name="secretaria" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option value="Administração">Administração</option>
                                <option value="Agricultura">Agricultura</option>
                                <option value="Assistência Social">Assistência Social</option>
                                <option value="Controladoria">Controladoria</option>
                                <option value="Cultura">Cultura</option>
                                <option value="Desenvolvimento Econômico">Desenvolvimento Econômico</option>
                                <option value="Educação">Educação</option>
                                <option value="Esporte e Lazer">Esporte e Lazer</option>
                                <option value="Fazenda">Fazenda</option>
                                <option value="Governo">Governo</option>
                                <option value="Licitações e Contratos">Licitações e Contratos</option>
                                <option value="Meio Ambiente">Meio Ambiente</option>
                                <option value="Obras">Obras</option>
                                <option value="Ordem Pública">Ordem Pública</option>
                                <option value="Planejamento e Gestão">Planejamento e Gestão</option>
                                <option value="Previspa">Previspa</option>
                                <option value="Procon">Procon</option>
                                <option value="Procuradoria Geral">Procuradoria Geral</option>
                                <option value="Saúde">Saúde</option>
                                <option value="Serviços Públicos">Serviços Públicos</option>
                                <option value="Turismo">Turismo</option>
                                <option value="Segurança">Segurança</option>
                            </select>
                        </div>
                        <div class="col form-group">
                            <label for="setor">Setor:</label>
                            <input type="text" id="setor" name="setor" class="form-control" required>
                        </div>
                        <div class="col form-group">
                            <label for="responsavel">Responsável:</label>
                            <input type="text" id="responsavel" name="responsavel" class="form-control" required>
                        </div>
                        <div id="campoEquipamento" class="col form-group">
                        <label for="equipamento">Equipamento / Modelo :</label>
                        <input type="text" id="equipamento" name="equipamento" class="form-control" required>
                        </div>
                        <div class="row">                  
                            <div id="campoNomeComputador" class="col form-group">
                                <label for="nome_computador">Nome do Computador:</label>
                                <input type="text" id="nome_computador" name="nome_computador" class="form-control" required>
                            </div>
                            <div id="campoEnderecoIP" class="col form-group">
                                <label for="ip">Endereço IP:</label>
                                <input type="text" id="ip" name="ip" class="form-control" required>
                            </div>
                        </div>
                        <div id="campoObservacao" class="form-group">
                            <label for="observacao">Observação:</label>
                            <textarea id="observacao" name="observacao" class="form-control" rows="4"></textarea>
                        </div>
                                         <!-- Campos adicionais para Roteador (inicialmente ocultos) -->
                     <div id="camposRoteador" style="display: none;">
                        <div class="row">
                        <h4 class="title text-center">Dados do Roteador:</h4><br>
                            <div class="col form-group">
                                <label for="ssid">SSID:</label>
                                <input type="text" id="ssid" name="ssid" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="senha">Senha:</label>
                                <input type="text" id="senha" name="senha" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="ip_wan">IP da WAN:</label>
                                <input type="text" id="ip_wan" name="ip_wan" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="usuario_admin">usuario administrador:</label>
                                <input type="text" id="usuario_admin" name="usuario_admin" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="senha_admin">Senha:</label>
                                <input type="text" id="senha_admin" name="senha_admin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                    </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
