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
if ($IDsolicitacao <= 0) die("Solicitação inválida.");

// Buscar a solicitação com informações do cliente
$sqlDet = "
    SELECT s.*, c.nome AS nome_cliente, c.IDcliente
    FROM solicitacoes s
    LEFT JOIN cliente c ON s.IDcliente = c.IDcliente
    WHERE s.IDsolicitacao = ?
";
$stmtDet = mysqli_prepare($db, $sqlDet);
mysqli_stmt_bind_param($stmtDet, "i", $IDsolicitacao);
mysqli_stmt_execute($stmtDet);
$resDet = mysqli_stmt_get_result($stmtDet);
$sol = mysqli_fetch_assoc($resDet);
if (!$sol) die("Solicitação não encontrada.");

// Buscar propostas
$sqlProp = "
    SELECT p.IDproposta, p.IDtecnico, t.nome AS tecnico, t.foto, t.bio, p.mensagem, p.status
    FROM propostas p
    JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
    WHERE p.IDsolicitacao = ?
    ORDER BY p.criado_em DESC
";
$stmtProp = mysqli_prepare($db, $sqlProp);
mysqli_stmt_bind_param($stmtProp, "i", $IDsolicitacao);
mysqli_stmt_execute($stmtProp);
$resProp = mysqli_stmt_get_result($stmtProp);

// Verificar se o técnico possui proposta aceita
$sqlAceita = "
    SELECT 1 
    FROM propostas 
    WHERE IDsolicitacao = ? AND IDtecnico = ? AND LOWER(status) IN ('aceita','aceito','accepted')
";
$stmtAceita = mysqli_prepare($db, $sqlAceita);
mysqli_stmt_bind_param($stmtAceita, "ii", $IDsolicitacao, $idTecnico);
mysqli_stmt_execute($stmtAceita);
$resAceita = mysqli_stmt_get_result($stmtAceita);
$propostaAceitaPeloCliente = (mysqli_num_rows($resAceita) > 0);

// Finalizar solicitação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $sqlCheck = "SELECT cliente_finalizado, tecnico_finalizado FROM solicitacoes WHERE IDsolicitacao = ?";
    $stmtCheck = mysqli_prepare($db, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, "i", $IDsolicitacao);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $solCheck = mysqli_fetch_assoc($resCheck);

    if ($solCheck) {
        $tecnico_finalizado = 1;
        $cliente_finalizado = $solCheck['cliente_finalizado'];
        if ($cliente_finalizado && $tecnico_finalizado) {
    $status = 'Concluído';
} else {
    $status = 'Em Negociação';
}


        $sqlUpdate = "UPDATE solicitacoes SET tecnico_finalizado = ?, status = ? WHERE IDsolicitacao = ?";
        $stmtUpdate = mysqli_prepare($db, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "isi", $tecnico_finalizado, $status, $IDsolicitacao);
        mysqli_stmt_execute($stmtUpdate);

        header("Location: visualizar_solicitacao_tecnico.php?id=$IDsolicitacao");
        exit;
    }
}

// EXCLUIR PROPOSTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_proposta'])) {
    $idProposta = intval($_POST['excluir_proposta']);

    // Confirma que a proposta pertence ao técnico logado
    $sqlCheckProp = "SELECT IDproposta FROM propostas WHERE IDproposta = ? AND IDtecnico = ?";
    $stmtCheck = mysqli_prepare($db, $sqlCheckProp);
    mysqli_stmt_bind_param($stmtCheck, "ii", $idProposta, $idTecnico);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);

    if (mysqli_num_rows($resCheck) === 0) {
        die("Você não tem permissão para excluir esta proposta.");
    }

    // Agora pode excluir
    $sqlDel = "DELETE FROM propostas WHERE IDproposta = ?";
    $stmtDel = mysqli_prepare($db, $sqlDel);
    mysqli_stmt_bind_param($stmtDel, "i", $idProposta);
    mysqli_stmt_execute($stmtDel);

    // Redireciona para limpar POST e atualizar lista
    header("Location: visualizar_solicitacao_tecnico.php?id=$IDsolicitacao");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Visualizar Solicitação - ServiGera</title>
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    scroll-behavior: smooth;
}

/* Estrutura global */
html, body {
    height: 100%;
    font-family: "Arial", sans-serif;
    background: #e60000 url("../fundo.png") no-repeat center center fixed;
    background-size: cover;
    color: #333;
    display: flex;
    flex-direction: column;
}

/* Cabeçalho */
header {
    background-color: #900000;
    color: #fff;
    padding: 20px;
    text-align: center;
    position: relative;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}
header img {
    height: 80px;
    width: auto;
    border-radius: 10px;
}

/* Conteúdo principal */
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
    text-align: left;
}

/* Títulos e textos */
h1, h2 {
    color: #900000;
    margin-bottom: 15px;
}
p {
    margin-bottom: 10px;
    line-height: 1.6;
}

/* Links */
a.voltar {
    display: inline-block;
    text-decoration: none;
    color: #e60000;
    margin-bottom: 20px;
    font-weight: bold;
}
a.voltar:hover { text-decoration: underline; }

/* Botões */
button {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    color: #fff;
    cursor: pointer;
    margin-top: 8px;
    transition: all 0.2s ease-in-out;
    background-color: #008000;
}
button:hover { background-color: #006600; }

.btn-perfil {
    display: inline-block;
    margin-top: 8px;
    background: #900000;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
}
.btn-perfil:hover { background: #e60000; }

/* Detalhes e propostas */
.detail {
    background: #fff;
    border: 2px solid #e60000;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.detail strong { color: #900000; }

/* Alertas */
.alert {
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    text-align: center;
}
.alert.success {
    background: #e6ffe6;
    color: #065;
}
.alert {
    background: #f2f2f2;
    color: #555;
}

/* Rodapé fixo */
footer {
    background-color: #900000;
    color: #fff;
    padding: 12px;
    text-align: center;
    font-size: 0.95rem;
    box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
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
</header>

<main>
<div class="container">
    <a href="tecnico_home.php" class="voltar">← Voltar</a>

    <div class="detail">
        <strong>Título:</strong> <?= htmlspecialchars($sol['titulo']) ?><br>
        <strong>Descrição:</strong><br> <?= nl2br(htmlspecialchars($sol['descricao'])) ?><br>
        <strong>Cliente:</strong> <?= htmlspecialchars($sol['nome_cliente'] ?? '—') ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($sol['status'] ?: 'Concluído') ?><br>
        <?php if (!empty($sol['IDcliente'])): ?>
            <a href="ver_cliente.php?id=<?= $sol['IDcliente'] ?>" class="btn-perfil">Ver perfil do cliente</a>
        <?php endif; ?>
    </div>

    <?php if ($propostaAceitaPeloCliente && !$sol['tecnico_finalizado']): ?>
        <form method="post">
            <input type="hidden" name="finalizar" value="1">
            <button type="submit">Marcar como Finalizado</button>
        </form>
    <?php elseif ($propostaAceitaPeloCliente && $sol['tecnico_finalizado']): ?>
        <?php
            $sqlCheckFinal = "SELECT cliente_finalizado, tecnico_finalizado, status FROM solicitacoes WHERE IDsolicitacao = ?";
            $stmtCheckFinal = mysqli_prepare($db, $sqlCheckFinal);
            mysqli_stmt_bind_param($stmtCheckFinal, "i", $IDsolicitacao);
            mysqli_stmt_execute($stmtCheckFinal);
            $resCheckFinal = mysqli_stmt_get_result($stmtCheckFinal);
            $finalData = mysqli_fetch_assoc($resCheckFinal);

            $cliente_finalizado = $finalData['cliente_finalizado'] ?? 0;
            $tecnico_finalizado = $finalData['tecnico_finalizado'] ?? 0;
            $statusAtual = strtolower($finalData['status'] ?? '');
        ?>
        <?php if (($cliente_finalizado && $tecnico_finalizado) || in_array($statusAtual, ['concluído','finalizado'])): ?>
            <div class="alert success">Solicitação finalizada com sucesso!</div>
        <?php else: ?>
            <div class="alert">Aguardando finalização pelo cliente...</div>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Propostas</h2>
    <?php if($resProp && mysqli_num_rows($resProp) > 0): ?>
        <?php while($p = mysqli_fetch_assoc($resProp)): ?>
            <div class="detail">
                <strong><?= htmlspecialchars($p['tecnico']); ?></strong><br>
                <?= nl2br(htmlspecialchars($p['mensagem'])); ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($p['status'] ?: 'Concluído'); ?><br>
                <?php if ($p['IDtecnico'] == $idTecnico): ?>
                    <form method="post" style="margin-top: 6px;">
                        <input type="hidden" name="excluir_proposta" value="<?= $p['IDproposta']; ?>">
                        <button type="submit" onclick="return confirm('Deseja realmente excluir esta proposta?');">Excluir Proposta</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert">Não há propostas nesta solicitação.</div>
    <?php endif; ?>
</div>
</main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>

</body>
</html>
