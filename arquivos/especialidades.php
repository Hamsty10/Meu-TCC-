<?php
session_start();
include("verifica.php");
verifica_tipo('T'); // apenas técnicos
include("conexao.php");

$idTecnico = $_SESSION['id_usuario'];

// Busca especialidades já selecionadas pelo técnico
$sql = "SELECT IDespecialidade FROM tecnico_especialidade WHERE IDtecnico = ?";
$stmt = mysqli_prepare($id, $sql);
mysqli_stmt_bind_param($stmt, "i", $idTecnico);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$selecionadas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $selecionadas[] = $row['IDespecialidade'];
}

// Busca todas as especialidades disponíveis
$sql = "SELECT IDespecialidade, nome FROM especialidades";
$resEspecialidades = mysqli_query($id, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Especialidades - Servigera</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: Arial, sans-serif; background: #e60000 url("../fundo.png") no-repeat center center fixed; background-size: cover; color: #333; }
        body { display: flex; flex-direction: column; }

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

        header nav a.logout {
            color: #fff;
            background-color: #cc0000;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        header nav a.logout:hover { background-color: #ff3333; }

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
            width: 400px;
        }

        h2 {
            color: #e60000;
            text-align: center;
            margin-bottom: 20px;
        }

        form { display: flex; flex-direction: column; gap: 10px; }
        label { display: flex; align-items: center; gap: 10px; }

        button, .btn-voltar {
            background-color: #e60000;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 20px;
            text-align: center;
        }

        button:hover, .btn-voltar:hover { background-color: #ff3333; }
        .btn-voltar { background-color: #900000; text-decoration: none; display: block; }

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
        <a class="logout" href="logout.php">Sair</a>
    </nav>
</header>

<main>
    <div class="container">
        <form action="salvar_especialidades.php" method="post">
            <?php while ($row = mysqli_fetch_assoc($resEspecialidades)) { ?>
                <div>
                    <label>
                        <input type="checkbox" name="especialidades[]" value="<?= $row['IDespecialidade'] ?>"
                            <?= in_array($row['IDespecialidade'], $selecionadas) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($row['nome']) ?>
                    </label>
                </div>
            <?php } ?>
            <button type="submit">Salvar</button>
            <a href="tecnico_home.php" class="btn-voltar">Voltar</a>
        </form>
    </div>
</main>

  <footer>
    &copy; <?php echo date("Y"); ?> ServiGera - Todos os direitos reservados
  </footer>
</body>
</html>
