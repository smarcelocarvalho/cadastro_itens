<?php
require_once '../../model/db_connect.php';

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;

// Define fuso horário de brasilia
date_default_timezone_set('America/Sao_Paulo');

if(isset($_POST['enviar'])):
    $erros = array();
    if(!empty($_POST['nome_livro'])):
        if(!empty($_POST['editora_livro'])):
            if(!empty($_POST['autor_livro'])):
                if(!empty($_POST['genero_livro'])):
                    if(!empty($_POST['ano_publicacao'])):
                            $anoPublicacao = intval($_POST['ano_publicacao']);
                        if ($_POST['ano_publicacao'] <= date('Y') && gettype($anoPublicacao) == "integer"):

                            // Verificação de arquivos
                            if (empty($_FILES["arquivo_livro"]["name"])):
                                $nomeLivro = $_POST['nome_livro'];
                                $editoraLivro = $_POST['editora_livro'];
                                $autorLivro = $_POST['autor_livro'];
                                $generoLivro = $_POST['genero_livro'];
                                $criado = date('Y-m-d H:i:s');
                                $insereRegistro = "INSERT INTO livros (nome_livro, editora_livro, autor_livro, genero_livro, ano_publicacao, criado, modificado ) VALUES (
                                    '$nomeLivro','$editoraLivro','$autorLivro','$generoLivro','$anoPublicacao', '$criado', '$criado')";
                                mysqli_query($connect,$insereRegistro);
                                $acertos = array();
                                $acertos[] = "<ul><li class='sucesso'>Cadastro realizado com sucesso.</li><ul>";
                            else :
                                // Caminho para upload de arquivo
                                $diretorioDestino = dirname(__FILE__,3)."\arquivos\\";
                                $nomeArquivo = basename($_FILES["arquivo_livro"]["name"]);
                                $nomeCompletoDestino = $diretorioDestino.$nomeArquivo;
                                $tipoArquivo = pathinfo($nomeCompletoDestino,PATHINFO_EXTENSION);
                                $tiposPermitidos = array('jpg','png','jpeg','gif','pdf');
                                $nomeArquivo = "file".uniqid();
                                $nomeCompletoDestino = $diretorioDestino.$nomeArquivo.".".$tipoArquivo;
                                
                                // Verifica se contem primeiro valor no array
                                if (in_array($tipoArquivo, $tiposPermitidos)):
                                    // Subir arquivo no servidor
                                    if (move_uploaded_file($_FILES["arquivo_livro"]["tmp_name"], $nomeCompletoDestino)):
                                        // Informações doarquivo inserido no banco
                                        $selectInformacaoArquivo = "INSERT INTO arquivos (nome_arquivo, data_upload ) VALUES ('$nomeArquivo', NOW() )";
                                        $resultadoInsercao = mysqli_query($connect,$selectInformacaoArquivo);
                                        if ($resultadoInsercao):
                                            //Cadastro livro no banco com informação do arquivo
                                            $nomeLivro = $_POST['nome_livro'];
                                            $editoraLivro = $_POST['editora_livro'];
                                            $autorLivro = $_POST['autor_livro'];
                                            $generoLivro = $_POST['genero_livro'];
                                            $criado = date('Y-m-d H:i:s');
                                            // Busca ID na ultima insert
                                            $id_arquivo = mysqli_insert_id($connect);
                                            // Inserido nome da coluna id_arquivo
                                            $insereRegistro = "INSERT INTO livros (nome_livro, editora_livro, autor_livro, genero_livro, ano_publicacao, id_arquivo, criado, modificado ) VALUES (
                                                '$nomeLivro','$editoraLivro','$autorLivro','$generoLivro','$anoPublicacao', '$id_arquivo', '$criado', '$criado')";
                                            mysqli_query($connect,$insereRegistro);
                                            $acertos = array();
                                            $acertos[] = "<ul><li class='sucesso'>Cadastro realizado, com arquivo $nomeArquivo armazenado com sucesso.</li><ul>";
                                        else:
                                            $erros[] = "Erro no armazenamento de informções do arquivo.";
                                        endif; 
                                    else:
                                        $erros[] = "Erro no armazenamento fisíco do arquivo.";
                                    endif;
                                else:
                                    $erros[] = "Permitido somente (JPG, JPEG, PNG, GIF, & PDF).";
                                endif;
                            endif;
                        else:
                            $erros[] = "<li>Campo ano de publicação não possível.</li>";
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
    <title>CADASTRO DE LIVROS</title>
    <link rel="stylesheet" href="../style/home.css">
</head>
<body>
    <div class="container grid">
    <?php include_once 'cabecalho.php'; ?>

    <div class="conteudo banner">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="formulario" enctype="multipart/form-data">
            <input type="text" name="nome_livro" class="input nome_livro" placeholder="Título">
            <input type="text" name="editora_livro" class="input editora_livro" placeholder="Editora">
            <input type="text" name="autor_livro" class="input autor_livro" placeholder="Autor">
            <input type="text" name="genero_livro" class="input genero_livro" placeholder="Gênero">
            <input type="text" name="ano_publicacao" class="input ano_publicacao" placeholder="Ano de Publicação (YYYY)">
            <label>ENVIAR ARQUIVO<input type="file" name="arquivo_livro" class="input arquivo_livro"></label>
            <input type="submit" value="CADASTRAR" class="input btn_enviar" name="enviar">
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