<?php
session_start();
include("verifica.php");
verifica_tipo('T'); 
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Nome do técnico
$sqlTec = "SELECT nome FROM tecnicos WHERE IDtecnico = ?";
$stmtTec = mysqli_prepare($id, $sqlTec);
mysqli_stmt_bind_param($stmtTec, "i", $idTecnico);
mysqli_stmt_execute($stmtTec);
$resTec = mysqli_stmt_get_result($stmtTec);
$tecnico = mysqli_fetch_assoc($resTec);

// Avaliações recebidas
$sql = "
SELECT a.IDavaliacao, a.comentario, a.nota, a.data_avaliacao, a.foto,
       c.nome AS nome_cliente, c.foto AS foto_cliente
FROM avaliacoes a
INNER JOIN solicitacoes s ON a.IDsolicitacao = s.IDsolicitacao
INNER JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao
INNER JOIN cliente c ON s.IDcliente = c.IDcliente
WHERE p.IDtecnico = ? AND p.status = 'aceita'
ORDER BY a.IDavaliacao DESC
";

$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Avaliações Recebidas - Servigera</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

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

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
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

        .foto-cliente {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
        }

        .detalhes {
            flex: 1;
        }

        .nome-cliente {
            font-weight: bold;
            font-size: 18px;
            color: #900000;
        }

        .meta {
            font-size: 0.85rem;
            color: #666;
            margin-top: 3px;
        }

        .estrelas {
            font-size: 20px;
            color: gold;
            margin: 6px 0;
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
            <h2>Avaliações que você recebeu</h2>

            <?php if (mysqli_num_rows($res) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($res)): ?>
                
                    <div class="avaliacao">

                        <div class="topo-avaliacao">
                            <?php if (!empty($row['foto_cliente'])): ?>
                                <img class="foto-cliente" src="data:image/jpeg;base64,<?= base64_encode($row['foto_cliente']); ?>">
                            <?php else: ?>
                                <img class="foto-cliente" src="../imagens/placeholder.png">
                            <?php endif; ?>

                            <div class="detalhes">
                                <div class="nome-cliente"><?= htmlspecialchars($row['nome_cliente']); ?></div>

                                <?php if (!empty($row['data_avaliacao'])): ?>
                                    <div class="meta">
                                        Avaliado em:
                                        <?= date('d/m/Y H:i', strtotime($row['data_avaliacao'])); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="estrelas">
                                    <?php 
                                    $nota = (int)$row['nota'];
                                    echo str_repeat("⭐", $nota) . str_repeat("☆", 5 - $nota);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="comentario">
                            "<?= htmlspecialchars($row['comentario']); ?>"
                        </div>

                        <?php if (!empty($row['foto'])): ?>
                            <img class="foto-avaliacao" src="data:image/jpeg;base64,<?= base64_encode($row['foto']); ?>">
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;">Você ainda não recebeu avaliações.</p>
            <?php endif; ?>

            <div style="text-align:center;">
                <a href="tecnico_home.php" class="voltar">Voltar à Home</a>
            </div>

        </div>
    </main>

    <footer>
        &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
    </footer>

</body>
</html>
