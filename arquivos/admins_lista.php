<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

// Busca todos os admins
$result = mysqli_query($id, "SELECT IDusuario, login FROM usuarios WHERE tipo = 'A' ORDER BY login ASC");
$admins = [];
if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $admins[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Lista de Administradores - Servigera</title>
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
      display: flex;
      flex-direction: column;
      color: #333;
    }

    header,
        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
    }

    .container {
      max-width: 800px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      padding: 30px;
      width: 100%;
    }

    h2 {
      color: #e60000;
      text-align: center;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f5f5f5;
      color: #900000;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    .btn {
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: bold;
      color: #fff;
      text-decoration: none;
      background-color: #e60000;
      transition: all 0.3s;
      margin-right: 5px;
      display: inline-block;
    }

    .btn:hover {
      background-color: #ff3333;
    }

    .botoes {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 15px;
    }

    .acoes {
      display: flex;
      gap: 5px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Administradores</h1>
  </header>

  <main>
    <div class="container">
      <div class="botoes">
        <a href="admin_home.php" class="btn">Voltar</a>
      </div>

      <h2>Lista de Admins</h2>

      <?php if (empty($admins)): ?>
        <p>Nenhum administrador cadastrado.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Login</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $admin): ?>
              <tr>
                <td><?= $admin['IDusuario'] ?></td>
                <td><?= htmlspecialchars($admin['login']) ?></td>
                <td class="acoes">
                  <a class="btn" href="editar_admin.php?id=<?= $admin['IDusuario'] ?>">Editar</a>
                  <a class="btn" href="excluir_admin.php?id=<?= $admin['IDusuario'] ?>" onclick="return confirm('Tem certeza que deseja excluir este admin?');">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>

 <footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>

</html>