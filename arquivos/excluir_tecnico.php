<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // apenas técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Exclui propostas do técnico
$sql = "DELETE FROM propostas WHERE IDtecnico = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);

// Exclui especialidades do técnico
$sql = "DELETE FROM tecnico_especialidade WHERE IDtecnico = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);

// Exclui da tabela tecnicos
$sql = "DELETE FROM tecnicos WHERE IDtecnico = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);

// Exclui também da tabela usuarios
$sql = "DELETE FROM usuarios WHERE IDusuario = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);

if (mysqli_stmt_execute($stmt)) {
    session_destroy();
    header("Location: escolher_tipo.php");
    exit;
} else {
    echo "<p>Erro ao excluir conta.</p>";
}
?>
