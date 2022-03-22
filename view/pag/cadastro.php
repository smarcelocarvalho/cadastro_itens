<?php
require_once '../../model/db_connect.php';

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;

if(isset($_POST['enviar'])):
    $erros = array();
    if(!empty($_POST['nome_livro'])):
        if(!empty($_POST['editora_livro'])):
            if(!empty($_POST['autor_livro'])):
                if(!empty($_POST['genero_livro'])):
                    if(!empty($_POST['data_publicacao'])):
                        if ($_POST['data_publicacao'] > date('Y-m-d')):
                            $erros[] = "<li>Campo data de publicação não possível.</li>";
                        else:
                            $nomeLivro = $_POST['nome_livro'];
                            $editoraLivro = $_POST['editora_livro'];
                            $autorLivro = $_POST['autor_livro'];
                            $generoLivro = $_POST['genero_livro'];
                            $dataPublicacao = $_POST['data_publicacao'];
                            $insereRegistro = "INSERT INTO livros (nome_livro, editora_livro, autor_livro, genero_livro, data_publicacao ) VALUES (
                                '$nomeLivro','$editoraLivro','$autorLivro','$generoLivro','$dataPublicacao')";
                            mysqli_query($connect,$insereRegistro);
                            echo mysqli_error($connect);
                        endif;
                    else:
                        $erros[] = "<li>Campo data de publicação não preeenchido.</li>";
                    endif;
                else:
                    $erros[] = "<li>Campo genêro do livro não preeenchido.</li>";
                endif;
            else:
                $erros[] = "<li>Campo autor do livro não preeenchido.</li>";
            endif;
        else:
            $erros[] = "<li>Campo editora do livro não preeenchido.</li>";
        endif;
    else:
        $erros[] = "<li>Campo nome do livro não preeenchido. <li>";
    endif;
endif;

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
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="formulario">
                <input type="text" name="nome_livro" class="input nome_livro" placeholder="Nome">
                <input type="text" name="editora_livro" class="input editora_livro" placeholder="Editora">
                <input type="text" name="autor_livro" class="input autor_livro" placeholder="Autor">
                <input type="text" name="genero_livro" class="input genero_livro" placeholder="Gênero">
                <input type="date" name="data_publicacao" class="input data_publicacao" placeholder="Publicação">
                <input type="submit" value="CADASTRAR" class="input btn_enviar" name="enviar">
            </form>
        </div>
    </div>
    <?php
    if(!empty($erros)):
        foreach ($erros as $erro):
            echo $erro;
        endforeach;
    endif;
    ?>
</body>
</html>