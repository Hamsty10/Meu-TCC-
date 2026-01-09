<?php
session_start();
include("verifica.php");
verifica_tipo('C'); // apenas cliente
include("conexao.php");

$idCliente = $_SESSION['id_usuario'];

// Pega o ID da solicitação
if(!isset($_GET['id'])){
    die("Solicitação não especificada.");
}

$idSolicitacao = intval($_GET['id']);

// Exclui a solicitação apenas se pertencer ao cliente
$sql = "DELETE FROM solicitacoes WHERE IDsolicitacao = ? AND IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "ii", $idSolicitacao, $idCliente);

if(mysqli_stmt_execute($stmt)){
    // Redireciona de volta para o cliente_home.php
    header("Location: cliente_home.php");
    exit;
} else {
    die("Erro ao excluir a solicitação.");
}
?>
