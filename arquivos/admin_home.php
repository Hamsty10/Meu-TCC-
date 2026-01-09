<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Admin Home - Servigera</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }

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
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;
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
            transition: 0.25s;
        }

        header nav a.logout:hover {
            background-color: #ff3333;
            transform: translateY(-2px);
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 36px 20px;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #900000;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.8rem;
        }

        .card-section {
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .card-section h3 {
            color: #900000;
            margin-bottom: 18px;
            font-size: 1.3rem;
            text-align: center;
        }

        .linha {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .btn {
            padding: 14px 26px;
            border-radius: 10px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background-color: #e60000;
            transition: all 0.25s;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-3px);
            opacity: 0.95;
        }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 720px) {
            .container { padding: 25px; }
            .btn { padding: 12px 20px; font-size: 0.95rem; }
        }
    </style>
</head>

<body>

<header>
    <h1>Bem-vindo, Admin!</h1>
    <nav><a class="logout" href="logout.php">Sair</a></nav>
</header>

<main>
    <div class="container">

        <h2>Painel Administrativo</h2>

        <!-- SEÇÃO 1 — GERENCIAR LISTAS -->
        <div class="card-section">
            <h3>Gerenciar Listas</h3>
            <div class="linha">
                <a class="btn" href="clientes_lista.php">Clientes</a>
                <a class="btn" href="tecnicos_lista.php">Técnicos</a>
                <a class="btn" href="admins_lista.php">Admins</a>
                <a class="btn" href="solicitacoes_lista.php">Solicitações</a>
                <a class="btn" href="propostas_lista.php">Propostas</a>
                <a class="btn" href="avaliacoes_lista.php">Avaliações</a>
                <a class="btn" href="especialidades_lista.php">Especialidades</a>
            </div>
        </div>

        <!-- SEÇÃO 2 — CADASTRAR -->
        <div class="card-section">
            <h3>Cadastrar Usuários</h3>
            <div class="linha">
                <a class="btn" href="cadastro_cliente_admin.php">Cadastrar Cliente</a>
                <a class="btn" href="cadastro_tecnico_admin.php">Cadastrar Técnico</a>
                <a class="btn" href="cadastro_admin.php">Cadastrar Admin</a>
            </div>
        </div>

    </div>
</main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>

</body>
</html>
