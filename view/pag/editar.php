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

// Dados arquivo para exibição (MESMOS DADOS UTILIZADOS NO IF ABAIXO)
$id_arquivo_antigo = $arrayResultado['id_arquivo'];
$selectArquivoAntigo = "SELECT * FROM arquivos WHERE id_arquivo = $id_arquivo_antigo";
$arrayArquivoAntigo = mysqli_fetch_array(mysqli_query($connect,$selectArquivoAntigo));

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
                                $arrayResultado['genero_livro']==$generoLivro && $arrayResultado['ano_publicacao']==$anoPublicacaoString && empty($_FILES["arquivo_livro"]["name"])):
                                $erros[] = "<li>Precisa ser alterado algum registro.</li>";
                            else:
                                if (!empty($_FILES["arquivo_livro"]["name"])):
                                
                                    // Caminho para upload de arquivo
                                    // Nome original do arquivo para buscar extensão
                                    $nomeArquivo = basename($_FILES["arquivo_livro"]["name"]);
                                    $tipoArquivo = pathinfo($nomeArquivo,PATHINFO_EXTENSION);
                                    $tiposPermitidos = array('jpg','png','jpeg','gif','pdf');
                                    // Renomeia arquivo para evitar conflito
                                    $nomeArquivo = "file".uniqid().".".$tipoArquivo;
                                    // Parametro 3 significa que está voltando 3 pastas
                                    $diretorioDestino = dirname(__FILE__,3)."\arquivos\\";
                                    $nomeCompletoDestino = $diretorioDestino.$nomeArquivo;
                                    
                                    // Verifica se contem primeiro valor no array
                                    if (in_array($tipoArquivo, $tiposPermitidos)):
                                        // Subir arquivo no servidor
                                        if (move_uploaded_file($_FILES["arquivo_livro"]["tmp_name"], $nomeCompletoDestino)):
                                            // Informações do arquivo inserido no banco
                                            $selectInformacaoArquivo = "INSERT INTO arquivos (nome_arquivo, data_upload ) VALUES ('$nomeArquivo', NOW() )";
                                            $resultadoInsercao = mysqli_query($connect,$selectInformacaoArquivo);
                                            // Busca ID na ultima insert
                                            $id_arquivo = mysqli_insert_id($connect);

                                            // Alterando status arquivo antigo banco
                                            $id_arquivo_antigo = $arrayResultado['id_arquivo'];
                                            $selectArquivoAntigo = "SELECT * FROM arquivos WHERE id_arquivo = $id_arquivo_antigo";
                                            $arrayArquivoAntigo = mysqli_fetch_array(mysqli_query($connect,$selectArquivoAntigo));
                                            $updateArquivoAntigo = "UPDATE arquivos SET status = '0' WHERE id_arquivo = $id_arquivo_antigo ";
                                            mysqli_query($connect,$updateArquivoAntigo);
                                            // Removendo arquivo antigo fisicamente
                                            $caminhoCompletoArquivoAntigo = $diretorioDestino.$arrayArquivoAntigo['nome_arquivo'];
                                            $acertos = array();
                                            if (!unlink($caminhoCompletoArquivoAntigo)):
                                                $erros[] = "<li>Erro ao remover arquivo antigo associado.</li>";
                                            else:
                                                $acertos[] = "<li class='sucesso'>Arquivo antigo removido.</li>"; 
                                            endif;

                                            if ($resultadoInsercao):
                                                // Upadte SQL (sem alteração de arquivo)
                                                $alteraRegistro = "UPDATE livros SET nome_livro = '$nomeLivro', editora_livro = '$editoraLivro', autor_livro = '$autorLivro',
                                                genero_livro = '$generoLivro', ano_publicacao = '$anoPublicacao', id_arquivo = '$id_arquivo', modificado = '$modificado' WHERE id_livros = '$idLivro'";
                                                mysqli_query($connect,$alteraRegistro);
                                                $acertos[] = "<li class='sucesso'>Editado com sucesso.</li>";
                                                // Busca realizada novamente para preencher campos de input com novos dados
                                                $resultadoBusca = mysqli_query($connect, $buscaLivro);
                                                $arrayResultado = mysqli_fetch_array($resultadoBusca);
                                                $id_arquivo_antigo = $arrayResultado['id_arquivo'];
                                                $selectArquivoAntigo = "SELECT * FROM arquivos WHERE id_arquivo = $id_arquivo_antigo";
                                                $arrayArquivoAntigo = mysqli_fetch_array(mysqli_query($connect,$selectArquivoAntigo));

                                            else:
                                                $erros[] = "<li>Erro no armazenamento de informações do arquivo.</li>";
                                            endif;
                                        else:
                                            $erros[] = "<li>Erro no armazenamento fisíco do arquivo.</li>";
                                        endif;
                                    else:
                                        $erros[] = "<li>Permitido somente (JPG, JPEG, PNG, GIF, & PDF).</li>";
                                    endif;
                                else:
                                    // Upadte SQL (sem alteração de arquivo)
                                    $alteraRegistro = "UPDATE livros SET nome_livro = '$nomeLivro', editora_livro = '$editoraLivro', autor_livro = '$autorLivro',
                                    genero_livro = '$generoLivro', ano_publicacao = '$anoPublicacao', modificado = '$modificado' WHERE id_livros = '$idLivro'";
                                    mysqli_query($connect,$alteraRegistro);
                                    $acertos = array();
                                    $acertos[] = "<li class='sucesso'>Editado com sucesso.</li>";
                                    // Busca realizada novamente para preencher campos de input com novos dados
                                    $resultadoBusca = mysqli_query($connect, $buscaLivro);
                                    $arrayResultado = mysqli_fetch_array($resultadoBusca);
                                endif;
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
    <title>EDITAR REGISTRO</title>
    <link rel="stylesheet" href="../style/home.css">
</head>
<body>
    <div class="container grid">
    <?php include_once 'cabecalho.php'; ?>

    <div class="conteudo banner flex_conteudo">
        <form action="<?php echo $_SERVER['PHP_SELF']; echo "?id=$idLivro"; ?>" method="POST" class="formulario_edit" autocomplete="off" enctype="multipart/form-data">
            <input type="text" name="nome_livro" class="input nome_livro" placeholder="Título" value="<?php echo $arrayResultado['nome_livro'] ?>">
            <input type="text" name="editora_livro" class="input editora_livro" placeholder="Editora" value="<?php echo $arrayResultado['editora_livro'] ?>">
            <input type="text" name="autor_livro" class="input autor_livro" placeholder="Autor" value="<?php echo $arrayResultado['autor_livro'] ?>">
            <input type="text" name="genero_livro" class="input genero_livro" placeholder="Gênero" value="<?php echo $arrayResultado['genero_livro'] ?>">
            <input type="text" name="ano_publicacao" class="input ano_publicacao" placeholder="Ano de Publicação" value="<?php echo $arrayResultado['ano_publicacao'] ?>">
            <label>ENVIAR ARQUIVO<input type="file" name="arquivo_livro" class="input arquivo_livro"></label>
            <input type="submit" value="EDITAR" class="input btn_enviar" name="enviar">
            <?php
                if(!empty($erros)):
                    foreach ($erros as $erro):
                        echo "<ul class='erro'>$erro</ul>";
                    endforeach;
                elseif(!empty($acertos)):
                    foreach ($acertos as $acerto):
                        echo "<ul>".$acerto."</ul>";
                    endforeach;
                endif;
            ?>
        </form>
        <div class = "img_arq">
            <h3>ARQUIVO ATUAL</h3>
            <img src="../../../arquivos/<?php echo $arrayArquivoAntigo['nome_arquivo']; ?>" alt="Arquivo Upload">
        </div>
    </div>

    <?php include_once 'rodape.php'; ?>
    </div>
</body>
</html>