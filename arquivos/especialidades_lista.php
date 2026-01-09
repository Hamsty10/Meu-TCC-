<?php
session_start();
include("verifica.php");
include("conexao.php");

// Somente admin
verifica_tipo('A');

// Busca todas as especialidades
$result = mysqli_query($id, "SELECT IDespecialidade, nome FROM especialidades ORDER BY IDespecialidade ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Lista de Especialidades - Servigera</title>

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
      max-width: 900px;
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
      min-width: 700px;
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
  </style>
</head>

<body>

  <header>
    <h1>Lista de Especialidades</h1>
    <nav>
      <a class="voltar" href="admin_home.php">Voltar</a>
      <a class="logout" href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">
      <h2>Especialidades Cadastradas</h2>

      <a class="btn" href="especialidades_add.php" style="margin-bottom: 15px; display: inline-block;">
        + Adicionar Especialidade
      </a>

      <table>
        <tr>
          <th>ID</th>
          <th>Nome da Especialidade</th>
          <th>Ações</th>
        </tr>

        <?php while ($esp = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $esp['IDespecialidade']; ?></td>
            <td><?php echo htmlspecialchars($esp['nome']); ?></td>
            <td>
              <a class="btn" href="especialidades_edit.php?id=<?php echo $esp['IDespecialidade']; ?>">Editar</a>
              <a class="btn"
                href="especialidades_delete.php?id=<?php echo $esp['IDespecialidade']; ?>"
                onclick="return confirm('Tem certeza que deseja excluir esta especialidade?')"
                style="background:#cc0000;">
                Excluir
              </a>
            </td>
          </tr>
        <?php } ?>

      </table>
    </div>
  </main>

  <footer>
    &copy; <?= date('Y'); ?> Servigera - Todos os direitos reservados.
  </footer>

</body>

</html>