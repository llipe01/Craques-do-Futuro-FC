<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "craques_do_futuro_fc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o pedido de DELETE foi recebido
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM atletas WHERE id = $id");
    header("Location: visualizar_cadastros.php");
}

// Processar a atualização dos dados via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    $sql = "UPDATE atletas SET ".$column."='".$conn->real_escape_string($value)."' WHERE id=".$id;
    if ($conn->query($sql) === TRUE) {
        echo "Registro atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar o registro: " . $conn->error;
    }
    exit; // Encerrar a execução após processar a requisição AJAX
}

// Exibir registros
$sql = "SELECT id, nome, data, posicao FROM atletas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visualizar Cadastros de Atletas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="estilo.css">

    <script>
    function enableEditing(id) {
        var autorCell = document.getElementById('nome-' + id);
        var tituloCell = document.getElementById('data-' + id);
        var descricaoCell = document.getElementById('posicao-' + id);
        autorCell.contentEditable = true;
        tituloCell.contentEditable = true;
        descricaoCell.contentEditable = true;
        autorCell.focus();
    }

    function updateData(element, column, id) {
    var value = element.innerText;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Atualiza o elemento de mensagem de status em vez de exibir um alerta
            document.getElementById('statusMessage').innerText = this.responseText;
            // Opcional: limpa a mensagem após um certo tempo
            setTimeout(function() {
                document.getElementById('statusMessage').innerText = '';
            }, 3000); // Limpa após 3 segundos
        }
    };
    xhttp.open("POST", "visualizar_cadastros.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + id + "&column=" + column + "&value=" + encodeURIComponent(value));
}


    function deleteRow(id) {
        var confirmDelete = confirm("Tem certeza que deseja excluir este registro?");
        if (confirmDelete) {
            window.location.href = 'visualizar_cadastros.php?delete=' + id;
        }
    }
    </script>



</head>
<body>

<div class="container">
    <img src="bola.jpg" alt="" class="header-image">
    <hr>
    <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center navbar-custom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Início</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cadastro_atleta.php">Cadastro do Atleta</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="visualizar_cadastros.php">Atletas</a>
            </li>
        </ul>
    </nav>
    <hr>
    <h1>Visualizar Cadastros de Atletas</h1>
    <?php
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Autor</th><th>Titulo</th><th>Descricao</th><th>Acoes</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td id='nome-" . $row["id"] . "' contentEditable='false' onBlur='updateData(this, \"nome\", ".$row["id"].")'>" . $row["nome"]. "</td><td id='data-" . $row["id"] . "' contentEditable='false' onBlur='updateData(this, \"data\", ".$row["id"].")'>" . $row["data"]. "</td><td id='posicao-" . $row["id"] . "' contentEditable='false' onBlur='updateData(this, \"posicao\", ".$row["id"].")'>" . $row["posicao"]. "</td><td>";
            echo "<button onClick='enableEditing(".$row["id"].")'>✏️</button> ";
            echo "<button onClick='deleteRow(".$row["id"].")'>🗑️</button>";
            echo "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Ainda não há cadastro...";
    }
    ?>
</div>

<div class="footer">
    © 2025 Craques do Futuro
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>