<?php
session_start();
include("conexao.php");

$erro_login = null; // variavel para exibir mensagem no HTML

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $data_nasc = $_POST['data_nascimento'];
    $login = trim($_POST['login']);
    $estado = mysqli_real_escape_string($id, $_POST['estado']);
    $cidade = mysqli_real_escape_string($id, $_POST['cidade']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $bio = trim($_POST['bio']);

    // Verificação de idade mínima (18 anos)
    $hoje = new DateTime();
    $nascimento = new DateTime($data_nasc);
    $idade = $hoje->diff($nascimento)->y;
    if ($idade < 18) {
        $erro_login = "Cadastro permitido apenas para maiores de 18 anos.";
    }

    // Verificação telefone
    if (!$erro_login && (strlen($telefone) < 10 || strlen($telefone) > 11)) {
        $erro_login = "Telefone inválido. Digite apenas números (10 ou 11 dígitos).";
    }

    // Verificar se login já existe
    if (!$erro_login) {
        $checkLogin = mysqli_prepare($id, "SELECT IDusuario FROM usuarios WHERE login = ?");
        mysqli_stmt_bind_param($checkLogin, "s", $login);
        mysqli_stmt_execute($checkLogin);
        mysqli_stmt_store_result($checkLogin);

        if (mysqli_stmt_num_rows($checkLogin) > 0) {
            $erro_login = "Este login já está em uso. Por favor, escolha outro.";
        }
    }

    // Se não houver erro, prossegue com o cadastro
    if (!$erro_login) {

        $stmtUser = mysqli_prepare($id, "INSERT INTO usuarios (login, senha, tipo) VALUES (?, ?, 'C')");
        mysqli_stmt_bind_param($stmtUser, "ss", $login, $senha);
        mysqli_stmt_execute($stmtUser);
        $idUsuario = mysqli_insert_id($id);

        $stmtCli = mysqli_prepare($id, "INSERT INTO cliente (IDcliente, nome, email, data_nascimento, telefone, estado, cidade, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmtCli, "isssssss", $idUsuario, $nome, $email, $data_nasc, $telefone, $estado, $cidade, $bio);
        mysqli_stmt_execute($stmtCli);

        $_SESSION['id_usuario'] = $idUsuario;
        $_SESSION['login'] = $login;
        $_SESSION['tipo'] = 'C';

        header("Location: cliente_home.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>ServiGera - Cadastro de Cliente</title>
<style>
    /* ===== SERVIGERA - CSS PADRONIZADO (interno) ===== */
    *{box-sizing:border-box;margin:0;padding:0;scroll-behavior:smooth;}
    html,body{height:100%;font-family:Arial, sans-serif;background:#e60000 url("../fundo.png") no-repeat center center fixed;background-size:cover;display:flex;flex-direction:column;}
    header{background-color:#900000;padding:20px;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,0.3);}
    header img{height:80px;width:auto;border-radius:10px;}
    main{flex:1;display:flex;justify-content:center;align-items:center;padding:40px 20px;}
    .container{max-width:520px;background:#fff;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,0.3);padding:36px;animation:fadeIn .8s ease-out;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(-12px);}to{opacity:1;transform:translateY(0);}}
    h2{text-align:center;color:#e60000;margin-bottom:18px;font-size:1.8rem;}
    form{display:flex;flex-direction:column;gap:12px;}
    label{font-weight:bold;}
    input[type="text"],input[type="email"],input[type="date"],input[type="password"],textarea,select{
        width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-top:6px;
    }
    textarea{height:80px;resize:none;}
    .btn{display:inline-block;padding:12px 18px;font-size:1rem;font-weight:bold;color:#fff;background:#e60000;border-radius:10px;border:none;cursor:pointer;text-align:center;box-shadow:0 3px 6px rgba(0,0,0,0.15);transition:all .25s;}
    .btn:hover{background:#ff3333;transform:translateY(-2px);}
    .btn.secondary{background:transparent;border:2px solid #e60000;color:#e60000;}
    .btn.secondary:hover{background:#e60000;color:#fff;}
    .botoes{display:flex;justify-content:center;gap:12px;margin-top:12px;}
    footer {
        background-color: #900000;
        color: #fff;
        padding: 12px;
        text-align: center;
        font-size: 0.95rem;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.25);
    }
    @media(max-width:520px){.container{padding:20px;}h2{font-size:1.4rem;}}
</style>
</head>
<body>
<header>
    <img src="../servigeralateral.png" alt="ServiGera">
</header>

<main>
    <div class="container">
        <h2>Cadastro de Cliente</h2>

        <!-- MENSAGEM DE ERRO -->
        <?php if ($erro_login): ?>
            <div style="background:#ffcccc;color:#900;border:1px solid #900;padding:10px;margin-bottom:15px;border-radius:8px;font-weight:bold;text-align:center;">
                <?= $erro_login ?>
            </div>
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
            <label>Bio:<textarea name="bio" placeholder="Fale um pouco sobre você..."></textarea></label>

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

// Carregar estados
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

// Quando o estado for selecionado, carregar cidades
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
