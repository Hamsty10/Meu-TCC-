<?php
session_start();
include("verifica.php");
verifica_tipo('C');
include("conexao.php");

$idCliente = $_SESSION['id_usuario'] ?? null;
if (!$idCliente) die("Sessão inválida.");

$idSolicitacao = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idSolicitacao <= 0) die("Solicitação inválida.");

// Finalizar solicitação (cliente)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $sqlCheck = "SELECT cliente_finalizado, tecnico_finalizado FROM solicitacoes WHERE IDsolicitacao = ? AND IDcliente = ?";
    $stmtCheck = mysqli_prepare($id, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, "ii", $idSolicitacao, $idCliente);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $solCheck = mysqli_fetch_assoc($resCheck);

    if ($solCheck) {
        $cliente_finalizado = 1;
        $tecnico_finalizado = $solCheck['tecnico_finalizado'];
        $status = ($cliente_finalizado && $tecnico_finalizado) ? 'Concluído' : 'Em Negociação';


        $sqlUpdate = "UPDATE solicitacoes SET cliente_finalizado = ?, status = ? WHERE IDsolicitacao = ?";
        $stmtUpdate = mysqli_prepare($id, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "isi", $cliente_finalizado, $status, $idSolicitacao);
        mysqli_stmt_execute($stmtUpdate);

        if ($status === 'Concluído') {
            header("Location: avaliar_tecnico.php?id=" . $idSolicitacao);
            exit;
        }

        header("Location: visualizar_solicitacao.php?id=" . $idSolicitacao);
        exit;
    }
}

// Buscar solicitação
$sql = "SELECT * FROM solicitacoes WHERE IDsolicitacao = ? AND IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "ii", $idSolicitacao, $idCliente);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$sol = mysqli_fetch_assoc($res);
if (!$sol) die("Solicitação não encontrada.");

// Buscar propostas
$sqlProp = "SELECT p.IDproposta, p.IDtecnico, t.nome AS tecnico, t.foto, t.bio, p.mensagem, p.status, t.telefone
            FROM propostas p
            JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
            WHERE p.IDsolicitacao = ?
            ORDER BY p.criado_em DESC";
$stmtProp = mysqli_prepare($id, $sqlProp);
mysqli_stmt_bind_param($stmtProp, "i", $idSolicitacao);
mysqli_stmt_execute($stmtProp);
$resProp = mysqli_stmt_get_result($stmtProp);

// Verifica se há proposta aceita
$propostaAceita = false;
while ($rowTemp = mysqli_fetch_assoc($resProp)) {
    if (strtolower($rowTemp['status']) === 'aceita') {
        $propostaAceita = true;
        $tecnicoAceito = $rowTemp;
        break;
    }
}
mysqli_data_seek($resProp, 0);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Visualizar Solicitação - ServiGera</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;scroll-behavior:smooth;}
html,body{height:100%;font-family:"Arial",sans-serif;background:#e60000 url("../fundo.png")no-repeat center center fixed;background-size:cover;color:#333;display:flex;flex-direction:column;}
header{background-color:#900000;color:#fff;padding:20px;text-align:center;position:relative;box-shadow:0 4px 10px rgba(0,0,0,0.3);}
header img{height:80px;width:auto;border-radius:10px;}
header nav{position:absolute;top:20px;right:20px;}
header nav a.logout{color:#fff;background-color:#cc0000;padding:8px 15px;border-radius:8px;text-decoration:none;font-weight:bold;transition:0.3s;}
header nav a.logout:hover{background-color:#ff3333;}
main{flex:1;display:flex;justify-content:center;align-items:flex-start;padding:40px 20px;}
.container{background-color:#fff;padding:30px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,0.3);max-width:900px;width:100%;}
h1,h2{color:#900000;margin-bottom:15px;}
p{margin-bottom:10px;line-height:1.6;}
a.voltar{display:inline-block;text-decoration:none;color:#e60000;margin-bottom:20px;font-weight:bold;}
a.voltar:hover{text-decoration:underline;}
button,.btn{display:inline-block;padding:10px 16px;border-radius:8px;border:none;font-weight:bold;color:#fff;cursor:pointer;margin-top:8px;transition:all 0.2s ease-in-out;}
.btn-accept{background:#008000;}
.btn-accept:hover{background:#006600;}
.btn-rej{background:#cc0000;}
.btn-rej:hover{background:#a00000;}
.btn-wa{background:#25D366;}
.btn-wa:hover{background:#1eb056;}
.btn-profile{background:#007bff;}
.btn-profile:hover{background:#0056b3;}
.detail{background:#fff;border:2px solid #e60000;padding:15px;margin-bottom:15px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.alert{padding:10px;margin:10px 0;border-radius:6px;text-align:center;}
.alert.success{background:#e6ffe6;color:#065;}
.alert{background:#f2f2f2;color:#555;}
footer{background-color:#900000;color:#fff;padding:12px;text-align:center;font-size:0.95rem;box-shadow:0 -4px 10px rgba(0,0,0,0.25);position:fixed;bottom:0;left:0;width:100%;}
.foto-tecnico{width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e60000;margin-bottom:10px;}
</style>
</head>
<body>
<header>
    <img src="../servigeralateral.png" alt="ServiGera">
    <nav><a class="logout" href="logout.php">Sair</a></nav>
</header>

<main>
<div class="container">
    <a href="cliente_home.php" class="voltar">← Voltar</a>

    <div class="detail">
        <strong>Título:</strong> <?= htmlspecialchars($sol['titulo']); ?><br>
        <strong>Descrição:</strong><br> <?= nl2br(htmlspecialchars($sol['descricao'])); ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($sol['status'] ?: 'Concluído'); ?><br>
    </div>

    <?php if ($propostaAceita && !$sol['cliente_finalizado']): ?>
        <form method="post">
            <input type="hidden" name="finalizar" value="1">
            <button type="submit" class="btn-accept">Marcar como Finalizado</button>
        </form>
    <?php elseif ($propostaAceita && $sol['cliente_finalizado']): ?>
        <?php
            $sqlCheckFinal = "SELECT cliente_finalizado, tecnico_finalizado, status FROM solicitacoes WHERE IDsolicitacao = ?";
            $stmtCheckFinal = mysqli_prepare($id, $sqlCheckFinal);
            mysqli_stmt_bind_param($stmtCheckFinal, "i", $idSolicitacao);
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
            <div class="alert">Aguardando finalização pelo técnico...</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert">Finalize disponível apenas após aceitar uma proposta.</div>
    <?php endif; ?>

    <h2>Propostas Recebidas</h2>
    <?php if(mysqli_num_rows($resProp) > 0): ?>
        <?php while($p = mysqli_fetch_assoc($resProp)): ?>
            <div class="detail">
                <?php if (!empty($p['foto'])): ?>
                    <img class="foto-tecnico" src="data:image/jpeg;base64,<?= base64_encode($p['foto']); ?>" alt="Foto do Técnico">
                <?php endif; ?>
                
                <strong><?= htmlspecialchars($p['tecnico']); ?></strong><br>
                <?= nl2br(htmlspecialchars($p['mensagem'])); ?><br>
                <strong>Status:</strong> <?= htmlspecialchars($p['status'] ?: 'Concluído'); ?><br>

                <a class="btn btn-profile" href="perfil_tecnico.php?id=<?= $p['IDtecnico']; ?>">Ver Perfil do Técnico</a>

                <?php if ($p['status'] === 'aceita'): ?>
                    <?php
                        $wh = preg_replace('/\D/', '', $p['telefone'] ?? '');
                        if ($wh && strlen($wh) >= 8) {
                            $msg = urlencode("Olá! Estou entrando em contato sobre a solicitação aceita no ServiGera.");
                            echo "<a class='btn btn-wa' target='_blank' href='https://wa.me/{$wh}?text={$msg}'>Conversar no WhatsApp</a>";
                        } else {
                            echo "<p class='small'>Técnico sem número cadastrado.</p>";
                        }
                    ?>
                <?php elseif ($p['status'] === 'pendente'): ?>
                    <form method="post" action="aceitar_proposta.php" style="display:inline;">
                        <input type="hidden" name="id_proposta" value="<?= $p['IDproposta']; ?>">
                        <button type="submit" class="btn-accept">Aceitar</button>
                    </form>
                    <form method="post" action="atualizar_proposta.php" style="display:inline;">
                        <input type="hidden" name="id_proposta" value="<?= $p['IDproposta']; ?>">
                        <input type="hidden" name="acao" value="rejeitar">
                        <button type="submit" class="btn-rej">Rejeitar</button>
                    </form>
                <?php else: ?>
                    <p class="small">Proposta rejeitada.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert">Nenhuma proposta recebida ainda.</div>
    <?php endif; ?>
</div>
</main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>
</html>
