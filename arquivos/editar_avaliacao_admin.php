<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

// Verifica se veio ID
if (!isset($_GET['id'])) {
  die("Avaliação não especificada.");
}

$IDAvaliacao = intval($_GET['id']);

$sql = "SELECT 
            a.IDAvaliacao,
            a.IDSolicitacao,
            a.nota,
            a.comentario,
            s.IDcliente,
            c.nome AS nomeCliente,
            t.nome AS nomeTecnico
        FROM avaliacoes a
        LEFT JOIN solicitacoes s ON a.IDSolicitacao = s.IDsolicitacao
        LEFT JOIN cliente c ON s.IDcliente = c.IDcliente
        LEFT JOIN propostas p ON p.IDsolicitacao = s.IDsolicitacao AND p.status = 'aceita'
        LEFT JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
        WHERE a.IDAvaliacao = ?";


$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $IDAvaliacao);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$avaliacao = mysqli_fetch_assoc($result);

if (!$avaliacao) {
  die("Avaliação não encontrada.");
}

// Atualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nota = intval($_POST['nota']);
  $comentario = trim($_POST['comentario']);

  $sqlUP = "UPDATE avaliacoes SET nota = ?, comentario = ? WHERE IDAvaliacao = ?";
  $stmtUP = mysqli_prepare($id, $sqlUP);
  mysqli_stmt_bind_param($stmtUP, "isi", $nota, $comentario, $IDAvaliacao);

  if (mysqli_stmt_execute($stmtUP)) {
    header("Location: avaliacoes_lista.php");
    exit;
  } else {
    $mensagem = "<div class='mensagem-erro'>Erro ao atualizar avaliação.</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Editar Avaliação (Admin) - ServiGera</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html,
    body {
      height: 100%;
    }

    body {
      background: #e60000 url("../fundo.png") no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background-color: #900000;
      color: #fff;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    header img {
      height: 80px;
      width: auto;
      border-radius: 10px;
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
      margin-left: 8px;
      transition: 0.3s;
    }

    header nav a:hover {
      background-color: #ff3333;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      width: 520px;
      text-align: center;
      animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .mensagem-erro {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
    }

    h1 {
      color: #e60000;
      margin-bottom: 25px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      text-align: left;
    }

    label {
      font-weight: bold;
      display: flex;
      flex-direction: column;
    }

    input[type="number"],
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }

    button,
    .btn-voltar {
      background-color: #e60000;
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
      text-decoration: none;
      text-align: center;
    }

    button:hover,
    .btn-voltar:hover {
      background-color: #ff3333;
    }

    .btn-voltar {
      background-color: #900000;
      display: block;
      margin-top: 15px;
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
    <img src="../servigeralateral.png" alt="ServiGera">
    <nav>
      <a href="logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <div class="container">
      <?= isset($mensagem) ? $mensagem : '' ?>

      <h1>Editar Avaliação</h1>

      <p><strong>Cliente:</strong> <?= htmlspecialchars($avaliacao['nomeCliente']); ?></p>
      <p><strong>Técnico:</strong> <?= htmlspecialchars($avaliacao['nomeTecnico']); ?></p>

      <form method="post">
<label for="nota">Nota (1 a 5):</label>
<select name="nota" id="nota" required>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= ($avaliacao['nota'] == $i) ? 'selected' : '' ?>>
            <?= $i ?>
        </option>
    <?php endfor; ?>
</select>


        <label for="comentario">Comentário
          <textarea name="comentario" rows="4"><?= htmlspecialchars($avaliacao['comentario']); ?></textarea>
        </label>

        <button type="submit">Salvar Alterações</button>
      </form>

      <a href="avaliacoes_lista.php" class="btn-voltar">Voltar</a>
    </div>
  </main>

  <footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
  </footer>

</body>

</html>