<?php
require_once '../../model/db_connect.php';

session_start();

if(!isset($_SESSION['acesso'])):
    session_destroy();
    header('Location: /index.php');
endif;

if(isset($_GET['id'])):
    $idApagar = $_GET['id'];
    $deletaRegistro = "DELETE FROM livros WHERE id_livros = $idApagar";
    $resultadoDeletar = mysqli_query($connect,$deletaRegistro);
endif;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>REGISTROS</title>
    <link rel="stylesheet" href="../style/home.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>
    <div class="container grid">
    <?php include_once 'cabecalho.php'; ?>

    <div class="conteudo banner">
        <?php
            // Paginação
            // Verifica se o parametro pagina foi inicializado
            if (isset($_GET['pagina'])):
                $pagina = $_GET['pagina'];
            else:
                $pagina = 1;
            endif;

            $selectBusca = "SELECT id_livros, nome_livro, editora_livro, autor_livro, genero_livro, ano_publicacao FROM livros ORDER BY nome_livro ";
            $resultadoBusca = mysqli_query($connect, $selectBusca);
            $numeroDeResultados = mysqli_num_rows($resultadoBusca);
            $registrosPorPagina = 13;

            // Calculo do número de paginas totais
            $numeroPaginas = ceil($numeroDeResultados/$registrosPorPagina);

            // Calculo do inicio dos registros
            $inicioPagina = (($registrosPorPagina*$pagina)-$registrosPorPagina);

            // Select de busca de resultados por pagina
            $selectBusca = "$selectBusca LIMIT $inicioPagina,$registrosPorPagina"; 
            $resultadoBusca = mysqli_query($connect, $selectBusca);
            $resultadosBuscaPagina = mysqli_num_rows($resultadoBusca);

            if ($numeroDeResultados>0):
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
                                echo "<td>".$registros[1]."</td>";
                                echo "<td>".$registros[2]."</td>";
                                echo "<td>".$registros[3]."</td>";
                                echo "<td>".$registros[4]."</td>";
                                echo "<td>".$registros[5]."</td>";
                                echo "<td class='icon'><a class='icon' href='editar.php?id=$registros[0]'><i class='material-icons'>edit</i></a></td>";
                                echo "<td class='icon'><a class='icon' href='registro.php?id=$registros[0]'><i class='material-icons'>delete</i></a></td>";
                            echo "</tr>";
                        endforeach;
                    ?>
                    <tr>
                        <td colspan="5" class="paginacao">
                            <a class="pag" href="registro.php?pagina=<?php if($pagina==1){echo $pagina;}else{echo $pagina-1;} ?>">&lt</a>
                            <?php
                            for($i = 1; $i < $numeroPaginas + 1 ; $i++):
                                echo "<a class='pag' href='registro.php?pagina=$i'>$i</a>";
                            endfor;
                            ?>
                            <a class="pag" href="registro.php?pagina=<?php if($pagina<$numeroPaginas){echo $pagina+1;}else{echo $pagina;} ?>">&gt</a>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            else:
                echo "<ul class='erro'><li>Nenhum registro encontrado.</li></ul>";
            endif;
        ?>
    </div>

    <?php include_once 'rodape.php'; ?>
    </div>
</body>
</html>