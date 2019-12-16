<?php 
    require 'database.php';
    
    $mensaje = '';

    if ($_POST) {
        
        if ($_POST["pass"] != $_POST["repetir_pass"]) {
            $mensaje = 'Las contraseñas no coinciden';
        } elseif(empty($_POST["email"]) || empty($_POST["pass"]) || empty($_POST["repetir_pass"])) {
            $mensaje = 'Debes completar todos los datos';
        } else {
            $datos = $db->prepare('SELECT * FROM users WHERE email=:email');
            $datos->bindParam(':email', $_POST["email"]);    
            $datos->execute();

            $user = $datos->fetch(PDO::FETCH_ASSOC);
            $mensaje = "";
        
            if ($user) {
               $mensaje = "El Usuario ya existe, intenta con otro";
                
               // header('Location: /Test-tecnico/salir.php');
            }   else {
                $email = $_POST["email"];
                $pass = password_hash($_POST["pass"], PASSWORD_BCRYPT); 

                $sql = "INSERT INTO users (email, pass) VALUES (:email, :pass)";
                $stm = $db->prepare($sql);
                $stm->bindParam(':email',$email);
                $stm->bindParam(':pass', $pass);

                if ($stm->execute()) {
                    $mensaje = '<span class="text-success"><strong>Te has registrado con exito! </strong><a href="iniciarSesion.php" style="color: honeydew;"> Iniciar Sesion</a></span>';
                } else {
                    $mensaje = "Ha ocurrido un error al registrarse";
                };
            }
        };
    };
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <title>Registro</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand titulo" href="" style="color: honeydew; font-size: 30px;">ClimApp</a>
                </div>
                <ul class="nav">
                    <li style="padding-right: 30px;"><a href="registro.php" style="color: honeydew;">Registrarse</a></li>
                    <li style="padding-right: 30px;"><a href="iniciarSesion.php" style="color: honeydew;">Iniciar Sesion</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container registro">
        
        <form class="form-horizontal form" action="registro.php" method="POST">
            <h2>Registrate</h2>
            <div class="form-group">
                <label class="control-label col-sm-2" for="email"></label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" placeholder="Introduce un email" name="email">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="pass"></label>
                <div class="col-sm-10">          
                    <input type="password" class="form-control" id="pass" placeholder="Introduce tu contraseña" name="pass">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4" for="repetir_pass"></label>
                <div class="col-sm-10">          
                    <input type="password" class="form-control" id="pass" placeholder="Repite tu contraseña" name="repetir_pass">
                </div>
            </div>

            <?php if(!empty($mensaje)) :?>
                <span class="text-danger"><?= $mensaje ?></span>
            <?php endif;?>
            <div class="form-group" style="padding-top: 50px;">        
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-success btn-lg">Registrarme</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

