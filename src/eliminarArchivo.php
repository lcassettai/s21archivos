<?php
session_start();

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])){
    header('Location: login.php?sinLoguear=true');
    exit;
}

include_once 'db/DbConection.php';

if(!isset($_GET['id_archivo']) || empty($_GET['id_archivo'])){   
    header('Location: misArchivos.php?eliminarError=true') ;
    exit;
}

try{
    $DbConection = new DbConection();
    $pdo = $DbConection->dbConnect();

    $id_archivo = $_GET['id_archivo'];

    $data = [
        'archivo' => $id_archivo,
        'usuario' => $_SESSION['usuario']['id_usuario'],
    ];

    $sql = "SELECT * FROM archivos WHERE id_archivo = :archivo AND id_usuario = :usuario";
    $stmt= $pdo->prepare($sql);
    
    // execute the statement
    if ($stmt->execute($data)) {
        if($stmt->rowCount() == 0){
            header('Location: misArchivos.php?eliminarError=true') ;
            exit;
        }
    }
    $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "DELETE FROM archivos WHERE id_archivo = :archivo AND id_usuario = :usuario";
    $stmt= $pdo->prepare($sql);
    
    // execute the statement
    if ($stmt->execute($data)) {     
        unlink("../uploads/".$archivo['archivo']);

        header('Location: misArchivos.php?eliminarExito=true') ;
        exit;
    }
  
   
}catch (PDOException $e) {
    header('Location: misArchivos.php?eliminarError=true') ;
    exit;
}

?>