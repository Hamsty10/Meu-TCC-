<?php
session_start();
include("conexao.php");

// Pegando dados do usuário e da solicitação
$idSolicitacao = intval($_POST['idSolicitacao']);
$tipoUsuario = $_SESSION['tipo_usuario']; // 'C' ou 'T'

// Busca a solicitação atual
$sql = "SELECT status, cliente_finalizado, tecnico_finalizado FROM solicitacoes WHERE IDsolicitacao = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $idSolicitacao);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)) {
    $cliente_finalizado = $row['cliente_finalizado'];
    $tecnico_finalizado = $row['tecnico_finalizado'];
    $status = $row['status'];

    // Atualiza o campo do usuário que marcou como finalizado
    if($tipoUsuario == 'C') {
        $cliente_finalizado = 1;
    } elseif($tipoUsuario == 'T') {
        $tecnico_finalizado = 1;
    }

    // Se ambos marcaram, muda o status real
    if($cliente_finalizado && $tecnico_finalizado) {
        $status = 'finalizado';
    } else {
        $status = 'aberto';
    }

    // Atualiza no banco
    $sqlUpdate = "UPDATE solicitacoes SET status = ?, cliente_finalizado = ?, tecnico_finalizado = ? WHERE IDsolicitacao = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "siii", $status, $cliente_finalizado, $tecnico_finalizado, $idSolicitacao);
    mysqli_stmt_execute($stmtUpdate);

    echo json_encode(["sucesso" => true, "status" => $status]);
} else {
    echo json_encode(["sucesso" => false, "mensagem" => "Solicitação não encontrada."]);
}
?>
