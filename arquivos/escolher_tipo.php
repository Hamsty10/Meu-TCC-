<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>ServiGera - Escolha o tipo de conta</title>
    <style>
        /* ===== SERVIGERA - CSS PADRONIZADO (interno) ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; scroll-behavior: smooth; }
        html, body {
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
        @keyframes fadeIn { from {opacity:0; transform:translateY(-12px);} to {opacity:1; transform:translateY(0);} }
        h2 { font-size: 2rem; color: #e60000; margin-bottom: 16px; }
        p { font-size: 1.1rem; margin-bottom: 22px; }
        .btn {
            display: inline-block;
            margin: 10px 12px;
            padding: 12px 26px;
            font-size: 1.03rem;
            font-weight: bold;
            color: #fff;
            background-color: #e60000;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.25);
            transition: all 0.25s ease;
        }
        .btn:hover { background-color: #ff3333; transform: translateY(-2px); }
        .btn.secondary {
            background-color: transparent;
            border: 2px solid #e60000;
            color: #e60000;
        }
        .btn.secondary:hover { background-color: #e60000; color: #fff; }
        .btn.voltar {
            background-color: #900000;
            border: 2px solid #900000;
            color: #fff;
        }
        .btn.voltar:hover { background-color: #b30000; transform: translateY(-2px); }
        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }
        @media (max-width:520px){
            header img{height:56px;}
            .container{padding:20px;}
            h2{font-size:1.5rem;}
            .btn{padding:10px 14px; font-size:0.95rem;}
        }
    </style>
</head>
<body>
    <header>
        <img src="../servigeralateral.png" alt="ServiGera">
    </header>

    <main>
        <div class="container">
            <h2>Cadastro</h2>
            <p>Selecione o tipo de conta que deseja criar:</p>
            <a href="cadastro_cliente.php" class="btn">Sou Cliente</a>
            <a href="cadastro_tecnico.php" class="btn secondary">Sou Técnico</a><br><br>
            <a href="../tela_inicial.php" class="btn voltar">Voltar</a>
        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>
</body>
</html>
