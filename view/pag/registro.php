<?php
require_once '../../model/db_connect.php';

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Livros</title>
    <link rel="stylesheet" href="../style/home.css">
</head>
<body>
    <div class="container grid">
    <?php include_once 'cabecalho.php'; ?>

    <div class="conteudo banner">
        <?php
            $selectBusca = "SELECT nome_livro, editora_livro, autor_livro, genero_livro, ano_publicacao FROM livros ORDER BY nome_livro";
            $resultadoBusca = mysqli_query($connect, $selectBusca);
            
            if (mysqli_num_rows($resultadoBusca)>0):
            ?>
            <div class="tabela">
                <table class="registros">
                    <tr>
                        <th>Título</th>
                        <th>Editora</th>
                        <th>Autor</th>
                        <th>Gênero</th>
                        <th>Ano</th>
                    </tr>
                    <?php
                        $arrayBusca = array();
                        while ($registros = mysqli_fetch_array($resultadoBusca)):
                            $arrayBusca[] = $registros;
                        endwhile;
                        foreach($arrayBusca as $registros):
                            echo "<tr>";
                                echo "<td>".$registros[0]."</td>";
                                echo "<td>".$registros[1]."</td>";
                                echo "<td>".$registros[2]."</td>";
                                echo "<td>".$registros[3]."</td>";
                                echo "<td>".$registros[4]."</td>";
                            echo "</tr>";
                        endforeach;
                    ?>
                </table>
            </div>
            <?php
            else:
                echo "Nenhum resultado encontrado.";
            endif;
        ?>
    </div>

    <?php include_once 'rodape.php'; ?>
    </div>
</body>
</html>