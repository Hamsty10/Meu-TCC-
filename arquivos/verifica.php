<?php

// se nao estiver logado, redireciona para escolher tipo
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: escolher_tipo.php");
    exit;
}

// função para restringir pagina por tipo
function verifica_tipo($tipo_esperado) {
    if ($_SESSION['tipo'] !== $tipo_esperado) {
        header("Location: escolher_tipo.php");
        exit;
    }
}
?>
