<?php
session_start();
include("verifica.php");
verifica_tipo('C');
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProposta = intval($_POST['id_proposta']);
    $acao = $_POST['acao'];

    if (!in_array($acao, ['aceitar', 'rejeitar'])) {
        die("Ação inválida.");
    }

    $novoStatus = ($acao === 'aceitar') ? 'aceita' : 'rejeitada';

    // Atualiza o status da proposta
    $sql = "UPDATE propostas SET status = ? WHERE IDproposta = ?";
    $stmt = mysqli_prepare($id, $sql);
    mysqli_stmt_bind_param($stmt, "si", $novoStatus, $idProposta);
    mysqli_stmt_execute($stmt);

    // Redireciona de volta
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
