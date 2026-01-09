<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // apenas técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'] ?? null;
if (!$idTecnico) {
  die("Acesso negado. Faça login novamente.");
}

$db = isset($id) ? $id : (isset($conexao) ? $conexao : (isset($mysqli) ? $mysqli : null));
if (!$db) die("Erro de conexão com o banco.");

// ID do cliente
$idCliente = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idCliente <= 0) {
  die("Cliente inválido.");
}

// Busca dados do cliente (incluindo cidade e estado)
$sql = "SELECT nome, foto, bio, email, telefone, cidade, estado FROM cliente WHERE IDcliente = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCliente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($result);

if (!$cliente) {
  die("Cliente não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Perfil de <?= htmlspecialchars($cliente['nome']); ?></title>
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
    }

    body {
      display: flex;
      flex-direction: column;
    }

    header {
      background-color: #900000;
      color: #fff;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
    }

    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }

    img.foto-perfil {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #e60000;
      margin-bottom: 15px;
    }

header h1 {
    font-size: 24px;
    letter-spacing: 0.5px;
    color: #fff;
}

h1 {
    color: #900000;
    font-size: 22px;
    margin-bottom: 8px;
}


    .bio {
      font-style: italic;
      background: #f5f5f5;
      padding: 10px;
      border-radius: 8px;
      white-space: pre-wrap;
      word-break: break-word;
      margin-bottom: 20px;
    }

    .info {
      text-align: left;
      margin-bottom: 10px;
    }

    .info strong {
      color: #900000;
    }

    .btn-voltar {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      border-radius: 8px;
      font-weight: bold;
      color: #fff;
      text-decoration: none;
      background-color: #900000;
      transition: all 0.3s;
    }

    .btn-voltar:hover {
      background-color: #ff3333;
    }

        footer {
            background-color: #900000;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-size: 0.95rem;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
        }
  </style>
</head>

<body>
  <header>
    <h1>Perfil do Cliente</h1>
  </header>
  <main>
    <div class="container">
      <?php if (!empty($cliente['foto'])): ?>
        <img class="foto-perfil" src="data:image/jpeg;base64,<?= base64_encode($cliente['foto']); ?>" alt="Foto de <?= htmlspecialchars($cliente['nome']); ?>">
      <?php else: ?>
        <img class="foto-perfil" src="../imagens/placeholder.png" alt="Sem foto">
      <?php endif; ?>

      <h1><?= htmlspecialchars($cliente['nome']); ?></h1>

      <?php if (!empty($cliente['bio'])): ?>
        <p class="bio"><?= htmlspecialchars($cliente['bio']); ?></p>
      <?php endif; ?>

      <div class="info"><strong>Email:</strong> <?= htmlspecialchars($cliente['email'] ?? 'Não informado'); ?></div>
      <div class="info"><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone'] ?? 'Não informado'); ?></div>
      <div class="info"><strong>Cidade:</strong> <?= htmlspecialchars($cliente['cidade'] ?? 'Não informada'); ?></div>
      <div class="info"><strong>Estado:</strong> <?= htmlspecialchars($cliente['estado'] ?? 'Não informado'); ?></div>

      <a href="javascript:history.back()" class="btn-voltar">Voltar</a>
    </div>
  </main>
  <footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
  </footer>
</body>

</html>
