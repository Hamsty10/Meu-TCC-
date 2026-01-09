<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome']);

  $sql = "INSERT INTO especialidades (nome) VALUES (?)";
  $stmt = mysqli_prepare($id, $sql);
  mysqli_stmt_bind_param($stmt, "s", $nome);
  mysqli_stmt_execute($stmt);

  header("Location: especialidades_lista.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Adicionar Especialidade</title>
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

    header,
    footer {
      background-color: #900000;
      color: #fff;
      padding: 12px;
      text-align: center;
      font-size: 0.95rem;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      width: 100%;
      max-width: 450px;
      background: #fff;
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.9s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      text-align: center;
      color: #e60000;
      margin-bottom: 25px;
      font-size: 1.8rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
      color: #333;
    }

    input[type="text"] {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }

    .btn {
      padding: 14px;
      background: #e60000;
      color: #fff;
      font-size: 1.05rem;
      font-weight: bold;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
      transition: 0.3s;
      text-align: center;
    }

    .btn:hover {
      background: #ff3333;
      transform: scale(1.05);
    }

    .btn.secondary {
      background: transparent;
      border: 2px solid #e60000;
      color: #e60000;
    }

    .btn.secondary:hover {
      background: #e60000;
      color: #fff;
    }

    .botoes {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 15px;
    }
  </style>
</head>

<body>

  <header>
    <h1>ServiGera - Admin</h1>
  </header>

  <main>
    <div class="container">
      <h2>Adicionar Especialidade</h2>

      <form method="POST">
        <label>Nome da Especialidade:
          <input type="text" name="nome" required>
        </label>

        <div class="botoes">
          <button class="btn" type="submit">Salvar</button>
          <a href="especialidades_lista.php" class="btn secondary">Voltar</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
  </footer>

</body>

</html>