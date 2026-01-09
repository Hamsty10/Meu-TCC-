<?php
session_start();
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $senha_digitada = $_POST['senha'];

    $stmt = mysqli_prepare($id, "SELECT IDusuario, senha FROM usuarios WHERE login = ? AND tipo = 'T'");
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $idUsuario, $senha_hash_db);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($senha_hash_db && password_verify($senha_digitada, $senha_hash_db)) {
        $_SESSION['id_usuario'] = $idUsuario;
        $_SESSION['login'] = $login;
        $_SESSION['tipo'] = 'T';

        header("Location: tecnico_home.php");
        exit;
    } else {
        echo "<p style='color:red; text-align:center;'>Login ou senha incorretos!</p>";
    }
}
?>
