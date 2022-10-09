<?php
session_start();

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])){
    header('Location: login.php?sinLoguear=true');
    exit;
}

include_once 'db/DbConection.php';

if(!isset($_POST['id_archivo']) || empty($_POST['id_archivo'])){   
    header('Location: misArchivos.php?actualizarError=true') ;
    exit;
}

try{
    $DbConection = new DbConection();
    $pdo = $DbConection->dbConnect();

    $id_archivo = $_POST['id_archivo'];

    if(empty($_POST['nombre'])){
        header('Location: misArchivos.php?actualizarError=true') ;
        exit;
    }

 
    $nombre = filter_var($_POST['nombre'] , FILTER_SANITIZE_STRING);
    $descripcion = filter_var($_POST['descripcion'] , FILTER_SANITIZE_STRING);
    $compartir = isset($_POST['compartir']) ? 1 : 0 ;

    $data = [
        'archivo' => $id_archivo,
        'usuario' => $_SESSION['usuario']['id_usuario']
    ];
  
    //Verificamos que el archivo exista y le pertenezca al usuario
    $sql = "SELECT * FROM archivos WHERE id_archivo = :archivo AND id_usuario = :usuario";
    $stmt= $pdo->prepare($sql);
    
    // execute the statement
    if ($stmt->execute($data)) {
        if($stmt->rowCount() == 0){
            header('Location: misArchivos.php?actualizarError=true') ;
            exit;
        }
    }

    $data = [
        'archivo' => $id_archivo,
        'usuario' => $_SESSION['usuario']['id_usuario'],
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'compartir' => $compartir,
    ];
    
    $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "UPDATE archivos 
            SET nombre = :nombre , descripcion = :descripcion, fecha = NOW() , compartir = :compartir
            WHERE id_archivo = :archivo AND id_usuario = :usuario";
    $stmt= $pdo->prepare($sql);
    
    // execute the statement
    if ($stmt->execute($data)) {     
        header('Location: misArchivos.php?actualizarExito=true') ;
        exit;
    }
  
   
}catch (PDOException $e) {
    header('Location: misArchivos.php?actualizarError=true') ;
    exit;
}

?>