<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost:3306";
    $username   = "root";
    $password   = "Senai@118";
    $dbname     = "craques_do_futuro_fc";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $_SESSION['mensagem'] = "Conexão falhou: " . $conn->connect_error;
    } else {
        // Valida campos
        if (!empty($_POST['nome']) && !empty($_POST['data']) && !empty($_POST['posicao'])) {
            $nome    = $_POST['nome'];
            $data    = $_POST['data'];
            $posicao = $_POST['posicao'];

            // Usando Prepared Statement
            $stmt = $conn->prepare("INSERT INTO atletas (nome, data, posicao) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $data, $posicao);

            if ($stmt->execute()) {
                $_SESSION['mensagem'] = "Cadastro realizado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Erro ao realizar cadastro: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['mensagem'] = "Preencha todos os campos!";
        }
    }

    $conn->close();
    header("Location: cadastro_atleta.php");
    exit;
}
?>
