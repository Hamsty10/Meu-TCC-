<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

if (!isset($_GET['id'])) {
  header("Location: admins_lista.php");
  exit;
}

$idAdmin = (int)$_GET['id'];

// Busca dados do admin
$sql = "SELECT login FROM usuarios WHERE IDusuario = ? AND tipo='A'";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idAdmin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
  echo "Administrador não encontrado.";
  exit;
}

// Atualiza admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $novoLogin = trim($_POST['login']);
  if ($novoLogin !== '') {
    $sql = "UPDATE usuarios SET login = ? WHERE IDusuario = ?";
    $stmt = mysqli_prepare($id, $sql);
    mysqli_stmt_bind_param($stmt, "si", $novoLogin, $idAdmin);
    if (mysqli_stmt_execute($stmt)) {
      header("Location: admins_lista.php");
      exit;
    } else {
      $erro = "Erro ao atualizar admin.";
    }
  } else {
    $erro = "O login não pode ser vazio.";
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Editar Admin - Servigera</title>
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
      max-width: 500px;
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

    input[type="text"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1em;
    }

    input[type="submit"] {
      background-color: #e60000;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      transition: all 0.3s;
    }

    input[type="submit"]:hover {
      background-color: #ff3333;
    }

    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
    }

    a {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #900000;
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      opacity: 0.8;
    }
  </style>
</head>

<body>
  <header>
    <h1>Editar Administrador</h1>
  </header>

  <main>
    <div class="container">
      <?php if (isset($erro)) echo "<p class='error'>{$erro}</p>"; ?>
      <form method="POST">
        <input type="text" name="login" value="<?= htmlspecialchars($admin['login']) ?>" required>
        <input type="submit" value="Salvar">
      </form>
      <a href="admins_lista.php">Voltar à Lista</a>
    </div>
  </main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>
</body>

</html>