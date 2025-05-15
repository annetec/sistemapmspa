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
    <title>Consulta de Equipamentos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Reset e ajustes gerais */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            max-width: 95%;
            margin: 0 auto;
        }

        .title {
            text-align: center;
            font-size: 26px;
            margin-bottom: 20px;
            color: #333;
        }

        .table-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        /* Botão Imprimir */
        .print-button {
            background:rgb(12, 109, 148);
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .print-button:hover {
            background: #45a049;
        }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            color: #555;
        }

        th {
            background-color:rgb(53, 125, 219);
            font-weight: 600;
            font-size: 14px;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* Inputs e selects */
        .search-select, .search-input {
            width: 100%;
            padding: 6px 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f8f9fa;
            font-size: 13px;
        }

        /* Botões de Ação */
        .actions a {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            color: #fff;
            cursor: pointer;
        }

        /* Botão Consultar (para quando a categoria for "Roteador") */
        .edit-btn {
            background-color: #007bff; /* Azul */
            color: #fff;
        }

        .edit-btn:hover {
            background-color: #0056b3; /* Azul escuro */
        }

        /* Botão Editar */
        .edit-btn {
            background-color: #28a745; /* Verde */
            color: #fff;
        }

        .edit-btn:hover {
            background-color: #218838; /* Verde escuro */
        }

        /* Botão Excluir */
        .delete-btn {
            background-color: #dc3545; /* Vermelho */
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333; /* Vermelho escuro */
        }

        /* Responsivo */
        @media (max-width: 768px) {
            th, td {
                font-size: 12px;
            }
            .search-select, .search-input {
                font-size: 12px;
            }
        }
        /* Estilos adicionais para o dropdown */
        .dropdown {
            position: relative;
            top: 0px;  /* Ajuste a posição para ficar acima da tabela */
            left: 2.5%;
            transform: translateX(-50%);
            display: inline-block;
            z-index: 10;  /* Garante que o botão fique sobre outros elementos */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-content button:hover {
            background-color: #45a049;
        }


    </style>
</head>

<?php
include "DB_connection.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "equipamento"; 

$whereClauses = [];
$sql = "SELECT * FROM chamados WHERE 1=1 ";

if ($search) {
    $whereClauses[] = "($filter LIKE :search)";
}

if (!empty($whereClauses)) {
    $sql .= " AND " . implode(" AND ", $whereClauses);
}

$sql .= " ORDER BY data_cadastro DESC";

$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();
$chamados = $stmt->fetchAll();
?>

<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>
    <div class="body">
        <?php include "inc/nav.php"; ?>
        <section class="section-1">
            <div class="dashboard-container">
                <h4 class="title">Consulta de Equipamentos</h4>
                <div class="table-wrapper">
                    <table id="tabelaEquipamentos">
                        <thead>
                        <div class="dropdown">
                            <button class="print-button">
                                <i class="fa fa-download"></i> Exportar
                            </button>
                            <div class="dropdown-content">
                                <button onclick="exportToJSON()">
                                    <i class="fa fa-file-code-o"></i> Exportar JSON
                                </button>
                                <button onclick="exportToXML()">
                                    <i class="fa fa-file-text-o"></i> Exportar XML
                                </button>
                            </div>
                        </div>
                            <button onclick="printFilteredTable()" class="print-button">
                                <i class="fa fa-print"></i> Imprimir
                            </button>
                            <tr>
                                <th>
                                    <select id="searchCategoria" class="search-select" onchange="filterTable()">
                                        <option value="">Categoria...</option>
                                        <option value="Computador">Computador</option>
                                        <option value="Notebook">Notebook</option>
                                        <option value="Impressora">Impressora</option>
                                        <option value="Monitor">Monitor</option>
                                        <option value="Nobreak">Nobreak</option>
                                        <option value="DockStation">DockStation</option>
                                        <option value="Roteador">Roteador</option>
                                        <option value="Outros">Outros</option>
                                    </select>
                                </th>
                                <th><input type="text" id="searchEquipamento" class="search-input" placeholder="Buscar Equipamento" onkeyup="filterTable()"></th>
                                <th>
                                    <select id="searchSecretaria" class="search-select" onchange="filterTable()">
                                        <option value="">Secretaria...</option>
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
                                </th>
                                <th><input type="text" id="searchSetor" class="search-input" placeholder="Buscar Setor" onkeyup="filterTable()"></th>
                                <th><input type="text" id="searchResponsavel" class="search-input" placeholder="Buscar Responsável" onkeyup="filterTable()"></th>
                                <?php if ($filter == "roteador"): ?>
                                    <th><input type="text" id="searchSSID" class="search-input" placeholder="Buscar SSID" onkeyup="filterTable()"></th>
                                    <th><input type="text" id="searchIPWAN" class="search-input" placeholder="Buscar IP DA WAN" onkeyup="filterTable()"></th>
                                <?php else: ?>
                                    <th><input type="text" id="searchNumerodeserie" class="search-input" placeholder="Buscar Nº de Série" onkeyup="filterTable()"></th>
                                    <th><input type="text" id="searchComputador" class="search-input" placeholder="Nome do Computador" onkeyup="filterTable()"></th>
                                    <th><input type="text" id="searchIP" class="search-input" placeholder="Numero do IP" onkeyup="filterTable()"></th>
                                <?php endif; ?>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="resultsTable">
                            <?php foreach ($chamados as $chamado) : ?>
                            <tr>
                                <td><?= htmlspecialchars($chamado['categoria']); ?></td>
                                <td><?= htmlspecialchars($chamado['equipamento']); ?></td>
                                <td><?= htmlspecialchars($chamado['secretaria']); ?></td>
                                <td><?= htmlspecialchars($chamado['setor']); ?></td>
                                <td><?= htmlspecialchars($chamado['responsavel']); ?></td>
                                <td><?= htmlspecialchars($chamado['numero_serie']); ?></td>

                                <?php if ($filter == "roteador"): ?>
                                    <td><?= htmlspecialchars($chamado['ssid']); ?></td>
                                    <td><?= htmlspecialchars($chamado['ip_wan']); ?></td>
                                <?php else: ?>
                                    <td><?= htmlspecialchars($chamado['nome_computador']); ?></td>
                                    <td><?= htmlspecialchars($chamado['ip']); ?></td>
                                <?php endif; ?>
                                <td class="actions">
                                    <?php if ($chamado['categoria'] == 'Roteador'): ?>
                                        <a href="editar_roteador.php?id=<?= $chamado['id']; ?>" class="edit-btn">Consultar</a>
                                    <?php else: ?>
                                        <a href="editar.php?id=<?= $chamado['id']; ?>" class="edit-btn">Editar</a>
                                    <?php endif; ?>

                                    <a href="excluir.php?id=<?= $chamado['id']; ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este item?');">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="footer">
        <p>Gerado por Sistema de Suporte | Versão 1.0</p>
    </div>
    <div class="datetime">
        <p><?= date('d/m/Y H:i:s'); ?></p>
    </div>
    <script>
        function filterTable() {
            const inputs = document.querySelectorAll('.search-input, .search-select');
            const rows = document.querySelectorAll('#resultsTable tr');
            
            rows.forEach(row => {
                let showRow = true;

                // Verifica cada input (caixa de texto ou select)
                inputs.forEach((input, index) => {
                    const cell = row.cells[index];
                    
                    if (input.tagName.toLowerCase() === 'select') {
                        if (input.value && !cell.textContent.toLowerCase().includes(input.value.toLowerCase())) {
                            showRow = false;
                        }
                    } else if (input.tagName.toLowerCase() === 'input') {
                        if (cell && !cell.textContent.toLowerCase().includes(input.value.toLowerCase())) {
                            showRow = false;
                        }
                    }
                });

                row.style.display = showRow ? '' : 'none';
            });
        }

        function printFilteredTable() {
            let table = document.getElementById('tabelaEquipamentos');
            let rows = document.querySelectorAll('#resultsTable tr');
            let printWindow = window.open('', '', 'width=900,height=700');
            
            printWindow.document.write('<html><head><title>Impressão</title>');
            printWindow.document.write('<style>');
            printWindow.document.write(`@page { size: landscape; } body { font-family: Arial, sans-serif; margin: 20px; color: #333; font-size: 12px; text-align: center; } h2 { font-size: 14px; margin-bottom: 10px; } table { width: 100%; border-collapse: collapse; font-size: 12px; } th, td { padding: 6px; text-align: left; border: 1px solid #ddd; } th { background-color: #007bff; color: #fff; } tr:nth-child(even) { background-color: #f9f9f9; } tr:hover { background-color: #e9e9e9; }</style>`);
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Consulta de Equipamentos</h2>');
            printWindow.document.write('<table><thead><tr><th>Categoria</th><th>Equipamento</th><th>Secretaria</th><th>Setor</th><th>Responsável</th><th>Nº Série</th><th>Computador</th><th>IP</th><th>Ações</th></tr></thead><tbody>');

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    printWindow.document.write('<tr>');
                    for (let cell of row.cells) {
                        printWindow.document.write(`<td>${cell.textContent}</td>`);
                    }
                    printWindow.document.write('</tr>');
                }
            });

            printWindow.document.write('</tbody></table>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
        // Função para exportar os dados filtrados para JSON
        function exportToJSON() {
    const rows = document.querySelectorAll('#resultsTable tr');
    const data = [];
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const rowData = {};
            const cells = row.cells;

            rowData.categoria = cells[0].textContent;
            rowData.equipamento = cells[1].textContent;
            rowData.secretaria = cells[2].textContent;
            rowData.setor = cells[3].textContent;
            rowData.responsavel = cells[4].textContent;
            rowData.numero_serie = cells[5].textContent;

            if (cells.length > 7) {
                rowData.ssid = cells[6].textContent;
                rowData.ip_wan = cells[7].textContent;
            } else {
                rowData.nome_computador = cells[6].textContent;
                rowData.ip = cells[7].textContent;
            }

            data.push(rowData);
        }
    });

    const jsonData = JSON.stringify(data, null, 2);

    const blob = new Blob([jsonData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'equipamentos.json';
    link.click();
    URL.revokeObjectURL(url);
}


    // Função para exportar os dados filtrados para XML
    function exportToXML() {
        const rows = document.querySelectorAll('#resultsTable tr');
        let xmlData = '<?xml version="1.0" encoding="UTF-8"?>\n<equipamentos>\n';

        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.cells;
                let xmlRow = '  <equipamento>\n';

                xmlRow += `    <categoria>${cells[0].textContent}</categoria>\n`;
                xmlRow += `    <equipamento_nome>${cells[1].textContent}</equipamento_nome>\n`;
                xmlRow += `    <secretaria>${cells[2].textContent}</secretaria>\n`;
                xmlRow += `    <setor>${cells[3].textContent}</setor>\n`;
                xmlRow += `    <responsavel>${cells[4].textContent}</responsavel>\n`;
                xmlRow += `    <numero_serie>${cells[5].textContent}</numero_serie>\n`;

                // Se for um roteador, adiciona as colunas extras
                if (cells.length > 7) {
                    xmlRow += `    <ssid>${cells[6].textContent}</ssid>\n`;
                    xmlRow += `    <ip_wan>${cells[7].textContent}</ip_wan>\n`;
                } else {
                    xmlRow += `    <nome_computador>${cells[6].textContent}</nome_computador>\n`;
                    xmlRow += `    <ip>${cells[7].textContent}</ip>\n`;
                }

                xmlRow += '  </equipamento>\n';
                xmlData += xmlRow;
            }
        });

        xmlData += '</equipamentos>';

        // Cria um link temporário para o download do arquivo XML
        const blob = new Blob([xmlData], { type: 'application/xml' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'equipamentos.xml';
        link.click();
        URL.revokeObjectURL(url);
    }
    </script>
</body>
</html>
