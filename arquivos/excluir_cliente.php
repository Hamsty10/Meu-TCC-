<?php
session_start();
include("verifica.php");
verifica_tipo('C'); // apenas clientes
include("conexao.php");

$idCliente = $_SESSION['id_usuario'];

// Exclui propostas relacionadas às solicitações do cliente
$sql = "DELETE p FROM propostas p 
        INNER JOIN solicitacoes s ON p.IDsolicitacao = s.IDsolicitacao 
        WHERE s.IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);

// Exclui solicitações do cliente
$sql = "DELETE FROM solicitacoes WHERE IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);

// Exclui da tabela cliente
$sql = "DELETE FROM cliente WHERE IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);

// Exclui também da tabela usuarios
$sql = "DELETE FROM usuarios WHERE IDusuario = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);

if (mysqli_stmt_execute($stmt)) {
    session_destroy();
    header("Location: escolher_tipo.php");
    exit;
} else {
    echo "<p>Erro ao excluir conta.</p>";
}
?>
