<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

// Verifica ID enviado
if (!isset($_GET['id'])) {
  die("Solicitação não encontrada.");
}

$idSol = intval($_GET['id']);

// Se enviou o formulário → atualizar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $titulo = trim($_POST['titulo']);
  $descricao = trim($_POST['descricao']);
  $status = $_POST['status'];

  $sqlUp = "UPDATE solicitacoes SET titulo=?, descricao=?, status=? WHERE IDsolicitacao=?";
  $stmt = mysqli_prepare($id, $sqlUp);
  mysqli_stmt_bind_param($stmt, "sssi", $titulo, $descricao, $status, $idSol);
  mysqli_stmt_execute($stmt);

  header("Location: solicitacoes_lista.php");
  exit;
}

// Carrega os dados da solicitação
$sql = "SELECT s.*, c.nome AS cliente 
        FROM solicitacoes s 
        LEFT JOIN cliente c ON s.IDcliente = c.IDcliente 
        WHERE s.IDsolicitacao = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idSol);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sol = mysqli_fetch_assoc($result);

if (!$sol) {
  die("Solicitação inexistente.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Editar Solicitação - Admin</title>

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

    /* HEADER */
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

    /* MAIN */
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
      width: 500px;
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

    h1 {
      color: #e60000;
      text-align: center;
      margin-bottom: 25px;
    }

    label {
      font-weight: bold;
      display: flex;
      flex-direction: column;
      margin-top: 15px;
    }

    input,
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }

    textarea {
      resize: vertical;
      height: 120px;
    }

    /* BOTÕES */
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
      text-align: center;
      display: block;
      width: 100%;
      margin-top: 20px;
    }

    button:hover,
    .btn-voltar:hover {
      background-color: #ff3333;
    }

    .btn-voltar {
      background-color: #900000;
      margin-top: 10px;
    }

    /* FOOTER */
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
      <a href="solicitacoes_lista.php">Voltar</a>
      <a href="logout.php">Sair</a>
    </nav>
  </header>

  <main>

    <div class="container">

      <h1>Editar Solicitação</h1>

      <form method="POST">

        <label>Cliente:
          <input type="text" value="<?= htmlspecialchars($sol['cliente']); ?>" disabled>
        </label>

        <label>Título:
          <input type="text" name="titulo" required value="<?= htmlspecialchars($sol['titulo']); ?>">
        </label>

        <label>Descrição:
          <textarea name="descricao" required><?= htmlspecialchars($sol['descricao']); ?></textarea>
        </label>

        <label>Status:
          <select name="status" required>
            <option value="Aberto" <?= $sol['status'] == "Aberto" ? "selected" : "" ?>>Aberto</option>
            <option value="Em Negociação" <?= $sol['status'] == "Em Negociação" ? "selected" : "" ?>>Em Negociação</option>
            <option value="Concluído" <?= $sol['status'] == "Concluído" ? "selected" : "" ?>>Concluído</option>
          </select>
        </label>

        <button type="submit">Salvar Alterações</button>
      </form>

      <a href="solicitacoes_lista.php" class="btn-voltar">Voltar</a>

    </div>

  </main>

  <footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
  </footer>

</body>

</html>