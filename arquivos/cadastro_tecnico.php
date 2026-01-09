<?php
session_start();
include("conexao.php");

$especialidades = [];
$resultEsp = mysqli_query($id, "SELECT IDespecialidade, nome FROM especialidades");
if ($resultEsp) {
  while ($row = mysqli_fetch_assoc($resultEsp)) {
    $especialidades[] = $row;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $nome = trim($_POST['nome']);
  $email = trim($_POST['email']);
  $telefone = preg_replace('/\D/', '', $_POST['telefone']);
  $data_nasc = $_POST['data_nascimento'];
  $login = trim($_POST['login']);
  $estado = mysqli_real_escape_string($id, $_POST['estado']);
  $cidade = mysqli_real_escape_string($id, $_POST['cidade']);
  $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
  $selecionadas = $_POST['especialidades'] ?? [];

  // Verificar idade
  $hoje = new DateTime();
  $nascimento = new DateTime($data_nasc);
  $idade = $hoje->diff($nascimento)->y;
  if ($idade < 18) {
      $erro_login = "Cadastro permitido apenas para maiores de 18 anos.";
  }

  // Verificar login duplicado
  $checkLogin = mysqli_prepare($id, "SELECT IDusuario FROM usuarios WHERE login = ?");
  mysqli_stmt_bind_param($checkLogin, "s", $login);
  mysqli_stmt_execute($checkLogin);
  mysqli_stmt_store_result($checkLogin);

  if (mysqli_stmt_num_rows($checkLogin) > 0) {
      $erro_login = "Este login já está em uso. Por favor, escolha outro.";
  }

  // Se não houver erro → inserir
  if (!isset($erro_login)) {

      $stmtUser = mysqli_prepare($id, "INSERT INTO usuarios (login, senha, tipo) VALUES (?, ?, 'T')");
      mysqli_stmt_bind_param($stmtUser, "ss", $login, $senha);
      mysqli_stmt_execute($stmtUser);
      $idUsuario = mysqli_insert_id($id);

      $stmtTec = mysqli_prepare($id, "INSERT INTO tecnicos (IDtecnico, nome, email, data_nascimento, telefone, estado, cidade) VALUES (?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmtTec, "issssss", $idUsuario, $nome, $email, $data_nasc, $telefone, $estado, $cidade);
      mysqli_stmt_execute($stmtTec);

      if (!empty($selecionadas)) {
        foreach ($selecionadas as $idEsp) {
          $stmtEsp = mysqli_prepare($id, "INSERT INTO tecnico_especialidade (IDtecnico, IDespecialidade) VALUES (?, ?)");
          mysqli_stmt_bind_param($stmtEsp, "ii", $idUsuario, $idEsp);
          mysqli_stmt_execute($stmtEsp);
        }
      }

      $_SESSION['id_usuario'] = $idUsuario;
      $_SESSION['login'] = $login;
      $_SESSION['tipo'] = 'T';

      header("Location: tecnico_home.php");
      exit;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>ServiGera - Cadastro de Técnico</title>
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  height: 100%;
  font-family: Arial, sans-serif;
  background: #e60000 url("../fundo.png") no-repeat center center fixed;
  background-size: cover;
  display: flex;
  flex-direction: column;
}

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
  align-items: center;
  padding: 40px 20px;
}

.container {
  max-width: 550px;
  background-color: #fff;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  padding: 40px;
  animation: fadeIn 1s ease-out;
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
  color: #e60000;
  text-align: center;
  margin-bottom: 25px;
  font-size: 2rem;
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

input[type="text"],
input[type="email"],
input[type="date"],
input[type="password"],
input[type="tel"],
select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-top: 5px;
}

fieldset {
  border: none;
  margin-top: 15px;
  padding: 0;
}

legend {
  font-weight: bold;
  margin-bottom: 10px;
}

.especialidades-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.especialidades-container label {
  font-weight: normal;
  width: 48%;
  display: flex;
  align-items: center;
}

.btn {
  display: inline-block;
  padding: 14px;
  font-size: 1.05rem;
  font-weight: bold;
  color: #fff;
  background-color: #e60000;
  text-decoration: none;
  border-radius: 10px;
  box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
}

.btn:hover {
  background-color: #ff3333;
  transform: scale(1.05);
}

.btn.secondary {
  background-color: #900000;
}

.botoes {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
}

header {
    background-color:#900000;
    padding:20px;
    text-align:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
}
header img {
    height:80px;
    width:auto;
    border-radius:10px;
}
</style>
</head>
<body>
<header>
    <img src="../servigeralateral.png" alt="ServiGera">
</header>

<main>
  <div class="container">

    <h2>Cadastro de Técnico</h2>

    <?php if (isset($erro_login)) : ?>
      <div style="
        background:#ffcccc;
        color:#900;
        border:1px solid #900;
        padding:10px;
        margin-bottom:15px;
        border-radius:8px;
        font-weight:bold;
        text-align:center;
      ">
        <?= $erro_login ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Nome:<input type="text" name="nome" required></label>
      <label>Email:<input type="email" name="email" required></label>
      <label>Telefone:<input type="tel" name="telefone" required maxlength="11" placeholder="Ex: 11987654321" oninput="this.value=this.value.replace(/[^0-9]/g,'')"></label>
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

      <fieldset>
        <legend>Especialidades:</legend>
        <div class="especialidades-container">
          <?php if (!empty($especialidades)): ?>
            <?php foreach ($especialidades as $esp): ?>
              <label><input type="checkbox" name="especialidades[]" value="<?= $esp['IDespecialidade'] ?>"> <?= htmlspecialchars($esp['nome']); ?></label>
            <?php endforeach; ?>
          <?php else: ?>
            <p>Nenhuma especialidade cadastrada.</p>
          <?php endif; ?>
        </div>
      </fieldset>

      <div class="botoes">
        <button type="submit" class="btn">Cadastrar</button>
        <a href="escolher_tipo.php" class="btn secondary">Voltar</a>
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
