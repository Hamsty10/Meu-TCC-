<?php
session_start();
include("verifica.php");
include("conexao.php");

// Garante que é admin
verifica_tipo('A');

if(!isset($_GET['id'])){
    die("Técnico não especificado.");
}

$IDtecnico = intval($_GET['id']);

// Inicia transação para garantir consistência
mysqli_begin_transaction($id);

try {

    // 1) Deleta especialidades do técnico
    mysqli_query($id, "DELETE FROM tecnico_especialidade WHERE IDtecnico=$IDtecnico");

    // 2) Deleta propostas que este técnico fez
    mysqli_query($id, "DELETE FROM propostas WHERE IDtecnico=$IDtecnico");

    // 3) Deleta avaliações ligadas às propostas desse técnico
    mysqli_query($id,
        "DELETE FROM avaliacoes 
         WHERE IDsolicitacao IN 
             (SELECT IDsolicitacao FROM propostas WHERE IDtecnico=$IDtecnico)"
    );

    // 4) Deleta login do técnico na tabela usuarios
    mysqli_query($id, "DELETE FROM usuarios WHERE IDusuario=$IDtecnico");

    // 5) Deleta o técnico
    if(mysqli_query($id, "DELETE FROM tecnicos WHERE IDtecnico=$IDtecnico")){

        mysqli_commit($id);
        header("Location: tecnicos_lista.php");
        exit;

    } else {
        throw new Exception("Erro ao excluir técnico: " . mysqli_error($id));
    }

} catch(Exception $e){

    mysqli_rollback($id);
    $mensagem = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Técnico - Servigera</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: Arial, sans-serif; background: #e60000 url("../fundo.png") no-repeat center center fixed; background-size: cover; color: #333; display: flex; flex-direction: column; }
        header { background-color: #900000; color: #fff; padding: 20px; text-align: center; position: relative; }
        header h1 { margin: 0; }
        header nav { position: absolute; top: 20px; right: 20px; }
        header nav a.voltar, header nav a.logout { color: #fff; background-color: #cc0000; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-left: 5px; }
        header nav a.voltar:hover, header nav a.logout:hover { background-color: #ff3333; }
        main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background-color: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.3); max-width: 500px; width: 100%; text-align: center; }
        .mensagem { font-size: 18px; color: red; margin-bottom: 20px; }
        a.btn { display: inline-block; padding: 10px 20px; background-color: #e60000; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s; }
        a.btn:hover { opacity: 0.8; }
        footer { background-color: #900000; color: #fff; padding: 15px; text-align: center; margin-top: auto; }
    </style>
</head>
<body>
<header>
    <h1>Excluir Técnico</h1>
    <nav>
        <a class="voltar" href="tecnicos_lista.php">Voltar</a>
        <a class="logout" href="logout.php">Sair</a>
    </nav>
</header>

<main>
    <div class="container">
        <?php if(isset($mensagem)) { ?>
        <div class="mensagem"><?php echo $mensagem; ?></div>
        <a class="btn" href="tecnicos_lista.php">Voltar à Lista de Técnicos</a>
        <?php } ?>
    </div>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> Servigera - Todos os direitos reservados
</footer>
</body>
</html>
