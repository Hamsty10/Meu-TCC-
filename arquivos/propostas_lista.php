<?php
session_start();
include("verifica.php");
include("conexao.php");

// Garante que é admin
verifica_tipo('A');

$sql = "SELECT p.IDproposta, s.IDsolicitacao, t.nome AS tecnico, 
               p.mensagem, p.status, p.criado_em
        FROM propostas p
        LEFT JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
        LEFT JOIN solicitacoes s ON p.IDsolicitacao = s.IDsolicitacao
        ORDER BY p.criado_em DESC";
$result = mysqli_query($id, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Lista de Propostas - Servigera</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      font-family: Arial, sans-serif;
      background: #e60000 url("../fundo.png") no-repeat center center fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
    }

    /* HEADER */
    header {
      background-color: #900000;
      color: #fff;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
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

    header nav a {
      color: #fff;
      background-color: #cc0000;
      padding: 8px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      margin-left: 5px;
      transition: 0.3s;
    }

    header nav a:hover {
      background-color: #ff3333;
    }

    /* MAIN */
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      padding: 30px 20px;
    }

    .container {
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 1200px;
      overflow-x: auto;
      animation: fadeIn .7s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      color: #900000;
      margin-bottom: 20px;
      text-align: center;
    }

    /* TABELA */
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 900px;
    }

    table th {
      background: #f5f5f5;
      color: #900000;
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    table td {
      padding: 12px;
      border: 1px solid #ccc;
    }

    table tr:hover {
      background: #f0f0f0;
    }

    /* BOTÕES */
    .btn {
      padding: 8px 14px;
      background: #e60000;
      color: white;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
      transition: .3s;
      margin-right: 5px;
      display: inline-block;
    }

    .btn:hover {
      background: #ff0000;
    }

    .btn-edit {
      background: #0077cc;
    }

    .btn-edit:hover {
      background: #1a8cff;
    }

    /* FOOTER */
    footer {
      background: #900000;
      color: #fff;
      padding: 12px;
      text-align: center;
      font-size: .95rem;
      box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.25);
    }
  </style>

</head>

<body>

  <header>
    <h1>Lista de Propostas</h1>
    <nav>
      <a class="voltar" href="admin_home.php">Voltar</a>
      <a class="logout" href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">

      <h2>Propostas Enviadas</h2>

      <table>
        <tr>
          <th>ID</th>
          <th>Solicitação</th>
          <th>Técnico</th>
          <th>Mensagem</th>
          <th>Status</th>
          <th>Criado em</th>
          <th>Ações</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['IDproposta']; ?></td>
            <td><?= $row['IDsolicitacao']; ?></td>
            <td><?= htmlspecialchars($row['tecnico'] ?? '—'); ?></td>
            <td><?= htmlspecialchars($row['mensagem']); ?></td>
            <td><?= htmlspecialchars($row['status']); ?></td>
            <td><?= date("d/m/Y H:i", strtotime($row['criado_em'])); ?></td>
            <td>

              <a class="btn"
                href="editar_proposta.php?id=<?= $row['IDproposta']; ?>">
                Editar
              </a>

              <a class="btn"
                href="excluir_proposta.php?id=<?= $row['IDproposta']; ?>"
                onclick="return confirm('Tem certeza que deseja excluir esta proposta?')">
                Excluir
              </a>

            </td>
          </tr>
        <?php } ?>
      </table>

    </div>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
  </footer>

</body>

</html>