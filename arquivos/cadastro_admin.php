<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A'); // Apenas admins

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $senha = $_POST['senha'];

    // Criptografar a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir no banco
    $stmt = $id->prepare("INSERT INTO usuarios (login, senha, tipo) VALUES (?, ?, 'A')");
    $stmt->bind_param("ss", $login, $senha_hash);

    if ($stmt->execute()) {
        // Redireciona para admin_home sem logar o usuário novo
        header("Location: admin_home.php");
        exit;
    } else {
        $erro = "Erro ao cadastrar: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Admin - ServiGera</title>
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
            display: flex;
            flex-direction: column;
        }

        header,
          footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            max-width: 400px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            padding: 40px;
            animation: fadeIn 1s ease-out;
            text-align: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: #e60000;
            margin-bottom: 25px;
            font-size: 2rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
            text-align: left;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            padding: 14px;
            font-size: 1.05rem;
            font-weight: bold;
            color: #fff;
            background-color: #e60000;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #ff3333;
            transform: scale(1.05);
        }

        .btn.secondary {
            background-color: transparent;
            border: 2px solid #e60000;
            color: #e60000;
        }

        .btn.secondary:hover {
            background-color: #e60000;
            color: #fff;
        }

        .erro {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <header>
        <h1>ServiGera - Admin</h1>
    </header>

    <main>
        <div class="container">
            <h2>Cadastro de Administrador</h2>
            <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>
            <form method="post">
                <label>Login:<input type="text" name="login" required></label>
                <label>Senha:<input type="password" name="senha" required></label>
                <div class="botoes">
                    <button type="submit" class="btn">Cadastrar</button>
                    <a href="admin_home.php" class="btn secondary">Voltar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>
</body>

</html>