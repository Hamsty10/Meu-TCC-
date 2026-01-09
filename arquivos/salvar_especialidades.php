<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // apenas técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Pega especialidades enviadas pelo formulário
$especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];

// Remove todas as especialidades atuais do técnico
$sql = "DELETE FROM tecnico_especialidade WHERE IDtecnico = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);

// Insere as novas especialidades selecionadas
if (!empty($especialidades)) {
    $sql = "INSERT INTO tecnico_especialidade (IDtecnico, IDespecialidade) VALUES (?, ?)";
    $stmt = mysqli_prepare($id, $sql);

    foreach ($especialidades as $esp) {
        mysqli_stmt_bind_param($stmt, "ii", $idTecnico, $esp);
        mysqli_stmt_execute($stmt);
    }
}

// Redireciona de volta
header("Location: tecnico_home.php");
exit;