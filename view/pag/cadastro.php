<?php

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;

if(isset($_POST['enviar'])){
    
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Livros</title>
    <link rel="stylesheet" href="../style/home.css">
</head>
<body>
    <div class="pagina_inteira">
        <div class="superior_pagina">
            <div class="header">
                <a href="home.php">HOME</a>
                <a href="/index.php">LOGOUT</a>
            </div>
            <div class="titulo">
                <h1>CADASTRAR LIVROS</h1>
            </div>
        </div>
        <div class="inferior_pagina">
            <form action="<?php echo $_SESSION['PHP_SELF'] ?>" method="POST" class="formulario">
                <input type="text" name="nome_livro" class="input nome_livro" placeholder="Nome">
                <input type="text" name="editora_livro" class="input editora_livro" placeholder="Editora">
                <input type="text" name="autor_livro" class="input autor_livro" placeholder="Autor">
                <input type="text" name="genero_livro" class="input genero_livro" placeholder="Gênero">
                <input type="date" name="data_publicacao" class="input data_publicacao" placeholder="Publicação">
                <input type="submit" value="CADASTRAR" class="input btn_enviar" name="enviar">
            </form>
        </div>
    </div>
</body>
</html>