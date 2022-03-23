<?php
require_once '../../model/db_connect.php';

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;

$idLivro = $_GET['id'];
$buscaLivro = "SELECT * FROM livros WHERE id_livros = $idLivro ";
$resultadoBusca = mysqli_query($connect, $buscaLivro);
$arrayResultado = mysqli_fetch_array($resultadoBusca);

date_default_timezone_set('America/Sao_Paulo');

if(isset($_POST['enviar'])):
    $erros = array();
    if(!empty($_POST['nome_livro'])):
        if(!empty($_POST['editora_livro'])):
            if(!empty($_POST['autor_livro'])):
                if(!empty($_POST['genero_livro'])):
                    if(!empty($_POST['ano_publicacao'])):
                        $anoPublicacaoString = $_POST['ano_publicacao'];
                        $anoPublicacao = intval($anoPublicacaoString);
                        if ($_POST['ano_publicacao'] <= date('Y') && gettype($anoPublicacao) == "integer"):
                            $nomeLivro = $_POST['nome_livro'];
                            $editoraLivro = $_POST['editora_livro'];
                            $autorLivro = $_POST['autor_livro'];
                            $generoLivro = $_POST['genero_livro'];
                            $modificado = date('Y-m-d H:i:s');
                            if ($arrayResultado['nome_livro']==$nomeLivro && $arrayResultado['editora_livro']==$editoraLivro && $arrayResultado['autor_livro']==$autorLivro &&
                                $arrayResultado['genero_livro']==$generoLivro && $arrayResultado['ano_publicacao']==$anoPublicacaoString):
                                $erros[] = "<li>Precisa ser alterado algum registro.</li>";
                            else:
                                $alteraRegistro = "UPDATE livros SET nome_livro = '$nomeLivro', editora_livro = '$editoraLivro', autor_livro = '$autorLivro',
                                genero_livro = '$generoLivro', ano_publicacao = '$anoPublicacao', modificado = '$modificado' WHERE id_livros = '$idLivro'";
                                mysqli_query($connect,$alteraRegistro);
                                $acertos = array();
                                $acertos[] = "<ul><li class='sucesso'>Editado com sucesso.</li><ul>";
                                $resultadoBusca = mysqli_query($connect, $buscaLivro);
                                $arrayResultado = mysqli_fetch_array($resultadoBusca);
                            endif;
                        else:
                            $erros[] = "<li>Campo ano de publicação precisa ser numérico.</li>";
                        endif;
                    else:
                        $erros[] = "<li>Campo ano de publicação não preeenchido.</li>";
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
        $erros[] = "<li>Campo nome do livro não preeenchido. </li>";
    endif;
endif;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Livraria</title>
    <link rel="stylesheet" href="../style/home.css">
</head>
<body>
    <div class="container grid">
    <?php include_once 'cabecalho.php'; ?>

    <div class="conteudo banner">
        <form action="<?php echo $_SERVER['PHP_SELF']; echo "?id=$idLivro"; ?>" method="POST" class="formulario" autocomplete="off">
            <input type="text" name="nome_livro" class="input nome_livro" placeholder="Título" value="<?php echo $arrayResultado['nome_livro'] ?>">
            <input type="text" name="editora_livro" class="input editora_livro" placeholder="Editora" value="<?php echo $arrayResultado['editora_livro'] ?>">
            <input type="text" name="autor_livro" class="input autor_livro" placeholder="Autor" value="<?php echo $arrayResultado['autor_livro'] ?>">
            <input type="text" name="genero_livro" class="input genero_livro" placeholder="Gênero" value="<?php echo $arrayResultado['genero_livro'] ?>">
            <input type="text" name="ano_publicacao" class="input ano_publicacao" placeholder="Ano de Publicação" value="<?php echo $arrayResultado['ano_publicacao'] ?>">
            <input type="submit" value="EDITAR" class="input btn_enviar" name="enviar">
        </form>
        <?php
            if(!empty($erros)):
                foreach ($erros as $erro):
                    echo "<ul class='erro'>$erro</ul>";
                endforeach;
            elseif(!empty($acertos)):
                foreach ($acertos as $acerto):
                    echo $acerto;
                endforeach;
            endif;
        ?>
    </div>

    <?php include_once 'rodape.php'; ?>
    </div>
</body>
</html>