<?php
    $idFixo = 1;
    $loginFixo = "teste";
    $senhaFixo = 123;
    
    session_start();
    $_SESSION['acesso'] = NULL;

    if (isset($_POST['enviar'])):
    
        $erros = array();
        $login = $_POST['login'];
        $senha = $_POST['senha'];
    
        if (empty($senha) or empty($login)):
            $erros[] = "<p>Todos os campos devem ser preenchidos. <p>";
        else:
            if ($login == $loginFixo):
                if ($senha == $senhaFixo):
                    $_SESSION['acesso'] = TRUE;
                    $_SESSION['id_usuario'] = $idFixo;
                    header('Location: view/pag/home.php');
    
                else:
                    $erros[] = "<p>Login correto porém senha inválida. <p>";
                endif;
        
            else:
                $erros[] = "<p>Login não existente. </p>";
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