<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

$sql = "SELECT 
    a.IDAvaliacao,
    c.nome AS cliente,
    t.nome AS tecnico,
    a.nota,
    a.comentario,
    a.data_avaliacao
FROM avaliacoes a
LEFT JOIN solicitacoes s ON a.IDSolicitacao = s.IDsolicitacao
LEFT JOIN cliente c ON s.IDcliente = c.IDcliente
LEFT JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao AND p.status = 'aceita'
LEFT JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
ORDER BY a.data_avaliacao DESC
";
$result = mysqli_query($id, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Lista de Avaliações - Servigera</title>
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
  </style>
</head>

<body>
  <header>
    <h1>Lista de Avaliações</h1>
    <nav>
      <a class="voltar" href="admin_home.php">Voltar</a>
      <a class="logout" href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">
      <h2>Avaliações Registradas</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Técnico</th>
          <th>Nota</th>
          <th>Comentário</th>
          <th>Data</th>
          <th>Ações</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row['IDAvaliacao']; ?></td>
            <td><?php echo htmlspecialchars($row['cliente'] ?? '—'); ?></td>
            <td><?php echo htmlspecialchars($row['tecnico'] ?? '—'); ?></td>
            <td><?php echo $row['nota']; ?></td>
            <td><?php echo htmlspecialchars($row['comentario']); ?></td>
            <td><?php echo date("d/m/Y H:i", strtotime($row['data_avaliacao'])); ?></td>
            <td>
              <a class="btn" href="editar_avaliacao_admin.php?id=<?= $row['IDAvaliacao']; ?>">Editar</a>
              <a class="btn" href="excluir_avaliacao.php?id=<?php echo $row['IDAvaliacao']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?')">Excluir</a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </main>

  <footer>
    &copy; <?php echo date("Y"); ?> ServiGera - Todos os direitos reservados
  </footer>
</body>

</html>