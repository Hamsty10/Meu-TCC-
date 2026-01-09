<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // somente técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'] ?? null;
if (!$idTecnico) {
    die("Técnico não autenticado.");
}

$db = isset($id) ? $id : (isset($conexao) ? $conexao : (isset($mysqli) ? $mysqli : null));
if (!$db) die("Erro: conexão com o banco não encontrada.");

$IDsolicitacao = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($IDsolicitacao <= 0) {
    die("Solicitação inválida.");
}

$sqlDet = "SELECT s.*, c.nome AS nome_cliente, c.IDcliente
           FROM solicitacoes s
           LEFT JOIN cliente c ON s.IDcliente = c.IDcliente
           WHERE s.IDsolicitacao = ?";
$stmtDet = mysqli_prepare($db, $sqlDet);
mysqli_stmt_bind_param($stmtDet, "i", $IDsolicitacao);
mysqli_stmt_execute($stmtDet);
$resDet = mysqli_stmt_get_result($stmtDet);
$sol = mysqli_fetch_assoc($resDet);
if (!$sol) die("Solicitação não encontrada.");

$sqlCheck = "SELECT COUNT(*) AS cnt FROM propostas WHERE IDsolicitacao = ? AND IDtecnico = ?";
$stmtCheck = mysqli_prepare($db, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "ii", $IDsolicitacao, $idTecnico);
mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);
$rowCheck = mysqli_fetch_assoc($resCheck);
$jaEnviou = ($rowCheck['cnt'] > 0);

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$jaEnviou) {
    $mensagem = trim($_POST['mensagem'] ?? '');
    if ($mensagem === '') {
        $erro = "Por favor, escreva uma mensagem para sua proposta.";
    } else {
        $sqlIns = "INSERT INTO propostas (IDsolicitacao, IDtecnico, mensagem, status, criado_em)
                   VALUES (?, ?, ?, 'pendente', NOW())";
        $stmtIns = mysqli_prepare($db, $sqlIns);
        mysqli_stmt_bind_param($stmtIns, "iis", $IDsolicitacao, $idTecnico, $mensagem);
        if (mysqli_stmt_execute($stmtIns)) {
            header("Location: tecnico_home.php?msg=" . urlencode("Proposta enviada com sucesso"));
            exit();
        } else {
            $erro = "Erro ao enviar proposta. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Enviar Proposta - ServiGera</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: Arial, sans-serif; background: #e60000 url("../fundo.png") no-repeat center center fixed; background-size: cover; display: flex; flex-direction: column; }
        header { background-color: #900000; color: #fff; padding: 20px; text-align: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); position: relative; }
        header img { height: 80px; width: auto; border-radius: 10px; }
        main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
        .container { background: #fff; max-width: 600px; width: 100%; padding: 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3); text-align: left; }
        h1 { text-align: center; margin-bottom: 15px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        textarea { width: 100%; padding: 10px; margin-top: 6px; border-radius: 8px; border: 1px solid #ccc; resize: none; }
        button { margin-top: 14px; padding: 10px 16px; background: #009933; color: #fff; border: none; border-radius: 8px; cursor: pointer; width: 100%; }
        button:hover { background: #00b33c; }
        a.voltar { display: inline-block; margin-bottom: 10px; color: #900000; text-decoration: none; font-weight: bold; }
        a.voltar:hover { text-decoration: underline; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 6px; }
        .alert.error { background: #ffe6e6; color: #900; }
        .alert.success { background: #e6ffe6; color: #065; }
        .detail { background: #f8f8f8; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
           footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }
        .btn-perfil { display: inline-block; margin-top: 8px; background: #900000; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px; transition: all 0.3s; }
        .btn-perfil:hover { background: #e60000; }
    </style>
</head>

<body>

<header>
    <img src="../servigeralateral.png" alt="ServiGera">
</header>

<main>
    <div class="container">
        <a href="solicitacoes_abertas.php" class="voltar">← Voltar</a>

        <?php if ($erro): ?>
            <div class="alert error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <div class="detail">
            <strong>Título:</strong> <?= htmlspecialchars($sol['titulo']) ?><br>
            <strong>Descrição:</strong><br> <?= nl2br(htmlspecialchars($sol['descricao'])) ?><br>
            <strong>Cliente:</strong> <?= htmlspecialchars($sol['nome_cliente'] ?? '—') ?><br>

            <?php if (!empty($sol['IDcliente'])): ?>
                <a href="ver_cliente.php?id=<?= $sol['IDcliente'] ?>" class="btn-perfil">Ver perfil do cliente</a>
            <?php endif; ?>
        </div>

        <?php if ($jaEnviou): ?>
            <div class="alert error">Você já enviou uma proposta para esta solicitação.</div>
        <?php else: ?>
            <form method="post" action="">
                <label for="mensagem">Mensagem / Proposta</label>
                <textarea id="mensagem" name="mensagem" rows="6" required><?= isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : '' ?></textarea>
                <input type="hidden" name="IDsolicitacao" value="<?= $IDsolicitacao ?>">
                <button type="submit">Enviar Proposta</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>
</html>