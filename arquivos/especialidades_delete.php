<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

if (!isset($_GET['id'])) die("ID não informado.");
$idEsp = intval($_GET['id']);

$sql = "DELETE FROM especialidades WHERE IDespecialidade=?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idEsp);
mysqli_stmt_execute($stmt);

header("Location: especialidades_lista.php");
exit;
