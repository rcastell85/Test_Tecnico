<?php 
    require 'database.php';

    

    if (!empty($_POST["ciudad_fav"])) {
       
        $nombreCiudad = $_POST["ciudad_fav"]; 
        $id_usuario = $_POST["id_usuario_fav"];
        $id = $_POST["id_usuario_fav"];

        $sql = "INSERT INTO favoritos (nombreCiudad, id_usuario) VALUES (:nombreCiudad, :id_usuario)";
        $stm = $db->prepare($sql);
        $stm->bindParam(':nombreCiudad',$nombreCiudad);
        $stm->bindParam(':id_usuario', $id_usuario);

        $stm->execute();

        header('Location: /Test-tecnico/index.php');
    };
?>