<?php
// Inicia a sessão para usar as mensagens
session_start(); 

// Variáveis para mensagens
$erro = "";
$sucesso = "";

// Verifica se existe uma mensagem de sucesso na sessão (após um redirecionamento)
if (isset($_SESSION['mensagem_sucesso'])) {
    $sucesso = $_SESSION['mensagem_sucesso'];
    // Limpa a mensagem da sessão para que não apareça novamente
    unset($_SESSION['mensagem_sucesso']);
}

// Verifica se o método de requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['nome']) || empty($_POST['data']) || empty($_POST['posicao'])) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        require_once "processa_cadastro.php"; 
        $_SESSION['mensagem_sucesso'] = "Atleta cadastrado com sucesso!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Atletas - Craques do Futuro</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="estilo.css"> 

    <style>
        /* --- ESTILOS GLOBAIS --- */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
        }

        /* --- NAVBAR FLUTUANTE E DINÂMICA (A MÁGICA ACONTECE AQUI) --- */
        .navbar {
            transition: background-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out; /* Transição suave */
        }
        
        /* Estilo inicial da navbar: transparente e sem sombra */
        .navbar-transparent {
            background-color: transparent !important;
            box-shadow: none;
        }

        /* Estilo dos links quando a navbar está transparente */
        .navbar-transparent .navbar-brand,
        .navbar-transparent .nav-link {
            color: #ffffff !important;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6); /* Sombra para legibilidade sobre a imagem */
        }

        /* Estilo da navbar quando o usuário rola a página: fundo branco e com sombra */
        .navbar-scrolled {
            background-color: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }

        /* Estilo dos links quando a navbar ganha fundo */
        .navbar-scrolled .navbar-brand {
            color: #f14511 !important; /* Cor da marca volta ao laranja */
            text-shadow: none;
        }
        .navbar-scrolled .nav-link {
            color: #333 !important; /* Links voltam a ser escuros */
            text-shadow: none;
        }

        /* --- CABEÇALHO COM IMAGEM (AGORA MAIS VISÍVEL) --- */
        .header-image-cadastro {
            height: 60vh; /* Altura aumentada para mais impacto */
            width: 100%;
            padding-top: 56px; /* Compensa a altura da navbar */
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://www.gremistas.net/wp-content/uploads/raphinha-atacante-barcelona-nao-torce-gremio.jpg');
            background-size: cover;
            background-position: center 30%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }
        .header-image-cadastro h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* --- OUTROS ESTILOS (FORMULÁRIO E RODAPÉ) --- */
        .form-container { padding: 50px 0; }
        .card { max-width: 600px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .btn-custom-primary { background-color: #f14511; border-color: #f14511; color: #fff; font-weight: bold; transition: background-color 0.3s; }
        .btn-custom-primary:hover { background-color: #e03400; border-color: #e03400; }
        .footer { background-color: #212529; color: #f8f9fa; padding: 20px 0; }
    </style>
</head>
<body>

<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top navbar-transparent">
    <a class="navbar-brand" href="index.php">Craques do Futuro FC</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
            <li class="nav-item active"><a class="nav-link" href="cadastro_atleta.php">Cadastro do Atleta</a></li>
            <li class="nav-item"><a class="nav-link" href="visualizar_cadastros.php">Atletas</a></li>
        </ul>
    </div>
</nav>

<div class="header-image-cadastro">
    <h1>Cadastro de Atletas</h1>
</div>

<div class="form-container">
    <div class="card p-4 p-md-5">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="data">Data de Nascimento:</label>
                <input type="date" id="data" name="data" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="posicao">Posição Preferencial:</label>
                <input type="text" id="posicao" name="posicao" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-custom-primary btn-block mt-4">Cadastrar Atleta</button>
        </form>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger mt-3"><?php echo $erro; ?></div>
        <?php endif; ?>
        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success mt-3"><?php echo $sucesso; ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="footer text-center">
    © 2025 Craques do Futuro FC
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.getElementById('mainNavbar');
        
        // Função para mudar o estilo da navbar
        const handleScroll = () => {
            if (window.scrollY > 50) { // Se rolar mais de 50 pixels
                navbar.classList.add('navbar-scrolled');
                navbar.classList.remove('navbar-transparent');
            } else { // Se estiver no topo
                navbar.classList.add('navbar-transparent');
                navbar.classList.remove('navbar-scrolled');
            }
        };

        // Adiciona o 'escutador' de evento de rolagem
        window.addEventListener('scroll', handleScroll);
    });
</script>

</body>
</html>