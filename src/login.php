<?php 
session_start();

if(isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])){
    header('Location: misArchivos.php');
    exit;
}

include_once 'db/DbConection.php';

$mensaje = [];

if(isset($_POST['iniciarSesion'])){ 
    if(empty($_POST['email']) || empty($_POST['password'])){
        $mensaje = ["error","Debe completar todos los campos"]; 
    }else{
        $email = filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL);
        $password = filter_var($_POST['password'] , FILTER_SANITIZE_STRING);
    
        $DbConection = new DbConection();
        $pdo = $DbConection->dbConnect();
    
        try{
            $verificarUsuario = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND password = :password");
            $verificarUsuario->bindParam(':email', $email);
            $verificarUsuario->bindParam(':password', $password);
            $verificarUsuario->execute();
            
            if($verificarUsuario->rowCount() > 0){
                $usuario = $verificarUsuario->fetch(PDO::FETCH_ASSOC);
                $_SESSION["usuario"] = $usuario;
                header('Location: misArchivos.php');
                exit;
            }else{
                $mensaje = ["warning","El email o password es incorrecto o no se encuentra registrado"];
            }
           
        }catch (PDOException $e) {
            $mensaje = ["error","Algo salio mal"];
        }
    }
}else{
    if(isset($_GET['usuarioCreado']) && $_GET['usuarioCreado']){
        $_GET['usuarioCreado'] = null;
        $mensaje = ["success","Se creo el usuario exitosamente!"];
    }
    if(isset($_GET['sinLoguear']) && $_GET['sinLoguear']){
        $_GET['usuarioCreado'] = null;
        $mensaje = ["error","Tenes que iniciar sesion!"];
    }
}

include 'layout/header.php';

?> 
<main class="d-flex justify-content-center align-items-center main-login">
    <div class="form-login mt-2">
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
    <form method="post" action="">
        <div class="div text-center">
        <img class="mb-4" src="../assets/img/logo.png" alt="" width="72" height="57">
        </div>
        <h1 class="h3 mb-3 fw-normal text-center">Iniciar sesion</h1>
        <div class="form-floating">
            <input type="email" class="form-control mb-2" id="floatingInput" name="email">
            <label for="floatingInput">Email</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" name="password">
            <label for="floatingPassword">Password</label>
        </div>
        <div class="d-flex">
            <button type="submit" class="w-100 btn btn-lg btn-primary" name="iniciarSesion">Iniciar sesion</button>      
        </div>
    </form>
    <a class="mt-2 d-block text-center" href="nuevoUsuario.php">Aun no tengo usuario</a>
    </div>
</main>

<?php include 'layout/footer.php';?>