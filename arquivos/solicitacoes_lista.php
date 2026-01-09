<?php
session_start();
include("verifica.php");
include("conexao.php");

// Garante que é admin
verifica_tipo('A');

// Consulta todas as solicitações com nome do cliente e da especialidade
$sql = "SELECT 
    s.IDsolicitacao,
    s.titulo,
    s.descricao,
    s.data_criacao,
    s.status,
    c.nome AS nome_cliente,
    GROUP_CONCAT(DISTINCT e.nome SEPARATOR ', ') AS nome_especialidade
FROM solicitacoes s
LEFT JOIN cliente c ON s.IDcliente = c.IDcliente

-- pega propostas
LEFT JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao

-- pega técnicos das propostas
LEFT JOIN tecnicos t ON t.IDtecnico = p.IDtecnico

-- pega especialidades dos técnicos
LEFT JOIN tecnico_especialidade te ON te.IDtecnico = t.IDtecnico
LEFT JOIN especialidades e ON e.IDespecialidade = te.IDespecialidade

GROUP BY s.IDsolicitacao
ORDER BY s.IDsolicitacao DESC";

$result = mysqli_query($id, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Lista de Solicitações - Servigera</title>
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
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
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
    }

    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      max-width: 1100px;
      width: 100%;
      overflow-x: auto;
    }

    h2 {
      color: #900000;
      margin-bottom: 20px;
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
    }

    table th,
    table td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: left;
    }

    table th {
      background-color: #f5f5f5;
      color: #900000;
    }

    table tr:hover {
      background-color: #f0f0f0;
    }

    a.btn {
      display: inline-block;
      padding: 8px 15px;
      background-color: #e60000;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
      margin-right: 5px;
    }

    a.btn:hover {
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

    .status {
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 5px;
      color: #000;
      /* cor do texto alterada para preta */
    }

    .status.pendente {
      background: #ff9900;
    }

    .status.concluida {
      background: #009933;
    }

    .status.cancelada {
      background: #666;
    }
  </style>
</head>

<body>
  <header>
    <h1>Lista de Solicitações</h1>
    <nav>
      <a class="voltar" href="admin_home.php">Voltar</a>
      <a class="logout" href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">
      <h2>Solicitações Cadastradas</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Cliente</th>
          <th>Data</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
        <?php while ($sol = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $sol['IDsolicitacao']; ?></td>
            <td><?= htmlspecialchars($sol['titulo']); ?></td>
            <td><?= htmlspecialchars($sol['nome_cliente'] ?? '—'); ?></td>
            <td><?= date("d/m/Y H:i", strtotime($sol['data_criacao'])); ?></td>
            <td>
              <span class="status <?= strtolower($sol['status']); ?>">
                <?= ucfirst($sol['status']); ?>
              </span>
            </td>
            <td>
              <a class="btn" href="ver_solicitacao_admin.php?id=<?= $sol['IDsolicitacao']; ?>">Ver</a>

              <a class="btn" href="editar_solicitacao_admin.php?id=<?= $sol['IDsolicitacao']; ?>">Editar</a>

              <a class="btn" href="excluir_solicitacao_admin.php?id=<?= $sol['IDsolicitacao']; ?>"
                onclick="return confirm('Tem certeza que deseja excluir esta solicitação?')">Excluir</a>
            </td>

          </tr>
        <?php } ?>
      </table>
    </div>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados.
  </footer>
</body>

</html>