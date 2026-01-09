<?php
session_start();
include("verifica.php");
include("conexao.php");

if (!isset($_GET['id'])) {
    die("Técnico não especificado.");
}

$idTecnico = intval($_GET['id']);

$sql = "SELECT t.nome, t.foto, t.bio, t.cidade, t.estado,
        GROUP_CONCAT(e.nome SEPARATOR ', ') AS especialidades
        FROM tecnicos t
        LEFT JOIN tecnico_especialidade te ON t.IDtecnico = te.IDtecnico
        LEFT JOIN especialidades e ON te.IDespecialidade = e.IDespecialidade
        WHERE t.IDtecnico = ?
        GROUP BY t.IDtecnico";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tecnico = mysqli_fetch_assoc($result);

if (!$tecnico) {
    die("Técnico não encontrado.");
}

$sqlMedia = "
    SELECT AVG(a.nota) AS media, COUNT(*) AS total
    FROM avaliacoes a
    INNER JOIN solicitacoes s ON a.IDsolicitacao = s.IDsolicitacao
    INNER JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao
    WHERE p.IDtecnico = ?
    AND p.status = 'aceita'
";
$stmtMedia = mysqli_prepare($id, $sqlMedia);
mysqli_stmt_bind_param($stmtMedia, "i", $idTecnico);
mysqli_stmt_execute($stmtMedia);
$resMedia = mysqli_stmt_get_result($stmtMedia);
$mediaRow = mysqli_fetch_assoc($resMedia);


$media = $mediaRow['media'] !== null ? (float)$mediaRow['media'] : 0.0;
$totalAvaliacoes = isset($mediaRow['total']) ? (int)$mediaRow['total'] : 0;
$mediaArredondada = (int) round($media);
$mediaArredondada = max(0, min($mediaArredondada, 5));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($tecnico['nome']); ?></title>
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
            min-height: 100vh;
        }

        header {
            background-color: #900000;
            color: #fff;
            text-align: center;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        header h1 {
            font-size: 24px;
            color: #fff;
            letter-spacing: 0.5px;
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        img.foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
            margin-bottom: 15px;
        }

        header h1 {
            font-size: 24px;
            letter-spacing: 0.5px;
            color: #fff;
        }

        h1 {
            color: #900000;
            font-size: 22px;
            margin-bottom: 8px;
        }


        h2 {
            color: #900000;
            font-size: 18px;
            font-weight: normal;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .local {
            color: #555;
            font-size: 15px;
            margin-bottom: 10px;
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

        .media-bloco {
            margin: 15px 0;
        }

        .estrelas {
            font-size: 22px;
            color: #FFD700;
        }

        .estrelas .vazia {
            color: #ccc;
        }

        .media-texto {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
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
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body>
    <header>
        <h1>Perfil do Técnico</h1>
    </header>

    <main>
        <div class="container">
            <?php if (!empty($tecnico['foto'])): ?>
                <img class="foto-perfil" src="data:image/jpeg;base64,<?= base64_encode($tecnico['foto']); ?>" alt="Foto de <?= htmlspecialchars($tecnico['nome']); ?>">
            <?php else: ?>
                <img class="foto-perfil" src="../imagens/placeholder.png" alt="Sem foto">
            <?php endif; ?>

            <h1><?= htmlspecialchars($tecnico['nome']); ?></h1>

            <?php if (!empty($tecnico['cidade']) && !empty($tecnico['estado'])): ?>
                <p class="local">📍 <?= htmlspecialchars($tecnico['cidade']); ?> - <?= htmlspecialchars($tecnico['estado']); ?></p>
            <?php endif; ?>

            <?php if (!empty($tecnico['especialidades'])): ?>
                <h2>Especialidades: <?= htmlspecialchars($tecnico['especialidades']); ?></h2>
            <?php endif; ?>

            <div class="media-bloco">
                <div class="estrelas">
                    <?php
                    echo str_repeat('⭐', $mediaArredondada);
                    echo str_repeat('<span class="vazia">☆</span>', 5 - $mediaArredondada);
                    ?>
                </div>
                <div class="media-texto">
                    <?php if ($totalAvaliacoes > 0): ?>
                        Média: <strong><?= number_format($media, 1, ',', '.'); ?></strong> (<?= $totalAvaliacoes; ?> avaliação<?= $totalAvaliacoes > 1 ? 'es' : '' ?>)
                    <?php else: ?>
                        Sem avaliações ainda
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($tecnico['bio'])): ?>
                <p class="bio"><?= htmlspecialchars($tecnico['bio']); ?></p>
            <?php endif; ?>

            <a href="cliente_home.php" class="btn-voltar">Voltar</a>
        </div>
    </main>

    <footer>
        &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
    </footer>
</body>

</html>