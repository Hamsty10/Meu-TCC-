<?php
session_start();
include("conexao.php");
include("verifica.php");
verifica_tipo('C'); 

$idCliente = $_SESSION['id_usuario'] ?? null;
$idSolicitacao = intval($_POST['id_solicitacao'] ?? 0);

header('Content-Type: application/json; charset=utf-8');

if (!$idCliente || !$idSolicitacao) {
    echo json_encode(["sucesso" => false, "mensagem" => "Dados inválidos."]);
    exit;
}

// Verifica se a conexão existe
if (!isset($id) || !$id) {
    echo json_encode(["sucesso" => false, "mensagem" => "Conexão com o banco não encontrada."]);
    exit;
}

// Busca a solicitação
$sql = "SELECT status, cliente_finalizado, tecnico_finalizado FROM solicitacoes WHERE IDsolicitacao = ? AND IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
if (!$stmt) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao preparar consulta: " . mysqli_error($id)]);
    exit;
}
mysqli_stmt_bind_param($stmt, "ii", $idSolicitacao, $idCliente);
if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao executar consulta: " . mysqli_stmt_error($stmt)]);
    exit;
}
$res = mysqli_stmt_get_result($stmt);
$sol = mysqli_fetch_assoc($res);

if (!$sol) {
    echo json_encode(["sucesso" => false, "mensagem" => "Solicitação não encontrada ou não pertence a este cliente."]);
    exit;
}

// Marca como finalizado pelo cliente
$cliente_finalizado = 1;
$tecnico_finalizado = intval($sol['tecnico_finalizado']); // garante int 0/1

// Se ambos finalizaram, muda status — aqui padronizei para 'Concluído' (maiusc.) como no outro arquivo
$status = ($cliente_finalizado && $tecnico_finalizado) ? 'Concluído' : 'Aberto';

// Atualiza
$sqlUpdate = "UPDATE solicitacoes SET cliente_finalizado = ?, status = ? WHERE IDsolicitacao = ?";
$stmtUpdate = mysqli_prepare($id, $sqlUpdate);
if (!$stmtUpdate) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao preparar update: " . mysqli_error($id)]);
    exit;
}
mysqli_stmt_bind_param($stmtUpdate, "isi", $cliente_finalizado, $status, $idSolicitacao);
if (!mysqli_stmt_execute($stmtUpdate)) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao executar update: " . mysqli_stmt_error($stmtUpdate)]);
    exit;
}

// Verifica se foi atualizado
$affected = mysqli_stmt_affected_rows($stmtUpdate);

echo json_encode([
    "sucesso" => true,
    "status" => $status,
    "affected_rows" => $affected
]);
exit;
