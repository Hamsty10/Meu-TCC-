<?php
session_start();
include("verifica.php");
include("conexao.php");

if (!isset($_GET['id'])) {
    die("Cliente não especificado.");
}
$idCliente = intval($_GET['id']);

// Buscar informações do cliente incluindo cidade e estado
$sql = "SELECT nome, foto, bio, cidade, estado FROM cliente WHERE IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            background: #e60000 url("../fundo.png") no-repeat center center fixed;
            background-size: cover;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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



        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            background-color: #fff;
            padding: 35px 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        img.foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
            margin-bottom: 15px;
        }

       h1 {
    color: #900000; 
    font-size: 22px;
    margin-bottom: 8px;
}

        .local {
            color: #555;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .bio {
            font-style: italic;
            background: #f5f5f5;
            padding: 12px;
            border-radius: 8px;
            white-space: pre-wrap;
            word-break: break-word;
            margin-bottom: 20px;
        }

        .btn-voltar {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background-color: #900000;
            transition: all 0.3s;
        }

        .btn-voltar:hover {
            background-color: #ff3333;
            transform: scale(1.05);
        }

         footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }
    </style>
</head>

<body>
    <header>
     <img src="../servigeralateral.png" alt="ServiGera">
    </header>

    <main>
        <div class="container">
            <?php if (!empty($cliente['foto'])): ?>
                <img class="foto-perfil" src="data:image/jpeg;base64,<?= base64_encode($cliente['foto']); ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']); ?>">
            <?php else: ?>
                <img class="foto-perfil" src="../imagens/placeholder.png" alt="Sem foto">
            <?php endif; ?>

            <h1><?= htmlspecialchars($cliente['nome']); ?></h1>

            <?php if (!empty($cliente['cidade']) && !empty($cliente['estado'])): ?>
                <p class="local">📍<?= htmlspecialchars($cliente['cidade']) ?> - <?= htmlspecialchars($cliente['estado']) ?></p>
            <?php endif; ?>

            <?php if (!empty($cliente['bio'])): ?>
                <p class="bio"><?= htmlspecialchars($cliente['bio']); ?></p>
            <?php endif; ?>

            <a href="cliente_home.php" class="btn-voltar">Voltar</a>
        </div>
    </main>

    <footer>
        &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
    </footer>
</body>
</html>
