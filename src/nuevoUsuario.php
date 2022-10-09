<?php 
session_start();
include_once 'db/DbConection.php';

if(isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])){
    header('Location: misArchivos.php');
    exit;
}

$mensaje = [];

if(isset($_POST['btnRegistrarme'])){ 
    
    if(empty($_POST['nombre']) ||
    empty($_POST['apellido']) ||
    empty($_POST['email']) ||
    empty($_POST['password']))
    {
        $mensaje = ["error","Debe completar todos los campos"];  
    }else{
        $nombre = filter_var($_POST['nombre'] , FILTER_SANITIZE_STRING);
        $apellido = filter_var($_POST['apellido'] , FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'] , FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'] , FILTER_SANITIZE_STRING);
    
        $DbConection = new DbConection();
        $pdo = $DbConection->dbConnect();
    
        try{
            $verificarEmail = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $verificarEmail->bindParam(':email', $email);
            $verificarEmail->execute();
            
            if($verificarEmail->rowCount() > 0){
                $mensaje = ["error","El email ya se encuentra registrado"];
            }else{
                $sql = "INSERT INTO usuarios(nombre,apellido,email,password) VALUES(?,?,?,?)";
                $resultado = $pdo->prepare($sql)->execute([$nombre,$apellido,$email,$password]);
                    
                if($resultado = true){
                    header('Location: login.php?usuarioCreado=true') ;  
                    exit;
                }else{
                     $mensaje = ["error","Algo salio mal"];
                }
            }
           
        }catch (PDOException $e) {
            $mensaje = ["error","Algo salio mal"];
        }
    }   
} 


include 'layout/header.php';

?>

<div class="container">
    <div class="d-flex justify-content-center align-items-center">
        <div class="mt-4 panel-login">
            <?php if(!empty($mensaje)): ?>
                <script>
                    Swal.fire({
                        confirmButtonColor: '#72b053',
                        confirmButtonText: 'Aceptar',
                        icon: '<?= $mensaje[0] ?>',
                        text: '<?= $mensaje[1] ?>',
                        })
                </script>
            <?php endif;?>
            <div class="div text-center">
                <img class="mb-4" src="../assets/img/logo.png" alt="" width="72" height="57">
                <h3>Nuevo usuario</h3>
            </div>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="obligatorio">*</span></label> 
                    <input type="text" class="form-control" id="nombre" aria-describedby="nombre" name="nombre" required value="<?= !empty($_POST['nombre']) ? $_POST['nombre'] : '';?> ">
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido <span class="obligatorio">*</span></label>
                    <input type="text" class="form-control" id="apellido" aria-describedby="apellido" name="apellido" required value="<?= !empty($_POST['apellido']) ? $_POST['apellido'] : '';?> ">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="obligatorio">*</span></label>
                    <input type="email" class="form-control" id="email" aria-describedby="email" name="email" required value="<?= !empty($_POST['email']) ? $_POST['email'] : '';?> ">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="obligatorio">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="login.php" class="btn btn-danger">Cancelar</a>
                    <button type="submit" class="btn btn-success" name="btnRegistrarme">Registrarme</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php  include 'layout/footer.php';?>
