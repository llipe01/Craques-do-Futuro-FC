<?php
// ARQUIVO: visualizar_cadastros.php (Versão Final Revisada)

$servername = "localhost:3306";
$username = "root";
$password = "Senai@118";
$dbname = "craques_do_futuro_fc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Lógica de DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM atletas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: visualizar_cadastros.php");
    exit();
}

// Busca os registros para exibir
$sql = "SELECT id, nome, data, DATE_FORMAT(data, '%d/%m/%Y') AS data_formatada, posicao FROM atletas ORDER BY nome ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atletas Cadastrados - Craques do Futuro</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <link rel="stylesheet" type="text/css" href="estilo.css">
    <link rel="stylesheet" type="text/css" href="estilo-visualizar.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <a class="navbar-brand" href="index.php">Craques do Futuro FC</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
            <li class="nav-item"><a class="nav-link" href="cadastro_atleta.php">Cadastro do Atleta</a></li>
            <li class="nav-item active"><a class="nav-link" href="visualizar_cadastros.php">Atletas</a></li>
        </ul>
    </div>
</nav>

<header class="header-visualizar">
    <h1>Nossos Atletas</h1>
    <p class="lead">Gerencie, edite e acompanhe os craques do futuro.</p>
</header>

<main class="table-container">
    <div class="container">
        <div class="card card-table">
            <div class="card-body">
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nome do Atleta</th>
                                    <th>Data de Nascimento</th>
                                    <th>Posição</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td id="nome-<?php echo $row['id']; ?>" data-original-value="<?php echo htmlspecialchars($row['nome']); ?>" onBlur="updateData(this, 'nome', <?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['nome']); ?></td>
                                        <td id="data-<?php echo $row['id']; ?>" data-original-date="<?php echo htmlspecialchars($row['data']); ?>"><?php echo htmlspecialchars($row['data_formatada']); ?></td>
                                        <td id="posicao-<?php echo $row['id']; ?>" data-original-value="<?php echo htmlspecialchars($row['posicao']); ?>" onBlur="updateData(this, 'posicao', <?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['posicao']); ?></td>
                                        <td class="text-center action-buttons">
                                            <button class="btn btn-outline-primary" title="Editar" onClick="enableEditing(<?php echo $row['id']; ?>)"><i class="fas fa-pencil-alt"></i></button>
                                            <button class="btn btn-outline-danger ml-2" title="Excluir" onClick="deleteRow(<?php echo $row['id']; ?>)"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center mb-0">Ainda não há atletas cadastrados.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="statusMessage" class="alert" role="alert" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1050; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>

<footer class="footer">© 2025 Craques do Futuro FC</footer>

<script>
    function enableEditing(id) {
        // Antes de habilitar uma nova edição, finaliza qualquer outra que esteja aberta para salvar os dados
        document.querySelectorAll('td[contentEditable="true"]').forEach(cell => cell.blur());
        const existingInput = document.querySelector('.editing-date-input');
        if (existingInput) {
            existingInput.blur();
        }

        // Habilita a edição para NOME e POSIÇÃO
        // Esta parte é a responsável por tornar o campo NOME editável
        ['nome-' + id, 'posicao-' + id].forEach(cellId => {
            const cell = document.getElementById(cellId);
            if (cell) {
                cell.setAttribute('data-original-value', cell.innerText);
                cell.contentEditable = true;
                cell.style.backgroundColor = '#fff3cd';
            }
        });

        // Habilita a edição para DATA (com o calendário)
        const dateCell = document.getElementById('data-' + id);
        if (dateCell && !dateCell.querySelector('input')) {
            const originalDate = dateCell.getAttribute('data-original-date');
            dateCell.innerHTML = `<input type="date" class="form-control form-control-sm editing-date-input" value="${originalDate}" 
                                         onBlur="updateData(this, 'data', ${id})" 
                                         onKeydown="if(event.key==='Enter'){this.blur()}">`;
        }
        
        // Foca no campo NOME para o usuário já poder digitar
        const nomeCell = document.getElementById('nome-' + id);
        if (nomeCell) {
            nomeCell.focus();
            // Truque para posicionar o cursor no final do texto
            const range = document.createRange();
            const sel = window.getSelection();
            range.selectNodeContents(nomeCell);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }

    function updateData(element, column, id) {
        let value;
        if (element.tagName.toLowerCase() !== 'input') {
            element.contentEditable = false;
            element.style.backgroundColor = '';
            value = element.innerText.trim();
        } else {
            value = element.value;
        }

        var formData = new FormData();
        formData.append('id', id);
        formData.append('column', column);
        formData.append('value', value);

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.innerText = this.responseText;
                
                const cell = document.getElementById(column + '-' + id);

                if (this.status >= 200 && this.status < 300) { // SUCESSO
                    statusMessage.className = 'alert alert-success';
                    if (column === 'data') {
                        const dateParts = value.split('-');
                        const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                        cell.innerHTML = formattedDate;
                        cell.setAttribute('data-original-date', value);
                    } else {
                        cell.setAttribute('data-original-value', value);
                    }
                } else { // ERRO
                    statusMessage.className = 'alert alert-danger';
                    if (column === 'data') {
                        const originalDate = new Date(cell.getAttribute('data-original-date') + 'T00:00:00');
                        cell.innerHTML = originalDate.toLocaleDateString('pt-BR');
                    } else {
                        // Reverte o campo NOME ou POSIÇÃO para o valor original em caso de erro
                        if (cell) {
                            cell.innerText = cell.getAttribute('data-original-value');
                        }
                    }
                }

                statusMessage.style.display = 'block';
                setTimeout(() => { statusMessage.style.display = 'none'; }, 3000);
            }
        };
        
        xhttp.open("POST", "atualizar_atleta.php", true);
        xhttp.send(formData);
    }

    function deleteRow(id) {
        if (confirm("Tem certeza que deseja excluir este atleta?")) {
            window.location.href = 'visualizar_cadastros.php?delete=' + id;
        }
    }
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paopper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
