<?php
ob_start();
session_start();
include("conexao.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $senha_digitada = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE login=? LIMIT 1";
    $stmt = mysqli_prepare($id, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($senha_digitada, $user['senha'])) {
            $tipo = strtoupper(trim($user['tipo']));
            $_SESSION['id_usuario'] = $user['IDusuario'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['tipo'] = $tipo;

            if ($tipo === 'C') {
                header("Location: cliente_home.php");
                exit;
            } elseif ($tipo === 'T') {
                header("Location: tecnico_home.php");
                exit;
            } elseif ($tipo === 'A') {
                header("Location: admin_home.php");
                exit;
            } else {
                $erro = "Tipo de usuário inválido!";
            }
        } else {
            $erro = "Login ou senha inválidos!";
        }
    } else {
        $erro = "Login ou senha inválidos!";
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - ServiGera</title>
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

         header {
            background-color: #900000;
            color: #fff;
            padding: 18px 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            position: relative;
        }

        header img {
            height: 80px;
            width: auto;
            border-radius: 10px;
        }
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
            background-color: #fff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 440px;
            animation: fadeIn 0.9s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: #e60000;
            margin-bottom: 25px;
        }

        input[type="text"],
        input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button,
        .btn-voltar {
            display: inline-block;
            background-color: #e60000;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        button:hover,
        .btn-voltar:hover {
            background-color: #ff3333;
            transform: scale(1.05);
        }

        .btn-voltar {
            background-color: #900000;
        }

        .erro {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <header>
        <img src="../servigeralateral.png" alt="ServiGera" style="height:80px; border-radius:10px;">
    </header>

    <main>
        <div class="container">
            <h2>Login</h2>
            <form method="post">
                <input type="text" name="login" placeholder="Usuário" required><br>
                <input type="password" name="senha" placeholder="Senha" required><br>
                <button type="submit">Entrar</button>
            </form>
            <a href="../tela_inicial.php" class="btn-voltar">Voltar</a>
            <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>
</body>
</html>
