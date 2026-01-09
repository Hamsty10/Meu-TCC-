<?php
session_start();
include("verifica.php");
verifica_tipo('C');
include("conexao.php");

$idCliente = $_SESSION['id_usuario'] ?? null;
$idSolicitacao = intval($_GET['id'] ?? 0);
if (!$idCliente || !$idSolicitacao) die("Acesso inválido.");

// Buscar técnico vinculado à solicitação concluída
$sql = "SELECT p.IDtecnico, t.nome 
        FROM propostas p
        JOIN tecnicos t ON p.IDtecnico = t.IDtecnico
        WHERE p.IDsolicitacao = ? AND p.status = 'aceita'";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idSolicitacao);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($res);
if (!$dados) die("Não há técnico vinculado a esta solicitação.");

$idTecnico = $dados['IDtecnico'];

// Inserir avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = intval($_POST['nota']);
    $comentario = trim($_POST['comentario']);
    $fotoConteudo = null;

    // Upload da imagem (opcional)
    if (!empty($_FILES['imagem']['name'])) {
        $permitidas = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['imagem']['type'], $permitidas)) {
            $fotoConteudo = file_get_contents($_FILES['imagem']['tmp_name']);
        } else {
            $erro = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
        }
    }

    if ($nota >= 1 && $nota <= 5 && empty($erro)) {

        // INSERT CORRETO PARA O SEU BANCO
        $sqlInsert = "INSERT INTO avaliacoes (IDSolicitacao, nota, comentario, foto)
                      VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($id, $sqlInsert);
        mysqli_stmt_bind_param(
            $stmt,
            "iiss",
            $idSolicitacao,
            $nota,
            $comentario,
            $fotoConteudo
        );

        if ($fotoConteudo !== null) {
            mysqli_stmt_send_long_data($stmt, 3, $fotoConteudo);
        }

        mysqli_stmt_execute($stmt);

        header("Location: cliente_home.php?avaliado=1");
        exit;
    } elseif (empty($erro)) {
        $erro = "Por favor, selecione uma nota entre 1 e 5.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Avaliar Técnico - ServiGera</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html,
        body {
            height: 100%;
            font-family: Arial, sans-serif;
            background: #e60000 url("../fundo.png") no-repeat center center fixed;
            background-size: cover;
            color: #333
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh
        }

        header {
            background-color: #900000;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3)
        }

        header h1 {
            color: #fff;
            margin: 0
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: left
        }

        h1 {
            color: #900000;
            margin-bottom: 20px;
            text-align: center
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold
        }

        select,
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em
        }

        button {
            margin-top: 20px;
            background: #900000;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: all 0.3s
        }

        button:hover {
            background: #e60000
        }

        .alert {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px
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
        <h1>Avaliar Técnico</h1>
    </header>

    <main>
        <div class="container">
            <h1>Serviço concluído!</h1>
            <p>Você está avaliando o técnico <strong><?= htmlspecialchars($dados['nome']) ?></strong></p>

            <?php if (!empty($erro)): ?>
                <div class="alert"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label for="nota">Nota (1 a 5):</label>
                <select name="nota" id="nota" required>
                    <option value="">Selecione</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label for="comentario">Comentário:</label>
                <textarea name="comentario" id="comentario" rows="4" placeholder="Deixe um comentário sobre o serviço..."></textarea>

                <label for="imagem">Imagem (opcional):</label>
                <input type="file" name="imagem" id="imagem" accept=".jpg,.jpeg,.png,.gif">

                <button type="submit">Enviar Avaliação</button>
            </form>
        </div>
    </main>

    <footer>&copy; <?= date("Y"); ?> ServiGera - Todos os direitos reservados</footer>
</body>

</html>