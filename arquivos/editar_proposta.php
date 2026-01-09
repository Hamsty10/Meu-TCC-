<?php
session_start();
include("verifica.php");
include("conexao.php");

verifica_tipo('A');

// Verifica ID
if (!isset($_GET['id'])) {
  die("ID da proposta não informado.");
}

$idProposta = intval($_GET['id']);

// Buscar dados da proposta
$sql = "SELECT p.*, 
        t.nome AS tecnico,
        s.titulo AS titulo_solicitacao
        FROM propostas p
        LEFT JOIN tecnicos t ON t.IDtecnico = p.IDtecnico
        LEFT JOIN solicitacoes s ON s.IDsolicitacao = p.IDsolicitacao
        WHERE p.IDproposta = ?";

$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idProposta);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
  die("Proposta não encontrada.");
}

$prop = mysqli_fetch_assoc($result);

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mensagem = $_POST['mensagem'];
  $status = $_POST['status'];

  $sqlUpdate = "UPDATE propostas 
                  SET mensagem = ?, status = ?
                  WHERE IDproposta = ?";
  $stmtUp = mysqli_prepare($id, $sqlUpdate);
  mysqli_stmt_bind_param($stmtUp, "ssi", $mensagem, $status, $idProposta);

  if (mysqli_stmt_execute($stmtUp)) {
    header("Location: propostas_lista.php?edit_sucesso=1");
    exit;
  } else {
    echo "Erro ao atualizar.";
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Editar Proposta - Servigera</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial;
      background: #e60000 url("../fundo.png") no-repeat center center fixed;
      background-size: cover;
    }

    header {
      background: #900000;
      padding: 20px;
      text-align: center;
      color: white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    header nav {
      position: absolute;
      right: 20px;
      top: 20px;
    }

    header nav a {
      padding: 8px 15px;
      background: #cc0000;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      margin-left: 5px;
    }

    header nav a:hover {
      background: #ff3333;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    textarea {
      height: 120px;
    }

    .btn {
      margin-top: 20px;
      display: block;
      width: 100%;
      background: #e60000;
      color: white;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
      cursor: pointer;
    }

    .btn:hover {
      background: #ff0000;
    }
  </style>
</head>

<body>

  <header>
    <h1>Editar Proposta</h1>
    <nav>
      <a href="propostas_lista.php">Voltar</a>
      <a href="logout.php">Sair</a>
    </nav>
  </header>

  <div class="container">

    <h2 style="color:#900000; text-align:center; margin-bottom:20px;">
      Proposta #<?= $prop['IDproposta'] ?>
    </h2>

    <form method="post">

      <label>Solicitação</label>
      <input type="text" value="<?= htmlspecialchars($prop['titulo_solicitacao']); ?>" disabled>

      <label>Técnico</label>
      <input type="text" value="<?= htmlspecialchars($prop['tecnico']); ?>" disabled>

      <label>Mensagem da Proposta</label>
      <textarea name="mensagem"><?= htmlspecialchars($prop['mensagem']); ?></textarea>

      <label>Status</label>
      <select name="status">
        <option value="pendente" <?= $prop['status'] == "pendente" ? "selected" : ""; ?>>Pendente</option>
        <option value="aceita" <?= $prop['status'] == "aceita" ? "selected" : ""; ?>>Aceita</option>
        <option value="rejeitada" <?= $prop['status'] == "rejeitada" ? "selected" : ""; ?>>Rejeitada</option>
      </select>

      <button class="btn" type="submit">Salvar Alterações</button>

    </form>

  </div>

</body>

</html>