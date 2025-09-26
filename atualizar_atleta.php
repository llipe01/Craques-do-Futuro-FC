<?php
// ARQUIVO: atualizar_atleta.php (Versão Corrigida e Mais Inteligente)

// --- Configuração do Banco de Dados ---
$servername = "localhost:3306";
$username = "root";
$password = "Senai@118";
$dbname = "craques_do_futuro_fc";

// --- Conexão ---
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Conexão falhou: " . $conn->connect_error);
}

// --- Lógica de Atualização Aprimorada ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'], $_POST['column'], $_POST['value'])) {
    
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    $allowed_columns = ['nome', 'data', 'posicao'];
    
    if (in_array($column, $allowed_columns)) {
        $stmt = $conn->prepare("UPDATE atletas SET $column = ? WHERE id = ?");
        $stmt->bind_param("si", $value, $id);
        
        // MUDANÇA IMPORTANTE AQUI: Verificamos a execução E o número de linhas afetadas
        if ($stmt->execute()) {
            
            // NOVO BLOCO: Verifica se algo realmente mudou
            if ($stmt->affected_rows > 0) {
                if ($column === 'data') {
                    $date = new DateTime($value);
                    echo "Data atualizada para " . $date->format('d/m/Y');
                } else {
                    echo "Registro atualizado com sucesso.";
                }
            } else {
                // Se nenhuma linha foi afetada, significa que o valor era o mesmo
                echo "Nenhuma alteração foi feita (o valor já era o mesmo).";
            }

        } else {
            // CORREÇÃO CRÍTICA: Envia um status de erro HTTP se a execução falhar
            header("HTTP/1.1 500 Internal Server Error");
            echo "Erro ao atualizar o registro: " . $stmt->error;
        }
        $stmt->close();
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo "Operação inválida: coluna não permitida.";
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "Requisição inválida.";
}

$conn->close();
?>