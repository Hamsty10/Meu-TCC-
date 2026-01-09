<?php
session_start();
include("verifica.php");
verifica_tipo('C');
$login = $_SESSION['login'];
$idCliente = $_SESSION['id_usuario'];
include("conexao.php");

$sqlCliente = "SELECT nome, foto, bio, telefone, email FROM cliente WHERE IDcliente = ?";
$stmtCli = mysqli_prepare($id, $sqlCliente);
mysqli_stmt_bind_param($stmtCli, "i", $idCliente);
mysqli_stmt_execute($stmtCli);
$resCli = mysqli_stmt_get_result($stmtCli);
$clienteDados = mysqli_fetch_assoc($resCli);

$sql = "SELECT * FROM solicitacoes WHERE IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Home Cliente - Servigera</title>
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

        @keyframes fadeIn { from { opacity:0; transform: translateY(-12px);} to { opacity:1; transform: translateY(0);} }

        img.foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e60000;
            margin-bottom: 12px;
            display: inline-block;
        }

        h2 { color: #900000; margin-bottom: 14px; font-size: 1.6rem; }
        .bio { font-style: italic; margin-bottom: 12px; white-space: pre-wrap; word-break: break-word; }

        .ver-perfil { display: block; margin-bottom: 14px; color: #e60000; text-decoration: underline; }

        .botoes {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            background-color: #e60000;
            transition: all 0.25s;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        .btn:hover { transform: translateY(-2px); opacity: 0.96; }

        .btn.azul { background-color: #0066cc; }
        .btn.vermelho { background-color: #cc0000; }

        .solicitacoes {
            margin-top: 18px;
            text-align: left;
        }
        .solicitacoes h2 { margin-bottom: 10px; color: #900000; font-size: 1.2rem; }
        .solicitacoes ul { list-style: none; padding: 0; margin: 0; }
        .solicitacoes li {
            padding: 12px;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            word-break: break-word;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .solicitacoes li span { display: inline-block; vertical-align: middle; }
        .solicitacoes .acoes a {
            margin-left: 6px;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            font-size: 0.92em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        }
        .solicitacoes .acoes a.visualizar { background-color: #009933; }
        .solicitacoes .acoes a.editar { background-color: #0066cc; }
        .solicitacoes .acoes a.excluir { background-color: #cc0000; }
        .solicitacoes .acoes a:hover { transform: translateY(-1px); opacity: 0.95; }

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
            .botoes .btn { padding: 9px 12px; font-size: 0.95rem; }
            .solicitacoes li { flex-direction: column; align-items: flex-start; gap: 6px; }
        }
    </style>
</head>

<body>
    <header>
        <h1>Bem-vindo(a), <?= htmlspecialchars($clienteDados['nome']); ?>!</h1>
        <nav>
            <a class="logout" href="logout.php">Sair</a>
        </nav>
    </header>
    <main>
        <div class="container">
            <?php if (!empty($clienteDados['foto'])): ?>
                <img class="foto-perfil" src="data:image/jpeg;base64,<?= base64_encode($clienteDados['foto']); ?>" alt="Foto do Cliente">
            <?php else: ?>
                <img class="foto-perfil" src="../imagens/placeholder.png" alt="Sem foto">
            <?php endif; ?>

            <?php if (!empty($clienteDados['bio'])): ?>
                <p class="bio"><?= htmlspecialchars($clienteDados['bio']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($clienteDados['email']); ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($clienteDados['telefone']); ?></p>
            <?php endif; ?>

            <a href="ver_perfil.php?id=<?= $idCliente; ?>" class="ver-perfil">Ver perfil público</a>

            <div class="botoes">
                <a class="btn" href="solicitar_servico.php">Solicitar Serviço</a>
                <a class="btn azul" href="editar_cliente.php">Editar Conta</a>
                <a class="btn" href="avaliacoes_cliente.php">Minhas Avaliações</a>
                <a class="btn vermelho" href="excluir_cliente.php" onclick="return confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível.');">Excluir Conta</a>
            </div>

            <section class="solicitacoes">
                <h2>Suas Solicitações</h2>
                <ul>
                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($res)): ?>
                            <li>
                                <span><strong><?= htmlspecialchars($row['titulo']); ?></strong> <?= htmlspecialchars($row['status']); ?></span>
                                <span class="acoes">
                                    <a href="visualizar_solicitacao.php?id=<?= $row['IDsolicitacao']; ?>" class="visualizar">Visualizar</a>
                                    <a href="editar_solicitacao.php?id=<?= $row['IDsolicitacao']; ?>" class="editar">Editar</a>
                                    <a href="excluir_solicitacao.php?id=<?= $row['IDsolicitacao']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir esta solicitação?');">Excluir</a>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Você ainda não possui solicitações.</li>
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
