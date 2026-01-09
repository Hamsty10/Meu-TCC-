<?php
session_start();
include("verifica.php");
verifica_tipo('C');
include("conexao.php");
$idCliente = $_SESSION['id_usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $data_criacao = date('Y-m-d H:i:s');
    $status = "Aberto";

    if ($titulo != "" && $descricao != "") {
        $sql = "INSERT INTO solicitacoes (IDcliente, titulo, descricao, status, data_criacao) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($id, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $idCliente, $titulo, $descricao, $status, $data_criacao);
        mysqli_stmt_execute($stmt);
        header("Location: cliente_home.php");
        exit();
    } else {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Solicitar Serviço - ServiGera</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: Arial, sans-serif;
            background: #e60000 url("../fundo.png") no-repeat center center fixed;
            background-size: cover;
            color: #333;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #900000;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        header img {
            height: 80px;
            width: auto;
            border-radius: 10px;
        }

        header nav {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        header nav a.logout {
            color: #fff;
            background-color: #cc0000;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        header nav a.logout:hover {
            background-color: #ff3333;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 100%;
        }

        h2 {
            color: #900000;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        .erro {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .botoes {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background-color: #e60000;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn.azul {
            background-color: #900000;
        }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body>
    <header>
        <img src="../servigeralateral.png" alt="ServiGera">
        <nav>
            <a class="logout" href="logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Solicitar Serviço</h2>
            <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
            <form method="post" action="">
                <label for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="descricao">Descrição *</label>
                <textarea id="descricao" name="descricao" rows="5" required></textarea>

                <div class="botoes">
                    <input type="submit" value="Enviar Solicitação" class="btn">
                    <a href="cliente_home.php" class="btn azul">Voltar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>
</body>

</html>