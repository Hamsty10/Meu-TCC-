<?php
session_start();
include("verifica.php"); // Verifica se está logado
verifica_tipo('A'); // Apenas admins
include("conexao.php"); // Conexão com o banco

// Verifica se o ID da solicitação foi enviado via GET ou POST
if (isset($_GET['id'])) {
  $idSolicitacao = intval($_GET['id']); // Garante que é um número inteiro
} else {
  die("ID da solicitação não informado.");
}

// Prepara o DELETE para evitar SQL Injection
$sql = "DELETE FROM solicitacoes WHERE IDsolicitacao = ?";
$stmt = mysqli_prepare($id, $sql);
if ($stmt) {
  mysqli_stmt_bind_param($stmt, "i", $idSolicitacao);
  if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    // Redireciona para a página de lista de solicitações ou mostra mensagem
    header("Location: solicitacoes_lista.php?msg=excluido");
    exit();
  } else {
    die("Erro ao excluir solicitação: " . mysqli_error($id));
  }
} else {
  die("Erro na preparação da query: " . mysqli_error($id));
}
