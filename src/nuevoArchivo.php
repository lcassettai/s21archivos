<?php  
session_start();

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])){
    header('Location: login.php?sinLoguear=true');
}

$_SESSION['menu'] = 'nuevo_archivo';

if(isset($_POST['submit'])){
    include_once 'db/DbConection.php';

    $mensaje = [];
    $hayErrores = 0;

    //Directorio donde se va a subir el archivo nuevo
    $directorioDestino = "../uploads/";

    //Generamos un nombre random para el archivo con su extension original
    $nombreArchivoOriginal = basename($_FILES["archivoSubir"]["name"]);
    $extension =  strtolower(pathinfo($nombreArchivoOriginal,PATHINFO_EXTENSION));
    $nuevoNombre = uniqid() . '.' . $extension;
    $archivoDestino = $directorioDestino .  $nuevoNombre ;

    // Verificar el tamaño del archivo
    if ($_FILES["archivoSubir"]["size"] > 5000000) {
        $hayErrores = 1;
        $mensaje = ['error','El archivo es demasiado grande, el tamaño maximo es de 5Mb'];  
    }

    // Validar el tipo de archivo
    if($extension != "jpg" && $extension != "png" && $extension != "jpeg"
    && $extension != "gif" && $extension != "pdf" && $extension != "docx"
    && $extension != "xls" && $extension != "txt" ) {
        $hayErrores = 1;
        $mensaje = ['error','Los sentimos, las extensiones permitidas son JPG, JPEG, PNG, GIF, PDF, DOCX, XLS, TXT.'];
    }

    if(empty($_POST['nombre'])){
        $mensaje = ["error","Debe completar todos los campos"];  
    }elseif(strlen(empty($_POST['nombre']))){
        $mensaje = ["error","El nombre del archivo no puede ser mayor a 40 caracteres"];  
    }

    // Verificamos si no hubo algun error de validacion
    if ($hayErrores == 0) {
        if (move_uploaded_file($_FILES["archivoSubir"]["tmp_name"], $archivoDestino)) {
            $DbConection = new DbConection();
            $pdo = $DbConection->dbConnect();

            $nombre = filter_var($_POST['nombre'] , FILTER_SANITIZE_STRING);
            $descripcion = filter_var($_POST['descripcion'] , FILTER_SANITIZE_STRING);
            $compartir = isset($_POST['compartir']) ? 1 : 0 ;
            $tamanoArchivo = $_FILES["archivoSubir"]["size"];

            try{
                $sql = "INSERT INTO archivos(archivo,nombre,tamano,extension,id_usuario,descripcion,compartir,fecha) VALUES(?,?,?,?,?,?,?,NOW())";
                $resultado = $pdo->prepare($sql)->execute([$nuevoNombre,$nombre,$tamanoArchivo,$extension,$_SESSION['usuario']['id_usuario'],$descripcion,$compartir,]);

                if($resultado = true){
                    $mensaje = ['success','El archivo se subio con exito!'];
                }else{
                    $mensaje = ["error","Algo salio mal al subir el archivo a la base de datos."];
                }               
            }catch (PDOException $e) {
                $mensaje = ["error","Algo salio mal al subir el archivo a la base de datos."];
            }
        } else {
            $mensaje = ['error', 'Lo sentimos, ocurrio un error al subir el archivo al servidor.!'];
        }
    }
}

include 'layout/header.php';
include 'layout/menu.php';

?>

<div class="container jumbotron">
    <div class="row">
        <div class="mt-3">
            <h1 class="text-center">SUBIR NUEVO ARCHIVO</h1>
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
            <div class="panel">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="col-auto">
                    <label for="nombre" class="form-label"><strong>Nombre del archivo <span class="obligatorio">*</span></strong></label>
                    <input class="form-control form-control-lg" id="nombre" type="text" name="nombre" maxlength="40" required>    
                </div>
                <br>
                <div class="col-auto">
                    <label for="descripcion" class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                <br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="compartir" name="compartir">
                    <label class="form-check-label" for="compartir" >
                        Compartir archivo
                    </label>
                </div>
                <br>
                <div class="col-auto">
                    <label for="archivo" class="form-label"><strong>Seleccione un archivo (Max 2Mb) <span class="obligatorio">*</span></strong></label>
                    <input class="form-control form-control-lg" id="archivo" type="file" name="archivoSubir">    
                </div>
                <br>
                <div class="col-auto">
                    <button type="submit" class="btn btn-danger btn-lg mb-3" name="submit">Subir Archivo</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<?php  include 'layout/footer.php';?>