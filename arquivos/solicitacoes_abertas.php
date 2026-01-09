<?php
session_start();
include("verifica.php");
verifica_tipo('T');
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Pega cidade e estado do técnico logado
$sqlTec = "SELECT cidade, estado FROM tecnicos WHERE IDtecnico = ?";
$stmtTec = mysqli_prepare($id, $sqlTec);
mysqli_stmt_bind_param($stmtTec, "i", $idTecnico);
mysqli_stmt_execute($stmtTec);
$resTec = mysqli_stmt_get_result($stmtTec);
$tecnico = mysqli_fetch_assoc($resTec);

if (!$tecnico) {
    die("Erro: técnico não encontrado.");
}

$cidade = $tecnico['cidade'];
$estado = $tecnico['estado'];

// Busca solicitações abertas SOMENTE da mesma cidade e estado
$sqlSolic = "
SELECT s.*, c.cidade, c.estado, c.nome AS nome_cliente
FROM solicitacoes s
JOIN cliente c ON s.IDcliente = c.IDcliente
WHERE s.status = 'Aberto'
AND c.cidade = ?
AND c.estado = ?
ORDER BY s.data_criacao DESC
";


$stmtSolic = mysqli_prepare($id, $sqlSolic);
mysqli_stmt_bind_param($stmtSolic, "ss", $cidade, $estado);

mysqli_stmt_execute($stmtSolic);
$resSolic = mysqli_stmt_get_result($stmtSolic);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Solicitações Abertas - Servigera</title>
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
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #900000;
            color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;
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

        .container {
            background-color: #fff;
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        h1.page-title {
            color: #e60000;
            text-align: center;
            margin-bottom: 25px;
        }

        a.voltar {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #900000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        a.voltar:hover {
            background-color: #ff3333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        li strong {
            display: block;
            margin-bottom: 8px;
            color: #900000;
        }

        li em {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        li small {
            display: block;
            margin-bottom: 5px;
            color: #777;
        }

        li a {
            display: inline-block;
            background-color: #e60000;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        li a:hover {
            opacity: 0.8;
        }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;

        }
    </style>
</head>

<body>

    <header>
        <img src="../servigeralateral.png" alt="ServiGera">
        <nav>
            <a href="logout.php" class="logout">Sair</a>
        </nav>
    </header>

    <div class="container">
        <a href="tecnico_home.php" class="voltar">← Voltar</a>
        <h1 class="page-title">Solicitações Abertas</h1>
        <ul>
            <?php if (mysqli_num_rows($resSolic) > 0): ?>
                <?php while ($sol = mysqli_fetch_assoc($resSolic)): ?>
                    <li>
                        <strong><?= htmlspecialchars($sol['titulo']); ?></strong>
                        <?= nl2br(htmlspecialchars($sol['descricao'])); ?>
                        <small>Cliente: <?= htmlspecialchars($sol['nome_cliente']); ?> — <?= htmlspecialchars($sol['cidade']); ?>/<?= htmlspecialchars($sol['estado']); ?></small>
                        <a href="enviar_proposta.php?id=<?= $sol['IDsolicitacao']; ?>">Enviar Proposta</a>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Nenhuma solicitação aberta na sua cidade (<?= htmlspecialchars($cidade); ?>/<?= htmlspecialchars($estado); ?>).</li>
            <?php endif; ?>
        </ul>
    </div>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados.
    </footer>
</body>

</html>