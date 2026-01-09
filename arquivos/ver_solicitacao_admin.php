<?php
session_start();
include("verifica.php");
include("conexao.php");

// Garante que é admin
verifica_tipo('A');

// Pega o ID da solicitação via GET
$IDsolicitacao = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($IDsolicitacao <= 0) {
  die("Solicitação inválida.");
}

// Consulta detalhes da solicitação + cliente + especialidade
$sql = "
SELECT 
    s.IDsolicitacao,
    s.titulo,
    s.descricao,
    s.status,
    s.data_criacao,
    c.nome AS nome_cliente,
    c.email AS email_cliente,
    c.telefone AS telefone_cliente,
    GROUP_CONCAT(DISTINCT e.nome SEPARATOR ', ') AS nome_especialidade
FROM solicitacoes s
LEFT JOIN cliente c ON s.IDcliente = c.IDcliente
LEFT JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao
LEFT JOIN tecnicos t ON t.IDtecnico = p.IDtecnico
LEFT JOIN tecnico_especialidade te ON te.IDtecnico = t.IDtecnico
LEFT JOIN especialidades e ON e.IDespecialidade = te.IDespecialidade
WHERE s.IDsolicitacao = ?
GROUP BY s.IDsolicitacao

";

$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $IDsolicitacao);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$sol = mysqli_fetch_assoc($res);

if (!$sol) {
  die("Solicitação não encontrada.");
}

// Busca técnicos que enviaram propostas para essa solicitação
$sqlTec = "
SELECT t.nome, t.email
FROM propostas p
INNER JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
WHERE p.IDsolicitacao = ?
";
$stmtTec = mysqli_prepare($id, $sqlTec);
mysqli_stmt_bind_param($stmtTec, "i", $IDsolicitacao);
mysqli_stmt_execute($stmtTec);
$resTec = mysqli_stmt_get_result($stmtTec);
$tecnicos = [];
while ($row = mysqli_fetch_assoc($resTec)) {
  $tecnicos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Ver Solicitação - Servigera</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html,
    body {
      height: 100%;
      font-family: Arial, sans-serif;
      background: #e60000 url("../fundo.png") no-repeat center center fixed;
      background-size: cover;
      color: #333;
      display: flex;
      flex-direction: column;
    }

    header {
      background-color: #900000;
      color: #fff;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    header h1 {
      margin: 0;
    }

    header nav {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    header nav a.logout,
    header nav a.voltar {
      color: #fff;
      background-color: #cc0000;
      padding: 8px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      margin-left: 5px;
    }

    header nav a.logout:hover,
    header nav a.voltar:hover {
      background-color: #ff3333;
    }

    main {
      flex: 1;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      max-width: 800px;
      width: 100%;
    }

    h2 {
      color: #900000;
      margin-bottom: 20px;
      text-align: center;
    }

    .detail {
      background: #f8f8f8;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .detail strong {
      color: #e60000;
    }

    .btn {
      display: inline-block;
      padding: 8px 15px;
      background-color: #e60000;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
      margin-top: 10px;
    }

    .btn:hover {
      opacity: 0.8;
    }

    footer {
      background-color: #900000;
      color: #fff;
      padding: 12px;
      text-align: center;
      font-size: 0.95rem;
      box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
    }
  </style>
</head>

<body>
  <header>
    <h1>Ver Solicitação</h1>
    <nav>

      <a class="logout" href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">
      <h2>Detalhes da Solicitação</h2>
      <div class="detail">
        <strong>ID:</strong> <?= $sol['IDsolicitacao'] ?><br>
        <strong>Título:</strong> <?= htmlspecialchars($sol['titulo']) ?><br>
        <strong>Descrição:</strong><br> <?= nl2br(htmlspecialchars($sol['descricao'])) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($sol['status']) ?><br>
        <strong>Data de Criação:</strong> <?= htmlspecialchars($sol['data_criacao']) ?><br>
        <strong>Especialidade:</strong> <?= htmlspecialchars($sol['nome_especialidade'] ?? '—') ?><br>
      </div>

      <h2>Informações do Cliente</h2>
      <div class="detail">
        <strong>Nome:</strong> <?= htmlspecialchars($sol['nome_cliente'] ?? '—') ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($sol['email_cliente'] ?? '—') ?><br>
        <strong>Telefone:</strong> <?= htmlspecialchars($sol['telefone_cliente'] ?? '—') ?><br>
      </div>

      <h2>Técnicos que enviaram propostas</h2>
      <div class="detail">
        <?php if (count($tecnicos) > 0): ?>
          <?php foreach ($tecnicos as $tec): ?>
            <strong>Nome:</strong> <?= htmlspecialchars($tec['nome']) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($tec['email']) ?><br>
            <hr>
          <?php endforeach; ?>
        <?php else: ?>
          Nenhum técnico enviou proposta ainda.
        <?php endif; ?>
      </div>

      <a href="solicitacoes_lista.php" class="btn">Voltar para Lista</a>
    </div>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados.
  </footer>
</body>

</html>