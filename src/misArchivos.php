<?php  
session_start();

if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])){
    header('Location: login.php?sinLoguear=true');
}

$_SESSION['menu'] = 'mis_archivos';

include_once 'db/DbConection.php';

try{
    $DbConection = new DbConection();
    $pdo = $DbConection->dbConnect();

    //Obtenemos todos los pedidos
    $obtenerArchivos = $pdo->prepare(
        "SELECT * 
        FROM archivos 
        WHERE id_usuario = :id_usuario order by fecha desc ");
    $obtenerArchivos->bindParam(':id_usuario',$_SESSION['usuario']['id_usuario']);
    $obtenerArchivos->execute();
    
    if($obtenerArchivos->rowCount() > 0){
        $archivos = $obtenerArchivos->fetchAll(PDO::FETCH_ASSOC);    
    }else{
        $archivos = [];
    }
   
}catch (PDOException $e) {
    $mensaje = ["error","Algo salio mal"];
}

include 'layout/header.php';
include 'layout/menu.php';

?>

<div class="container jumbotron">
    <div class="row">
        <div class="mt-3">
            <h1 class="text-center">MIS ARCHIVOS</h1>
            <div class='pb-2 pt-2'>
                <a href="nuevoArchivo.php" class="btn btn btn-success"><img src="../assets/icons/upload.svg" width="20"> &nbsp Subir nuevo archivo</a>
            </div>
            <div class="panel">
            <?php if(isset($_GET['actualizarError'])):?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        confirmButtonColor: '#72b053',
                        confirmButtonText: 'Aceptar',
                        text: 'Lo sentimos, Algo salio mal y no se pudieron actualizar los datos del archivo.!',
                        })
                </script>
            <?php endif; ?>
            <?php if(isset($_GET['eliminarError'])):?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        confirmButtonColor: '#72b053',
                        confirmButtonText: 'Aceptar',
                        text: 'Lo sentimos, Algo salio mal y no se eliminar el archivo.!',
                        })
                </script>
            <?php endif; ?>
            <?php if(isset($_GET['eliminarExito'])):?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        confirmButtonColor: '#72b053',
                        confirmButtonText: 'Aceptar',
                        text: 'El archivo se elimino de manera exitosa!!',
                        })
                </script>
            <?php endif; ?>
            <?php if(isset($_GET['actualizarExito'])):?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        confirmButtonColor: '#72b053',
                        confirmButtonText: 'Aceptar',
                        text: 'Se actualizo el archivo de manera exitosa!!',
                        })
                </script>
            <?php endif; ?>
            <div class="responsive-table">
            <table class="table">
            <thead>
                <tr>
                <th scope="col">Nombre</th>
                <th scope="col" class="text-center">Ultima Modificación</th>
                <th scope="col" class="text-center">Tamaño</th>
                <th scope="col" class="text-center">Compartido</th>
                <th scope="col" class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody> 
                <?php foreach($archivos as $i => $a):?>
                <?php 
                    switch($a['extension']){
                        case 'png':
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                            $icono = 'imagen';
                            break;
                        case 'pdf':
                            $icono = 'pdf';
                            break;
                        case 'docx':
                        case 'doc':
                            $icono = 'word';
                            break;
                        case 'xlsx':
                            $icono = 'excel';
                            break;
                        default:
                            $icono = 'archivo';
                            break;
                    }
                ?>
                <tr>
                    <td class="nombre-tabla"><img src="../assets/icons/<?= $icono;?>.svg" alt=""> &nbsp<?= $a['nombre']?></td>
                    <td class="text-center"><?= date("d/m/Y H:i:s", strtotime($a['fecha'])); ?></td>
                    <td class="text-center"><?= round($a['tamano'] / 1024,2) . ' KB';	?></td>
                    <td class="text-center"><?= ($a['compartir'] == 1) ? 'SI' : 'NO' ?></td>
                    <td class="text-center d-flex justify-content-center">
                        <a class="btn btn-danger me-1" 
                        href="eliminarArchivo.php?id_archivo=<?= $a['id_archivo']?>"  
                        onclick="return confirm('Esta seguro que desea eliminar el archivo?')">
                        <img src="../assets/icons/trash.svg" width="20" alt="Eliminar">
                        </a>
                        <button type="button" class="btn btn-primary me-1" data-bs-toggle="modal"
                                    data-bs-target="#pedido_<?= $a['id_archivo'] ?>">
                                    <img src="../assets/icons/edit.svg" width="20" alt="Descargar">
                                </button>
                        <a class="btn btn-success" href="../uploads/<?=$a['archivo'];?>" download="<?=$a['nombre'];?>"><img src="../assets/icons/download.svg" width="20" alt="Descargar"></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
            </div>
            </div>
        </div>
    </div>
</div>

<?php foreach($archivos as $a): ?>
<?php 
    switch($a['extension']){
        case 'png':
        case 'jpg':
        case 'jpeg':
        case 'gif':
            $imagen = "../uploads/".$a['archivo'];
            break;
        case 'pdf':
            $imagen = '../assets/icons/pdf.svg';
            break;
        case 'docx':
        case 'doc':
            $imagen = '../assets/icons/word.svg';
            break;
        case 'xlsx':
            $imagen = '../assets/icons/excel.svg';
            break;
        default:
            $imagen = '../assets/icons/archivo.svg';
            break;
    }
?>
<!-- Modal -->
<div class="modal fade" id="pedido_<?= $a['id_archivo']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-bg-primary">
                <h5 class="modal-title" id="exampleModalLabel">Editar archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="actualizarArchivo.php" method="post" enctype="multipart/form-data">
                <input type="hidden" value="<?= $a['id_archivo'] ?>" name="id_archivo">
                <p>
                   <img width="150" src="<?= $imagen?>" alt="">
                </p>
                <p>
                    <strong>Ultima actualizacion :</strong> <?= date("d/m/Y H:i:s", strtotime($a['fecha'])); ?>
                </p>
                <p>
                    <strong>Extension : </strong> .<?= $a['extension'];?>
                </p>
                <p>
                    <strong>Tamaño : </strong><?= round($a['tamano'] / 1024,2) . ' KB';	?>
                </p>               
                <div class="col-auto">
                    <label for="nombre" class="form-label"><strong>Nombre del archivo <span class="obligatorio">*</span></strong></label>
                    <input class="form-control form-control-lg" id="nombre" type="text" name="nombre" maxlength="40" required value="<?=$a['nombre']?>">    
                </div>
                <br>
                <div class="col-auto">
                    <label for="descripcion" class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= $a['descripcion']?></textarea>
                </div>
                <br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="compartir" name="compartir" <?= ($a['compartir'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="compartir" >
                        <strong>Compartir archivo</strong>
                    </label>
                </div>
                <br>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn" name="submit">Actualizar</button>
                </div>
            </form>
            </div>  
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php  include 'layout/footer.php';?>