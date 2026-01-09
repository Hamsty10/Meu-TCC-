<?php
session_start();
include("verifica.php");
include("conexao.php");

// Garante que é admin
verifica_tipo('A');

if (!isset($_GET['id'])) {
    die("Cliente não especificado.");
}

$IDcliente = intval($_GET['id']);

// Busca dados do cliente
$sql = "SELECT nome, email, telefone, data_nascimento, estado, cidade FROM cliente WHERE IDcliente = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $IDcliente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($result);

// Atualiza os dados se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $data_nascimento = $_POST['data_nascimento'];
    $estado = trim($_POST['estado']);
    $cidade = trim($_POST['cidade']);

    $sqlUpdate = "UPDATE cliente SET nome = ?, email = ?, telefone = ?, data_nascimento = ?, estado = ?, cidade = ? WHERE IDcliente = ?";
    $stmtUp = mysqli_prepare($id, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUp, "ssssssi", $nome, $email, $telefone, $data_nascimento, $estado, $cidade, $IDcliente);

    if (mysqli_stmt_execute($stmtUp)) {
        header("Location: clientes_lista.php");
        exit;
    } else {
        $mensagem = "<div class='mensagem-erro'>Erro ao atualizar cliente.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Cliente (Admin) - ServiGera</title>
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
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    width: 420px;
    text-align: center;
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-12px); }
    to { opacity: 1; transform: translateY(0); }
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

h1 { color: #e60000; margin-bottom: 25px; }

form { display: flex; flex-direction: column; gap: 15px; text-align: left; }
label { font-weight: bold; display: flex; flex-direction: column; }
input[type="text"], input[type="email"], input[type="date"], textarea, select {
    width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-top: 5px;
}

button, .btn-voltar {
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
button:hover, .btn-voltar:hover { background-color: #ff3333; }
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
        <a href="clientes_lista.php">Voltar</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<main>
    <div class="container">
        <?= isset($mensagem) ? $mensagem : '' ?>
        <h1>Editar Cliente</h1>

        <form method="post">
            <label for="nome">Nome
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($cliente['nome']); ?>" required>
            </label>
            <label for="email">Email
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($cliente['email']); ?>" required>
            </label>
            <label for="telefone">Telefone
                <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($cliente['telefone']); ?>" maxlength="11" pattern="\d{10,11}" title="Digite apenas números" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
            </label>
            <label for="data_nascimento">Data de Nascimento
                <input type="date" name="data_nascimento" id="data_nascimento" value="<?= htmlspecialchars($cliente['data_nascimento']); ?>" required>
            </label>

            <label for="estado">Estado
                <select id="estado" name="estado" required>
                    <option value="">Selecione o estado</option>
                </select>
            </label>
            <label for="cidade">Cidade
                <select id="cidade" name="cidade" required>
                    <option value="">Selecione a cidade</option>
                </select>
            </label>

            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="clientes_lista.php" class="btn-voltar">Voltar</a>
    </div>
</main>

<footer>
    &copy; <?= date('Y'); ?> ServiGera - Todos os direitos reservados.
</footer>

<script>
const estadoSelect = document.getElementById('estado');
const cidadeSelect = document.getElementById('cidade');
const estadoAtual = "<?= $cliente['estado'] ?? '' ?>";
const cidadeAtual = "<?= $cliente['cidade'] ?? '' ?>";

// Carrega estados do IBGE
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

// Carrega cidades conforme o estado selecionado
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
