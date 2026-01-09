<?php
session_start();
include("verifica.php");
include("conexao.php");
verifica_tipo('A'); // Apenas admins

$erro_login = null; // variável para exibir mensagem no HTML

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = trim($_POST['nome']);
  $email = trim($_POST['email']);
  $telefone = preg_replace('/\D/', '', $_POST['telefone']);
  $estado = $_POST['estado'];
  $cidade = $_POST['cidade'];
  $data_nasc = $_POST['data_nascimento'];
  $login = trim($_POST['login']);
  $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
  $bio = trim($_POST['bio']);

  //  --- Verificação de idade mínima ---
  $hoje = new DateTime();
  $nascimento = new DateTime($data_nasc);
  $idade = $hoje->diff($nascimento)->y;
  if ($idade < 18) {
      $erro_login = "Cadastro permitido apenas para maiores de 18 anos.";
  }

  //  --- Verificação de telefone ---
  if (!$erro_login && (strlen($telefone) < 10 || strlen($telefone) > 11)) {
      $erro_login = "Telefone inválido. Digite apenas números (10 ou 11 dígitos).";
  }

  // --- Verifica se o login já existe ---
  if (!$erro_login) {
      $verificaLogin = mysqli_prepare($id, "SELECT COUNT(*) FROM usuarios WHERE login = ?");
      mysqli_stmt_bind_param($verificaLogin, "s", $login);
      mysqli_stmt_execute($verificaLogin);
      mysqli_stmt_bind_result($verificaLogin, $existe);
      mysqli_stmt_fetch($verificaLogin);
      mysqli_stmt_close($verificaLogin);

      if ($existe > 0) {
          $erro_login = "Erro: o login '$login' já está em uso. Escolha outro.";
      }
  }

  // --- Se não houver erro, cria usuário ---
  if (!$erro_login) {

      // Criar usuário
      $stmtUser = mysqli_prepare($id, "INSERT INTO usuarios (login, senha, tipo) VALUES (?, ?, 'C')");
      mysqli_stmt_bind_param($stmtUser, "ss", $login, $senha);
      mysqli_stmt_execute($stmtUser);
      $idUsuario = mysqli_insert_id($id);

      // Criar cliente
      $stmtCli = mysqli_prepare($id, "INSERT INTO cliente (IDcliente, nome, email, data_nascimento, telefone, estado, cidade, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmtCli, "isssssss", $idUsuario, $nome, $email, $data_nasc, $telefone, $estado, $cidade, $bio);
      mysqli_stmt_execute($stmtCli);

      // Redirecionar o admin de volta
      header("Location: admin_home.php");
      exit;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Cadastro de Cliente - Admin</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100%;
      font-family: Arial, sans-serif;
      background: #e60000 url("../fundo.png") no-repeat center center fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
    }

    header, footer {
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
      align-items: center;
      padding: 40px 20px;
    }

    .container {
      max-width: 500px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      padding: 40px;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #e60000;
      text-align: center;
      margin-bottom: 25px;
      font-size: 2rem;
    }

    .erro {
      background: #ffcccc;
      color: #900;
      border: 1px solid #900;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
    }

    form { display: flex; flex-direction: column; gap: 15px; }

    label { font-weight: bold; color: #333; }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    input[type="password"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }

    textarea { height: 80px; resize: none; }

    .btn {
      padding: 14px;
      font-size: 1.05rem;
      font-weight: bold;
      color: #fff;
      background-color: #e60000;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: 0.3s;
      text-align: center;
      box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
    }

    .btn:hover { background-color: #ff3333; transform: scale(1.05); }

    .btn.secondary {
      background-color: transparent;
      border: 2px solid #e60000;
      color: #e60000;
    }

    .btn.secondary:hover {
      background-color: #e60000;
      color: #fff;
    }

    .botoes { display: flex; justify-content: center; gap: 15px; margin-top: 20px; }
  </style>
</head>

<body>
  <header>
    <h1>ServiGera - Admin</h1>
  </header>

  <main>
    <div class="container">
      <h2>Cadastro de Cliente</h2>

      <?php if ($erro_login): ?>
        <div class="erro"><?= $erro_login ?></div>
      <?php endif; ?>

      <form method="post">
        <label>Nome:<input type="text" name="nome" required></label>
        <label>Email:<input type="email" name="email" required></label>
        <label>Telefone:<input type="text" name="telefone" required maxlength="11" placeholder="Ex: 11987654321" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></label>
        <label>Data de nascimento:<input type="date" name="data_nascimento" required max="<?= date('Y-m-d', strtotime('-18 years')); ?>"></label>
        <label>Login:<input type="text" name="login" required></label>

        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
          <option value="">Selecione o estado</option>
        </select>

        <label for="cidade">Cidade:</label>
        <select id="cidade" name="cidade" required>
          <option value="">Selecione a cidade</option>
        </select>

        <label>Senha:<input type="password" name="senha" required></label>
        <label>Bio:<textarea name="bio" placeholder="Fale um pouco sobre o cliente..."></textarea></label>

        <div class="botoes">
          <button type="submit" class="btn">Cadastrar</button>
          <a href="admin_home.php" class="btn secondary">Voltar</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    &copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados
  </footer>

  <script>
    const estadoSelect = document.getElementById('estado');
    const cidadeSelect = document.getElementById('cidade');

    fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome')
      .then(res => res.json())
      .then(estados => {
        estados.forEach(estado => {
          const option = document.createElement('option');
          option.value = estado.sigla;
          option.textContent = estado.nome;
          estadoSelect.appendChild(option);
        });
      });

    estadoSelect.addEventListener('change', () => {
      const uf = estadoSelect.value;
      cidadeSelect.innerHTML = '<option value="">Carregando...</option>';

      fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`)
        .then(res => res.json())
        .then(cidades => {
          cidadeSelect.innerHTML = '<option value="">Selecione a cidade</option>';
          cidades.forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade.nome;
            option.textContent = cidade.nome;
            cidadeSelect.appendChild(option);
          });
        });
    });
  </script>

</body>

</html>
