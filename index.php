<?php
    require_once 'model/db_connect.php';

    //$idFixo = 1;
    //$loginFixo = "teste";
    //$senhaFixo = 123;
    
    session_start();
    $_SESSION['acesso'] = NULL;

    if (isset($_POST['enviar'])):
        
        $erros = array();
        $login = mysqli_escape_string($connect,$_POST['login']);
        $senha = mysqli_escape_string($connect,$_POST['senha']);

        if (empty($senha) or empty($login)):
            $erros[] = "<p>Todos os campos devem ser preenchidos. <p>";
        else:
            $senha = md5($senha);
            $buscaUsuario = "SELECT * FROM usuarios WHERE login = '$login' ";
            $resultadoBusca = mysqli_query($connect, $buscaUsuario);

            if (mysqli_num_rows($resultadoBusca)>0):
                $buscaUsuarioSenha = "SELECT * FROM usuarios WHERE login = '$login' and pass = '$senha'";
                $resultadoBusca = mysqli_query($connect,$buscaUsuarioSenha);
                $dadosUsuario = mysqli_fetch_array($resultadoBusca);
                
                if (mysqli_num_rows($resultadoBusca) == 1):
                    $_SESSION['acesso'] = TRUE;
                    $_SESSION['id_user'] = $dadosUsuario['id_user'];
                    header('Location: view/pag/home.php');
                else:
                    $erros[] = "<p>Login correto porém senha inválida. <p>";
                endif;

            else:
                $erros[] = "<p>Login inexistente. </p>";
            endif;
        endif;
    endif;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="view/style/index.css">
    <title>Login</title>
</head>
<body>
    <div class="box">
        <div class="formulario">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="text" name="login" class="login" placeholder="Login" autocomplete="off"><br>
                <input type="password" name="senha" class="senha" placeholder="Senha"><br>
                <input type="submit" name="enviar" class="enviar" value="ENVIAR">
            </form>
        </div>
        <div class="alerta">
            <?php
            if (!empty($erros)):
                foreach ($erros as $erro):
                    echo $erro;
                endforeach;
            endif;
            ?>
        </div>
    </div>
</body>
</html>