<?php
session_start();
include("verifica.php");
verifica_tipo('C'); // apenas cliente
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idProposta = intval($_POST['id_proposta'] ?? 0);
    $idCliente = $_SESSION['id_usuario'] ?? 0;

    if (!$idProposta || !$idCliente) {
        die("Dados inválidos.");
    }

    // Busca a proposta e verifica se pertence a uma solicitação do cliente
    $sql = "SELECT p.IDsolicitacao, s.IDcliente 
            FROM propostas p 
            JOIN solicitacoes s ON p.IDsolicitacao = s.IDsolicitacao 
            WHERE p.IDproposta = ?";
    $stmt = mysqli_prepare($id, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProposta);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $proposta = mysqli_fetch_assoc($res);

    if (!$proposta || $proposta['IDcliente'] != $idCliente) {
        die("Acesso não autorizado.");
    }

    $idSolicitacao = $proposta['IDsolicitacao'];

    // Define todas as outras propostas como rejeitadas
    $sqlRejeita = "UPDATE propostas SET status = 'rejeitada' WHERE IDsolicitacao = ? AND IDproposta != ?";
    $stmtR = mysqli_prepare($id, $sqlRejeita);
    mysqli_stmt_bind_param($stmtR, "ii", $idSolicitacao, $idProposta);
    mysqli_stmt_execute($stmtR);

    // Define a proposta escolhida como aceita
    $sqlAceita = "UPDATE propostas SET status = 'aceita' WHERE IDproposta = ?";
    $stmtA = mysqli_prepare($id, $sqlAceita);
    mysqli_stmt_bind_param($stmtA, "i", $idProposta);
    mysqli_stmt_execute($stmtA);

    // Atualiza o status da solicitação para "Em Negociação"
    $sqlS = "UPDATE solicitacoes SET status = 'Em Negociação' WHERE IDsolicitacao = ?";
    $stmtS = mysqli_prepare($id, $sqlS);
    mysqli_stmt_bind_param($stmtS, "i", $idSolicitacao);
    mysqli_stmt_execute($stmtS);

    // Redireciona de volta para a página da solicitação
    header("Location: visualizar_solicitacao.php?id=" . $idSolicitacao);
    exit;
} else {
    die("Método inválido.");
}
?>
