<?php
session_start();
include("verifica.php");
include("conexao.php");

// Apenas admins podem acessar
verifica_tipo('A');

if (!isset($_GET['id'])) {
  header("Location: admins_lista.php");
  exit;
}

$idAdmin = (int)$_GET['id'];

// Impede que o próprio admin se exclua
if ($idAdmin == $_SESSION['id_usuario']) {
  $erro = "Você não pode excluir a si mesmo!";
}

// Busca login do admin
$sql = "SELECT login FROM usuarios WHERE IDusuario = ? AND tipo='A'";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idAdmin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
  $erro = "Administrador não encontrado.";
}

// Se confirmar exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = "DELETE FROM usuarios WHERE IDusuario = ? AND tipo='A'";
  $stmt = mysqli_prepare($id, $sql);
  mysqli_stmt_bind_param($stmt, "i", $idAdmin);
  if (mysqli_stmt_execute($stmt)) {
    header("Location: admins_lista.php");
    exit;
  } else {
    $erro = "Erro ao excluir administrador.";
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Excluir Admin - Servigera</title>
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

    header,
    footer {
      background-color: #900000;
      color: #fff;
      text-align: center;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      max-width: 500px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      padding: 30px;
      width: 100%;
      text-align: center;
    }

    h2 {
      color: #e60000;
      margin-bottom: 25px;
    }

    p {
      margin-bottom: 25px;
      font-size: 1.1em;
    }

    .btn {
      padding: 12px 25px;
      border-radius: 8px;
      font-weight: bold;
      color: #fff;
      text-decoration: none;
      background-color: #e60000;
      transition: all 0.3s;
      display: inline-block;
      margin: 5px;
    }

    .btn:hover {
      background-color: #ff3333;
    }

    form {
      display: inline-block;
    }

    .error {
      color: red;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Excluir Administrador</h1>
  </header>
  <main>
    <div class="container">
      <?php if (isset($erro)): ?>
        <p class="error"><?= $erro ?></p>
        <a class="btn" href="admins_lista.php">Voltar</a>
      <?php else: ?>
        <h2>Tem certeza que deseja excluir o admin "<?= htmlspecialchars($admin['login']) ?>"?</h2>
        <form method="POST">
          <input type="submit" class="btn" value="Sim, Excluir">
        </form>
        <a href="admins_lista.php" class="btn">Cancelar</a>
      <?php endif; ?>
    </div>
  </main>
  <footer>
    &copy; <?= date("Y"); ?> Servigera - Todos os direitos reservados
  </footer>
</body>

</html>