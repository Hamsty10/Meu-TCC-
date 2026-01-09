<?php
session_start();
include("verifica.php");
verifica_tipo('C');
include("conexao.php");

$idCliente = $_SESSION['id_usuario'];

// Busca nome do cliente
$sqlCliente = "SELECT nome FROM cliente WHERE IDcliente = ?";
$stmtCli = mysqli_prepare($id, $sqlCliente);
mysqli_stmt_bind_param($stmtCli, "i", $idCliente);
mysqli_stmt_execute($stmtCli);
$resCli = mysqli_stmt_get_result($stmtCli);
$cliente = mysqli_fetch_assoc($resCli);

// Busca todas as avaliações (com foto do técnico e do cliente)
$sql = "
SELECT 
    a.comentario,
    a.nota,
    a.foto AS foto_cliente,
    t.nome AS nome_tecnico,
    t.foto AS foto_tecnico
FROM avaliacoes a
JOIN solicitacoes s ON a.IDsolicitacao = s.IDsolicitacao
JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao AND p.status = 'aceita'
JOIN tecnicos t ON t.IDtecnico = p.IDtecnico
WHERE s.IDcliente = ?
ORDER BY a.IDavaliacao DESC
";

$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Avaliações - Servigera</title>
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
        }

        body {
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
            max-width: 900px;
            width: 100%;
        }

        h2 {
            color: #900000;
            margin-bottom: 20px;
            text-align: center;
        }

        .avaliacao {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .avaliacao:last-child {
            border-bottom: none;
        }

        .topo-avaliacao {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .foto-tecnico {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
        }

        .detalhes {
            flex: 1;
        }

        .nome-tecnico {
            font-weight: bold;
            font-size: 18px;
            color: #900000;
        }

        .estrelas {
            font-size: 20px;
            color: gold;
            margin: 5px 0;
        }

        .comentario {
            margin-top: 10px;
            font-style: italic;
            color: #555;
        }

        .foto-avaliacao {
            margin-top: 15px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            border: 2px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            object-fit: cover;
        }

        a.voltar {
            display: inline-block;
            margin-top: 25px;
            background: #e60000;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        a.voltar:hover {
            opacity: 0.8;
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
        <nav>
            <a class="logout" href="logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Minhas Avaliações</h2>

            <?php if (mysqli_num_rows($res) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                    <div class="avaliacao">
                        <div class="topo-avaliacao">
                            <?php if (!empty($row['foto_tecnico'])): ?>
                                <img class="foto-tecnico" src="data:image/jpeg;base64,<?= base64_encode($row['foto_tecnico']); ?>" alt="Foto do Técnico">
                            <?php else: ?>
                                <img class="foto-tecnico" src="../imagens/placeholder.png" alt="Sem foto">
                            <?php endif; ?>

                            <div class="detalhes">
                                <div class="nome-tecnico"><?= htmlspecialchars($row['nome_tecnico']); ?></div>
                                <div class="estrelas">
                                    <?php
                                    $nota = (int)$row['nota'];
                                    echo str_repeat("⭐", $nota) . str_repeat("☆", 5 - $nota);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="comentario">"<?= htmlspecialchars($row['comentario']); ?>"</div>

                        <?php if (!empty($row['foto_cliente'])): ?>
                            <img class="foto-avaliacao" src="data:image/jpeg;base64,<?= base64_encode($row['foto_cliente']); ?>" alt="Foto da Avaliação">
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">Você ainda não fez nenhuma avaliação.</p>
            <?php endif; ?>

            <div style="text-align:center;">
                <a href="cliente_home.php" class="voltar">Voltar à Home</a>
            </div>
        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>
</body>
</html>
