<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>ServiGera - Bem-vindo</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; scroll-behavior: smooth; }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            background: #e60000 url("fundo.png") no-repeat center center fixed;
            background-size: cover;
            color: #333;
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

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 40px;
            border-radius: 15px;
            background-color: #fff;
            color: #000;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-size: 2.4rem;
            color: #e60000;
            margin-bottom: 18px;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 28px;
        }

        .btn {
            display: inline-block;
            margin: 10px 12px;
            padding: 14px 30px;
            font-size: 1.03rem;
            font-weight: bold;
            color: #fff;
            background-color: #e60000;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.25);
            transition: all 0.25s ease;
        }

        .btn:hover { background-color: #ff3333; transform: translateY(-2px); box-shadow: 4px 6px 16px rgba(0,0,0,0.3); }

        .btn.secondary {
            background-color: transparent;
            border: 2px solid #e60000;
            color: #e60000;
        }

        .btn.secondary:hover { background-color: #e60000; color: #fff; }

        .btn.voltar {
            display: block;
            margin: 32px auto 0;
            width: fit-content;
            background-color: transparent;
            border: 2px solid #900000;
            color: #900000;
            font-weight: bold;
            padding: 10px 18px;
            border-radius: 8px;
        }

        .btn.voltar:hover { background-color: #900000; color: #fff; transform: translateY(-2px); }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }

        @media (max-width: 520px) {
            header img { height: 56px; border-radius: 8px; }
            h2 { font-size: 1.6rem; }
            .container { padding: 22px; }
            .btn { padding: 10px 18px; font-size: 0.95rem; }
            .btn.voltar { margin-top: 20px; }
        }
    </style>
</head>
<body>
    <header>
        <img src="servigeralateral.png" alt="ServiGera">
    </header>

    <main>
        <div class="container">
            <h2>Bem-vindo ao ServiGera</h2>
            <p>Escolha uma opção para continuar:</p>

            <a href="arquivos/login.php" class="btn">Já tenho conta</a>
            <a href="arquivos/escolher_tipo.php" class="btn secondary">Quero me cadastrar</a>
            <a href="menu_principal.php" class="btn voltar">← Voltar ao Menu Principal</a>
        </div>
</main>
<footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>
</html>