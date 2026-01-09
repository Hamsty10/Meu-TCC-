<?php
session_start();
include("verifica.php");
include("conexao.php");

// Apenas admin pode acessar
verifica_tipo('A');

if(!isset($_GET['id'])) {
    die("Técnico não especificado.");
}

$IDtecnico = intval($_GET['id']);
$mensagem = "";

// Busca dados do técnico
$sqlTec = "SELECT nome, email, telefone, estado, cidade FROM tecnicos WHERE IDtecnico = ?";
$stmtTec = mysqli_prepare($id, $sqlTec);
mysqli_stmt_bind_param($stmtTec, "i", $IDtecnico);
mysqli_stmt_execute($stmtTec);
$resTec = mysqli_stmt_get_result($stmtTec);
$tecnico = mysqli_fetch_assoc($resTec);

// Busca todas as especialidades
$resEsp = mysqli_query($id, "SELECT * FROM especialidades ORDER BY nome ASC");

// Busca especialidades atuais do técnico
$resTecEsp = mysqli_query($id, "SELECT IDespecialidade FROM tecnico_especialidade WHERE IDtecnico=$IDtecnico");
$tecEsp = [];
while($row = mysqli_fetch_assoc($resTecEsp)) {
    $tecEsp[] = $row['IDespecialidade'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $estado = trim($_POST['estado']);
    $cidade = trim($_POST['cidade']);
    $especialidades = $_POST['especialidades'] ?? [];

    mysqli_begin_transaction($id);
    try {
        $sqlUpdate = "UPDATE tecnicos SET nome=?, email=?, telefone=?, estado=?, cidade=? WHERE IDtecnico=?";
        $stmtUp = mysqli_prepare($id, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUp, "sssssi", $nome, $email, $telefone, $estado, $cidade, $IDtecnico);
        mysqli_stmt_execute($stmtUp);

        mysqli_query($id, "DELETE FROM tecnico_especialidade WHERE IDtecnico=$IDtecnico");

        foreach ($especialidades as $esp) {
            $espID = intval($esp);
            mysqli_query($id, "INSERT INTO tecnico_especialidade (IDtecnico, IDespecialidade) VALUES ($IDtecnico, $espID)");
        }

        mysqli_commit($id);

        header("Location: tecnicos_lista.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($id);
        $mensagem = "<div class='mensagem-erro'>Erro ao atualizar técnico.</div>";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Técnico (Admin)</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }

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
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
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

header nav a:hover { background-color: #ff3333; }

main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}

.container {
    background-color: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    width: 500px;
    text-align: center;
    animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.mensagem-sucesso {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.mensagem-erro {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

h1 { color: #e60000; margin-bottom: 25px; }

form { display: flex; flex-direction: column; gap: 15px; text-align: left; }

label {
  font-weight: bold;
  color: #333;
  display: flex;
  flex-direction: column;
}

input[type="text"],
input[type="email"],
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
  color: #333;
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

button, .btn-voltar {
    background-color: #e60000;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
    text-decoration: none;
    text-align: center;
}

button:hover, .btn-voltar:hover { background-color: #ff3333; transform: scale(1.05); }
.btn-voltar { background-color: #900000; display: block; margin-top: 15px; }

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
    <img src="../servigeralateral.png" alt="ServiGera">
    <nav>
        <a href="tecnicos_lista.php">Voltar</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<main>
  <div class="container">
    <?= $mensagem ?>
    <h1>Editar Técnico</h1>

    <form method="post">
      <label for="nome">Nome:
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($tecnico['nome']); ?>" required>
      </label>

      <label for="email">Email:
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($tecnico['email']); ?>" required>
      </label>

      <label for="telefone">Telefone:
        <input type="text" name="telefone" id="telefone" maxlength="11" placeholder="Ex: 11987654321"
               value="<?= htmlspecialchars($tecnico['telefone']); ?>"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
      </label>

      <label for="estado">Estado:
        <select id="estado" name="estado" required>
          <option value="">Selecione o estado</option>
        </select>
      </label>

      <label for="cidade">Cidade:
        <select id="cidade" name="cidade" required>
          <option value="">Selecione a cidade</option>
        </select>
      </label>

      <fieldset>
        <legend>Especialidades:</legend>
        <div class="especialidades-container">
          <?php while($esp = mysqli_fetch_assoc($resEsp)) { ?>
            <label>
              <input type="checkbox" name="especialidades[]" value="<?= $esp['IDespecialidade']; ?>"
                <?= in_array($esp['IDespecialidade'], $tecEsp) ? "checked" : ""; ?>>
              <?= htmlspecialchars($esp['nome']); ?>
            </label>
          <?php } ?>
        </div>
      </fieldset>

      <button href="tecnicos_lista.php" type="submit">Salvar Alterações</button>
    </form>

    <a href="tecnicos_lista.php" class="btn-voltar">Voltar</a>
  </div>
</main>

<footer>
  &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>

<script>
const estadoSelect = document.getElementById('estado');
const cidadeSelect = document.getElementById('cidade');
const estadoAtual = "<?= $tecnico['estado'] ?? '' ?>";
const cidadeAtual = "<?= $tecnico['cidade'] ?? '' ?>";

fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome')
  .then(res => res.json())
  .then(estados => {
    estados.forEach(estado => {
      const option = document.createElement('option');
      option.value = estado.sigla;
      option.textContent = estado.nome;
      if (estado.sigla === estadoAtual) option.selected = true;
      estadoSelect.appendChild(option);
    });
    if (estadoAtual) carregarCidades(estadoAtual);
  });

function carregarCidades(uf) {
  cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
  fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`)
    .then(res => res.json())
    .then(cidades => {
      cidadeSelect.innerHTML = '<option value="">Selecione a cidade</option>';
      cidades.forEach(cidade => {
        const option = document.createElement('option');
        option.value = cidade.nome;
        option.textContent = cidade.nome;
        if (cidade.nome === cidadeAtual) option.selected = true;
        cidadeSelect.appendChild(option);
      });
    });
}

estadoSelect.addEventListener('change', () => {
  if (estadoSelect.value) carregarCidades(estadoSelect.value);
});
</script>
</body>
</html>
