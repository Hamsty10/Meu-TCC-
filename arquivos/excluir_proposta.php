<?php
session_start();
include("verifica.php");
include("conexao.php");

// Só admin
verifica_tipo('A');

// Pega id e valida
$id_proposta = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_proposta <= 0) {
  header("Location: propostas_lista.php?error=1");
  exit;
}

// Prepared statement para deletar
$sql = "DELETE FROM propostas WHERE IDproposta = ? LIMIT 1";
$stmt = mysqli_prepare($id, $sql);
if (!$stmt) {
  header("Location: propostas_lista.php?error=1");
  exit;
}

mysqli_stmt_bind_param($stmt, "i", $id_proposta);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
  mysqli_stmt_close($stmt);
  header("Location: propostas_lista.php?ok=1");
  exit;
} else {
  mysqli_stmt_close($stmt);
  header("Location: propostas_lista.php?error=1");
  exit;
}
