<!-- MENU -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuToggle"
            aria-controls="menuToggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#"><img src="../assets/img/logo.png" width="80" alt="logo"></a>
        <div class="collapse navbar-collapse" id="menuToggle">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SESSION['menu'] == 'nuevo_archivo' ? 'active' : '') ?>" aria-current="page" href="nuevoArchivo.php">Nuevo Archivo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SESSION['menu'] == 'mis_archivos' ? 'active' : '') ?>" href="misArchivos.php">Mis Archivos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($_SESSION['menu'] == 'archivos_compartidos' ? 'active' : '') ?>" href="archivosCompartidos.php">Archivos Compartidos</a>
                </li>
            </ul>
            <a class="nav-link" href="logout.php">
                Cerrar Sesion
                <img width="20" src="../assets/icons/logout.svg" alt="cerrar sesion">
            </a>
        </div>
    </div>
</nav>