<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // apenas técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Dados do técnico
$sqlTec = "SELECT nome, email, telefone, foto, bio FROM tecnicos WHERE IDtecnico = ?";
$stmtTec = mysqli_prepare($id, $sqlTec);
mysqli_stmt_bind_param($stmtTec, "i", $idTecnico);
mysqli_stmt_execute($stmtTec);
$resTec = mysqli_stmt_get_result($stmtTec);
$tecnico = mysqli_fetch_assoc($resTec);

// Especialidades
$sqlEsp = "SELECT e.nome 
           FROM tecnico_especialidade te
           JOIN especialidades e ON te.IDespecialidade = e.IDespecialidade
           WHERE te.IDtecnico = ?";
$stmtEsp = mysqli_prepare($id, $sqlEsp);
mysqli_stmt_bind_param($stmtEsp, "i", $idTecnico);
mysqli_stmt_execute($stmtEsp);
$resEsp = mysqli_stmt_get_result($stmtEsp);
$especialidades = [];
while($row = mysqli_fetch_assoc($resEsp)){
    $especialidades[] = $row['nome'];
}

// Propostas do técnico
$sqlProp = "SELECT p.*, s.titulo, s.status AS status_solicitacao
            FROM propostas p
            JOIN solicitacoes s ON p.IDsolicitacao = s.IDsolicitacao
            WHERE p.IDtecnico = ?";
$stmtProp = mysqli_prepare($id, $sqlProp);
mysqli_stmt_bind_param($stmtProp, "i", $idTecnico);
mysqli_stmt_execute($stmtProp);
$resProp = mysqli_stmt_get_result($stmtProp);

// Média de avaliações REAL
$sqlMedia = "
    SELECT AVG(a.nota) AS media
    FROM avaliacoes a
    JOIN solicitacoes s ON a.IDsolicitacao = s.IDsolicitacao
    JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao
    WHERE p.IDtecnico = ?
";
$stmtMedia = mysqli_prepare($id, $sqlMedia);
mysqli_stmt_bind_param($stmtMedia, "i", $idTecnico);
mysqli_stmt_execute($stmtMedia);
$resMedia = mysqli_stmt_get_result($stmtMedia);
$media = mysqli_fetch_assoc($resMedia)['media'] ?? 0;
$mediaArredondada = round($media);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Home Técnico - Servigera</title>
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

        header h1 { margin: 0; color: #fff; font-size: 1.6rem; }
        header nav { position: absolute; top: 18px; right: 18px; }
        header nav a.logout {
            color: #fff;
            background-color: #cc0000;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.25s;
        }
        header nav a.logout:hover { background-color: #ff3333; transform: translateY(-2px); }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 36px 20px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            max-width: 900px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-12px);} to { opacity:1; transform: translateY(0);} }

        img.foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
            margin-bottom: 12px;
            display: inline-block;
        }

        .bio { font-style: italic; margin-bottom: 16px; white-space: pre-wrap; word-break: break-word; color: #333; }

        .ver-perfil { display: block; margin-bottom: 14px; color: #e60000; text-decoration: underline; }

        .estrelas {
            font-size: 1.4em;
            color: #FFD700;
            margin-bottom: 8px;
        }
        .estrelas .cinza { color: #ccc; }

        .actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }
        .actions a {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background-color: #e60000;
            transition: all 0.25s;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        .actions a:hover { transform: translateY(-2px); opacity: 0.95; }
        .actions a.azul { background-color: #0066cc; }
        .actions a.verde { background-color: #009933; }
        .actions a.amarelo { background-color: #ff9900; }
        .actions a.vermelho { background-color: #cc0000; }

        .solicitacoes {
            margin-top: 18px;
            text-align: left;
        }
        .solicitacoes h2 { color: #900000; margin-bottom: 12px; font-size: 1.2rem; }
        .solicitacoes ul { list-style: none; padding: 0; margin: 0; }
        .solicitacoes li {
            padding: 12px;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-direction: column;
            word-break: break-word;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .solicitacoes li strong { display:block; margin-bottom:6px; color:#222; }
        .solicitacoes li em { color: #555; font-size:0.95rem; }

        .acoes { margin-top: 8px; }
        .acoes a {
            margin-right: 6px;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            font-size: 0.92em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        }
        .acoes a.visualizar { background-color: #009933; }
        .acoes a.editar { background-color: #0066cc; }
        .acoes a.excluir { background-color: #cc0000; }
        .acoes a:hover { opacity: 0.9; transform: translateY(-1px); }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }

        @media (max-width: 720px) {
            .container { padding: 20px; }
            img.foto-perfil { width: 96px; height: 96px; }
            .actions a { padding: 9px 14px; font-size: 0.95rem; }
        }
    </style>
</head>
<body>
<header>
    <h1>Bem-vindo(a), <?= htmlspecialchars($tecnico['nome']); ?>!</h1>
    <nav>
        <a class="logout" href="logout.php">Sair</a>
    </nav>
</header>
<main>
    <div class="container">
        <?php if(!empty($tecnico['foto'])): ?>
            <img class="foto-perfil" src="data:image/jpeg;base64,<?= base64_encode($tecnico['foto']); ?>" alt="Foto do Técnico">
        <?php else: ?>
            <img class="foto-perfil" src="../imagens/placeholder.png" alt="Sem foto">
        <?php endif; ?>

        <div class="estrelas">
            <?php
            $estrelasCheias = str_repeat("⭐", $mediaArredondada);
            $estrelasVazias = str_repeat("☆", 5 - $mediaArredondada);
            echo $estrelasCheias . $estrelasVazias;
            ?>
            <br><small>(Média: <?= number_format($media, 1, ',', '.'); ?>)</small>
        </div>

        <p class="bio"><?= htmlspecialchars($tecnico['bio']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($tecnico['email']); ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($tecnico['telefone']); ?></p>
        <p><strong>Especialidades:</strong> <?= implode(", ", $especialidades); ?></p>

        <a href="ver_perfil_tecnico.php?id=<?= $idTecnico; ?>" class="ver-perfil">Ver perfil público</a>

        <div class="actions">
            <a href="solicitacoes_abertas.php">Ver Solicitações Abertas</a>
            <a href="avaliacoes_tecnico.php" class="amarelo">Minhas Avaliações</a>
            <a href="editar_tecnico.php" class="azul">Editar Conta</a>
            <a href="especialidades.php" class="verde">Gerenciar Especialidades</a>
            <a href="excluir_tecnico.php" class="vermelho" onclick="return confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível.');">Excluir Conta</a>
        </div>

        <section class="solicitacoes">
            <h2>Suas Propostas</h2>
            <ul>
                <?php if(mysqli_num_rows($resProp) > 0): ?>
                    <?php while($prop = mysqli_fetch_assoc($resProp)): ?>
                        <li>
                            <strong><?= htmlspecialchars($prop['titulo']); ?></strong>
                            <?= nl2br(htmlspecialchars($prop['mensagem'])); ?><br>
                            <em>Status da proposta: <?= htmlspecialchars($prop['status']); ?></em><br>
                            <?php
                             $statusSolic = $prop['status_solicitacao'];
                            if (strtolower($statusSolic) === 'finalizado') {
                             $statusSolic = 'Concluído';
                            }
                            ?>
                            <em>Status da solicitação: <?= htmlspecialchars($statusSolic ?: 'Concluído'); ?></em><br>
                            <div class="acoes">
                                <a href="visualizar_solicitacao_tecnico.php?id=<?= $prop['IDsolicitacao']; ?>" class="visualizar">Visualizar Solicitação</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>Você ainda não enviou nenhuma proposta.</li>
                <?php endif; ?>
            </ul>
        </section>
    </div>
</main>
<footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
</footer>
</body>
</html>
