<?php
    session_start();
    
    require 'database.php';

    //-------------------llamamos a base de datos-----------------//
    if (isset($_SESSION["user_id"])) {
        $datos = $db->prepare('SELECT * FROM users WHERE id = :id');
        $datos->bindParam(':id', $_SESSION["user_id"]);
        $datos->execute();
        $resultado = $datos->fetch(PDO::FETCH_ASSOC);

        $user = null;

        if (count($resultado) > 0) {
            $user = $resultado;
        }

        

        $datos_favoritos = $db->prepare('SELECT * FROM favoritos WHERE id_usuario = :id_usuario');
        $datos_favoritos->bindParam(':id_usuario', $_SESSION["user_id"]);
        $datos_favoritos->execute();
        $result = $datos_favoritos->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $ciudades = $result;
        }
        
    }
    
    //----------------------Traemos los datos de la Api ----------------//

    function traerDatos($ciudad, $apiKey){ 

        $ch = curl_init(); // Inicio el CURL
        curl_setopt($ch, CURLOPT_URL, "api.openweathermap.org/data/2.5/weather?q=".$ciudad."&appid=".$apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch); // Respuesta
        curl_close($ch);

        $datos = json_decode($res, true);

        return $datos;
    }

    //------------------Busqueda principal en la Api Rest-----------------/
    if (isset($_GET["city"]) && !empty($_GET["city"])) {
        //Llamado de la funcion que trae datos de Api
        $data = traerDatos($_GET["city"], "65c791035fe1d9c58a851822d1721248"); 
        
    //----------------------Llevamos temperaturas de grados Kelvin a grados Centigrados--------------//
        if ($data && $data["cod"] != "404") {
            $temp_actual = $data["main"]["temp"] - 273.15;
            $minima = $data["main"]["temp_min"] - 273.15;
            $maxima = $data["main"]["temp_max"] - 273.15;
        }
       
    } 

  
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
    <title>Home</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="" style="color: honeydew; font-size: 30px;">ClimApp</a>
                </div>
                <?php if(!empty($user)) :?>
                    <ul class="nav navbar-nav">
                        <li>Bienvenido <?= $user["email"] ?></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="salir.php" style="color: honeydew;">Salir</a></li>
                    <?php else:?>
                    <ul class="nav">
                        <li style="padding-right: 30px;"><a href="registro.php" style="color: honeydew;">Registrarse</a></li>
                        <li style="padding-right: 30px;"><a href="iniciarSesion.php" style="color: honeydew;">Iniciar Sesion</a></li>
                    <?php endif;?>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container">
        <?php if(!empty($user)) :?>
           
        <h4>De cual ciudad deseas saber el clima actual?</h4>

        <form action="index.php" method="GET">
            <div class="input">
                <input type="text" name="city" class="form-control" placeholder="Buscar..."><br>
                <input type="submit" class="btn-success btn-lg" value="Buscar">
            </div>
        </form>
        
            <?php if(!empty($data) && $data["cod"] != "404") :?>
            <div class="content row">
                <div class="col-md-9">
                    <div class="data">
                        <div class="uno">
                            <div class="encabezado">
                                <h3 class="titulo-ciudad"><?= $data["name"] ?> - <?= $data["sys"]["country"]?></h3>
                                <form action="favoritos.php" method="post">
                                    <input type="hidden" value="<?= $data["name"] ?>" name="ciudad_fav">
                                    <input type="hidden" value="<?= $user["id"] ?>" name="id_usuario_fav"> 
                                    <button type="submit" class="btn-star"><i class="ifar" style="color: honeydew;">Favoritos</i></button>     
                                </form>
                            </div>
                            <p class="temp-principal"><?= $temp_actual ?> °C</p>
                            <p>Presion: <strong> <?= $data["main"]["pressure"]?> </strong></p>
                            <p>Humedad: <strong> <?= $data["main"]["humidity"]?> </strong></p>
                            <p>Minima: <strong> <?= $minima ?> °C </strong></p>
                            <p>Maxima: <strong> <?= $maxima ?> °C </strong></p>
                            <p>Velocidad del viento:<strong> <?= $data["wind"]["speed"] ?></strong></p>

                        </div>
                    </div>
                </div>
                <div class="col-md-3 lista-fav">

                    <h4>Lista de favoritos</h4> 
                    <?php if(isset($ciudades)){ 
                          for ($i=0; $i < count($ciudades); $i++) {  //llamamos funcion para consumir api
                          $datoCiudad = traerDatos($ciudades[$i]["nombreCiudad"], "65c791035fe1d9c58a851822d1721248"); ?>

                            <div class="fav">
                                <form action="index.php" method="GET">
                                    <input type="hidden" name="city" value="<?= $datoCiudad["name"] ?>">
                                    <button type="submit" style="background-color: rgb(75, 156, 221); border: none; color: honeydew;">
                                    <h6> <strong> <?= $datoCiudad["name"] ?> </strong></h6>
                                    <p style="font-size: 26px;"><?= $datoCiudad["main"]["temp"] - 273.15 ?> °C</p>
                                    </button>
                                </form>
                            </div>
                            <hr>
                    <?php }} ?>      
                         
                </div>
            </div>  
            <?php elseif(!empty($data) && $data["cod"] == "404") : ?>
                
                <div class="content row">
                    <div class="col-md-9">  
                        <h2 style="color: red;">Esta ciudad no existe!</h2>
                        <h2 style="color: red;">Intenta de nuevo</h2>
                    </div>

                    <div class="col-md-3 lista-fav">

                        <h4>Lista de favoritos</h4> 
                        <?php if(isset($ciudades)){ 
                            for ($i=0; $i < count($ciudades); $i++) {  //llamamos funcion para consumir api
                            $datoCiudad = traerDatos($ciudades[$i]["nombreCiudad"], "65c791035fe1d9c58a851822d1721248"); ?>

                                <div class="fav">
                                    <form action="index.php" method="GET">
                                        <input type="hidden" name="city" value="<?= $datoCiudad["name"] ?>">
                                        <button type="submit" style="background-color: rgb(75, 156, 221); border: none; color: honeydew;">
                                        <h6> <strong> <?= $datoCiudad["name"] ?> </strong></h6>
                                        <p style="font-size: 26px;"><?= $datoCiudad["main"]["temp"] - 273.15 ?> °C</p>
                                        </button>
                                    </form>
                                </div>
                                <hr>
                        <?php }} ?> 
                    </div>
                </div> 

            <?php else : ?>

                <div class="content row">
                    <div class="col-md-9">  

                    </div>

                    <div class="col-md-3 lista-fav">

                        <h4>Lista de favoritos</h4> 
                        <?php if(isset($ciudades)){ 
                            for ($i=0; $i < count($ciudades); $i++) {  //llamamos funcion para consumir api
                            $datoCiudad = traerDatos($ciudades[$i]["nombreCiudad"], "65c791035fe1d9c58a851822d1721248"); ?>

                                <div class="fav">
                                    <form action="index.php" method="GET">
                                        <input type="hidden" name="city" value="<?= $datoCiudad["name"] ?>">
                                        <button type="submit" style="background-color: rgb(75, 156, 221); border: none; color: honeydew;">
                                        <h6> <strong> <?= $datoCiudad["name"] ?> </strong></h6>
                                        <p style="font-size: 26px;"><?= $datoCiudad["main"]["temp"] - 273.15 ?> °C</p>
                                        </button>
                                    </form>
                                </div>
                                <hr>
                        <?php }} ?> 
                    </div>
                </div> 
            <?php endif; ?>
        
      
        <?php else :?>

            <h1>Bienvenido a tu aplicacion del clima!</h1>

            <h3 style="padding-top: 150px;">Si quieres saber toda la informacion del clima en el mundo <a href="registro.php"><strong style="color: green;"> registrate</strong></a></h3><br>

            <h3 style="padding-top: 10px;">Si ya estas registrado <a href="iniciarSesion.php"><strong style="color: green;" >inicia sesion</strong></a></h3>

        <?php endif; ?>
    </div>
</body>
</html>