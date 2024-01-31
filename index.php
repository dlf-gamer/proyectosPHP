<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>CRUD de Usuarios</title>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="http://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script> 
        <script src="http://threejs.org/examples/js/libs/stats.min.js"></script>
</head>
<body class="index">

<?php

// incluir archivo de conexión
require "conexion.php";

session_start();

// Verifica si el usuario ha iniciado session
if (!isset($_SESSION["id_usuario"])) {
    # code...
    header("Location: login.php");
    exit();
}

// Capturar las variables dession
$usuario = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : null;
$imagen = isset($_SESSION["foto_usuario"]) ? $_SESSION["foto_usuario"] : null;

// Comprobamos si esta definida la session "tiempo"
if (isset($_SESSION["tiempo"]) ? $_SESSION["tiempo"] : null) {
    // Tiempo en segundos para dar vida a la session
    $segundos = 1800;// Segundos

    // Calculamos el tiempo de vida inactivo
    $limite = time() - $_SESSION["tiempo"];

    // Comparacion para redirigir pagina, si la vida de session sea mayor a el tiempo insertado en inactivo
    if ($limite > $segundos) {
        # code...
        echo 
        "
        <script>
            window.alert('ADIOS');
            window.location.reload();
            window.location.href = 'destroy.php';
        </script>
        ";
        //header("Location: destroy.php");
        exit();// Salimos del escript despues de la carga
    }
} else {
    $_SESSION["tiempo"] = time();// Activamos session tiempo
}

// inicializar datos vacíos para capturar en el formulario a la hora de actualizar
$datos = array('id' => '', 'dni' => '', 'nombre' => '', 'apellido' => '', 'correo' => '', 'celular' => '');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //
    if (isset($_POST["registrar"])) {
        // capturar datos del formulario
        $dni = $_POST["dni"];
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $correo = $_POST["correo"];
        $celular = $_POST["celular"];
        $clave = $_POST["password"];// Contraseña
        $foto = $_FILES['foto'];// Imagen

        /* // Generar una contraseña aleatoria
        function GeneradorPassword($longitud = 8) {
            $caracteresPermitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_";
            $password = '';
            $longitudCaracteresPermitidos = strlen($caracteresPermitidos) - 1;
    
            for ($i = 0; $i < $longitud; $i++) {
                $indiceAleatorio = mt_rand(0, $longitudCaracteresPermitidos);
                $password .= $caracteresPermitidos[$indiceAleatorio];
            }
            return $password;
        }

        // Generar y almacenar la contraseña durante el registro
        $clave = GeneradorPassword(); */

        // Hash de la contraseña para elmacenarla de manera asegura
        $encriptar = password_hash($clave, PASSWORD_DEFAULT);
        //$encriptar = $clave;

        // Validar campos obligatorios
        if (empty($dni) or empty($nombre) or empty($apellido) or empty($correo) or empty($celular) or empty($clave)) {
            //echo "Todos los campos son obligatorios.";
            ?>
            <script>alert("TODOS LOS DATOS SON OBLIGATORIOS");window.location.href = ("index.php");</script>
            <?php
            exit();
        }

        // Verificar si el correo y celular ya existe
        $MySqlSelectInsert = "SELECT * FROM usuario WHERE dni = ? OR correo = ? OR celular = ? LIMIT 1";
        $ResulSelectInsert = $conexion->prepare($MySqlSelectInsert);
        $ResulSelectInsert->execute(array($dni, $correo, $celular));
        $ValidSelectInsert = $ResulSelectInsert->rowCount();
        $DatasSelectInsert = $ResulSelectInsert->fetch(PDO::FETCH_ASSOC);

        // ValidarSelectInsert parametros repetidos
        if ($ValidSelectInsert > 0 and !isset($_POST["actualizar"])) {
            //echo "El correo ya existe.";
            $MensajeInsert = "";

            // Comprobar duplicados
            if ($DatasSelectInsert["dni"] == $dni) {
                # code...
                $MensajeInsert .= "EL DNI YA EXISTE\n";
            }
            if ($DatasSelectInsert["correo"] == $correo) {
                # code...
                $MensajeInsert .= "EL CORREO YA EXISTE\n";
            }
            if ($DatasSelectInsert["celular"] == $celular) {
                # code...
                $MensajeInsert .= "EL CELULAR YA EXISTE\n";
            }

            // Imprimir MensajeInsert si hay duplicados
            if (!empty($MensajeInsert)) {
                # code...
                echo $MensajeInsert;
                exit();
            }
        }

        function SubirFoto() {
            // Directorio de destino para las imágenes
            $carpeta_destino = "upload/";
        
            // Lista de extensiones permitidas
            $extensiones_permitidas = ["jpeg", "jpg", "png", "gif"];
        
            // Validar si se envió un archivo
            if (!empty($_FILES["foto"]["name"])) {
                // Obtener la extensión del archivo
                $extension_archivo = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        
                // Verificar si la extensión está en la lista de extensiones permitidas
                if (!in_array($extension_archivo, $extensiones_permitidas)) {
                    die("Error: SOLO SE PERMITEN ARCHIVOS DE " . implode(", ", $extensiones_permitidas));
                }
        
                // Validar el límite de tamaño del archivo (5 MB en este caso)
                $limiteTamanio = 5 * 1024 * 1024; // 5 MB en bytes
                if ($_FILES["foto"]["size"] > $limiteTamanio) {
                    die("Error: EL TAMAÑO DEL ARCHIVO DEBE SER MENOR O IGUAL A " . $limiteTamanio . " BYTES.");
                }

                // Generar un nombre único para la imagen
                $nombreUnico = uniqid("", true);
                $fechaActual = date("YmdHis");

                $ruta_destino = $carpeta_destino . $nombreUnico . "_" . $fechaActual . "_" . $extension_archivo;
        
                // Verificar si la carpeta de destino existe, si no, créala
                if (!file_exists($carpeta_destino)) {
                    mkdir($carpeta_destino, 0777, true);
                }
        
                // Mover la foto a la carpeta de destino
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino);
        
                // Devolver la ruta final de la foto
                return $ruta_destino;
            } else {
                // Si no se proporciona una foto, devolver la ruta de la foto por defecto
                return 'image/foto_por_defecto.png';
            }
        }
        $ruta_final = SubirFoto(); 

        try {
            //
            $MySqlInsert = "INSERT INTO usuario (dni, nombre, apellido, correo, celular, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $ParamInsert = array($dni, $nombre, $apellido, $correo, $celular, $encriptar, $ruta_final); 
            $ResuIinsert = $conexion->prepare($MySqlInsert);
            $ResuIinsert->execute($ParamInsert);

            // Redirigir después de la operación
            ?>
            <script>
            Swal.fire({
                title: 'Acción exitosa',
                text: 'Usuario registrado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(function() {
                window.location.href = "index.php";
            });
            </script>
            <?php
            exit();
        
        } catch (\Exception $error) {
            echo "Error al procesar el formulario: " . $error->getMessage();
        }
    }

    if (isset($_POST["actualizar"])) {
        // Capturar datos del formulario
        $dni = $_POST["dni"] ? $_POST["dni"] : null;
        $nombre = $_POST["nombre"] ? $_POST["nombre"] : null;
        $apellido = $_POST["apellido"] ? $_POST["apellido"] : null;
        $correo = $_POST["correo"] ? $_POST["correo"] : null;
        $celular = $_POST["celular"] ? $_POST["celular"] : null;
        $clave = $_POST["password"];// Contraseña
        $foto = $_FILES['foto'];// Imagen

        // id
        $id = isset($_POST["id"]) ? $_POST["id"] : null;

        /* // Generar una contraseña aleatoria
        function GeneradorPassword($longitud = 8) {
            $caracteresPermitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_";
            $password = '';
            $longitudCaracteresPermitidos = strlen($caracteresPermitidos) - 1;
    
            for ($i = 0; $i < $longitud; $i++) {
                $indiceAleatorio = mt_rand(0, $longitudCaracteresPermitidos);
                $password .= $caracteresPermitidos[$indiceAleatorio];
            }
            return $password;
        }

        // Generar una contraseña aleatoria solo si no se proporcionó una nueva
        $clave = GeneradorPassword(); */

        // Hash de la contraseña para elmacenarla de manera asegura
        $encriptar = password_hash($clave, PASSWORD_DEFAULT);

        // Validar campos obligatorios
        if (empty($id) or empty($dni) or empty($nombre) or empty($apellido) or empty($correo) or empty($celular)) {
            //echo "Todos los campos son obligatorios.";
            ?>
            <script>alert("TODOS LOS DATOS SON OBLIGATORIOS");window.location.href = ("index.php");</script>
            <?php
            exit();
        }

        //
        try {
            // Verificar si el correo ya existe pero no pertenece al usuario que estamos actualizando
            $MySqlSelectUpdate = "SELECT * FROM usuario WHERE dni = ? OR correo = ? OR celular = ? AND id <> ? LIMIT 1";
            $ResulSelectUpdate = $conexion->prepare($MySqlSelectUpdate);
            $ResulSelectUpdate->execute(array($dni, $correo, $celular, $id));
            $ValidSelectUpdate = $ResulSelectUpdate->rowCount();
            $DatasSelectUpdate = $ResulSelectUpdate->fetch(PDO::FETCH_ASSOC);

            //
            if ($ValidSelectUpdate > 0 && !isset($_POST["actualizar"])) {

                //
                $MensajeUpdate = "";

                //
                if ($DatasSelectUpdate["dni"] == $dni) {
                    # code...
                    $MensajeUpdate .= "EL DNI QUE VAS ACTUALIZAR YA EXISTE\n";
                }
                if ($DatasSelectUpdate["correo"] == $correo) {
                    # code...
                    $MensajeUpdate .= "EL CORREO QUE VAS ACTUALIZAR YA EXISTE\n";
                }
                if ($DatasSelectUpdate["celular"] == $celular) {
                    # code...
                    $MensajeUpdate .= "EL CELULAR QUE VAS ACTUALIZAR YA EXISTE\n";
                }

                //
                if (!empty($MensajeUpdate)) {
                    # code...
                    echo $MensajeUpdate;
                    exit();
                }
            }
        } catch (Exception $error) {
            echo "ERROR AL BUSCAR LOS DATOS PARA VALIDAR => ".$error->getMessage();
        }

        //
        function SubirFoto() {
            // Directorio de destino para las imágenes
            $carpeta_destino = "upload/";
        
            // Lista de extensiones permitidas
            $extensiones_permitidas = ["jpeg", "jpg", "png", "gif"];
        
            // Validar si se envió un archivo
            if (!empty($_FILES["foto"]["name"])) {
                // Obtener la extensión del archivo
                $extension_archivo = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        
                // Verificar si la extensión está en la lista de extensiones permitidas
                if (!in_array($extension_archivo, $extensiones_permitidas)) {
                    die("Error: SOLO SE PERMITEN ARCHIVOS DE " . implode(", ", $extensiones_permitidas));
                }
        
                // Validar el límite de tamaño del archivo (5 MB en este caso)
                $limiteTamanio = 5 * 1024 * 1024; // 5 MB en bytes
                if ($_FILES["foto"]["size"] > $limiteTamanio) {
                    die("Error: EL TAMAÑO DEL ARCHIVO DEBE SER MENOR O IGUAL A " . $limiteTamanio . " BYTES.");
                }

                // Generar un nombre único para la imagen
                $nombreUnico = uniqid("", true);
                $fechaActual = date("YmdHis");

                $ruta_destino = $carpeta_destino . $nombreUnico . "_" . $fechaActual . "_" . $extension_archivo;
        
                // Verificar si la carpeta de destino existe, si no, créala
                if (!file_exists($carpeta_destino)) {
                    mkdir($carpeta_destino, 0777, true);
                }
        
                // Mover la foto a la carpeta de destino
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino);
        
                // Devolver la ruta final de la foto
                return $ruta_destino;
            } else {
                // Si no se proporciona una foto, devolver la ruta de la foto por defecto
                return 'image/foto_por_defecto.png';
            }
        }
        // Subir nueva foto si se proporciona
        $ruta_final = empty($ruta_final) ? SubirFoto() : null;  

        //si exiate el parametro id
        if ($id) {
            try {
                // Obtener la ruta de la foto antigua antes de la actualizacion
                $MySqlSelectImage = "SELECT foto FROM usuario WHERE id = ?";
                $ParamSelectImage = array($id);
                $ResulSelectImage = $conexion->prepare($MySqlSelectImage);
                $ResulSelectImage->execute($ParamSelectImage);
                $ValidSelectImage = $ResulSelectImage->rowCount();
    
                if ($ValidSelectImage > 0) {
                    # code...
                    while ($DatasSelectImage = $ResulSelectImage->fetch(PDO::FETCH_ASSOC)) {
                        // Verificar si se proporciono una foto nueva
                        if (!empty($_FILES["foto"]["name"])) {
                            # code...
                            $FotoPreterminado = "image/foto_por_defecto.png";
                            if ($DatasSelectImage["foto"] and $DatasSelectImage["foto"] !== $FotoPreterminado) {
                                # code...
                                if (file_exists($DatasSelectImage["foto"]) and is_file($DatasSelectImage["foto"])) {
                                    # code...
                                    unlink($DatasSelectImage["foto"]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $error) {
                //throw $th;
                echo "ERROR AL CONSULTAR LA FOTO PARA ACTUALIZAR => ".$error->getMessage();
            }

            //
            try {        
                // Actualizar datos en la base
                $MySqlUpdate = "UPDATE usuario SET dni = ?, nombre = ?, apellido = ?, correo = ?, celular = ?, password = ?, foto = ? WHERE id = ?";
                $ParamUpdate = array($dni, $nombre, $apellido, $correo, $celular, $encriptar, $ruta_final, $id);
                $ResulUpdate = $conexion->prepare($MySqlUpdate);
                $ResulUpdate->execute($ParamUpdate);
                $ValidUpdate = $ResulUpdate->rowCount();

                if ($ValidUpdate > 0) {
                    // Redirigir después de la operación
                    ?>
                    <script>
                    Swal.fire({
                        title: 'Acción exitosa',
                        text: 'Usuario actualizado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = "index.php";
                    });
                    </script>
                    <?php
                    exit();
                }

            } catch (\Exception $error) {
                echo "ERROR AL ACTUALIZAR LOS DATOS " . $error->getMessage();
            }
        }
    }
}

//
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    //obtener el parametro eliminarImagen para ejecutar el código que solo elimina la imagen
    if (isset($_GET["eliminarImagen"])) {

        $eliminar = $_GET["eliminarImagen"];

        if ($eliminar) {
            try {

                $MySqlSearchDelete = "SELECT foto FROM usuario WHERE id = ?";
                $ResulSearchDelete = $conexion->prepare($MySqlSearchDelete);
                $ParamSearchDelete = array($eliminar);
                $ResulSearchDelete->execute($ParamSearchDelete);
                $ValidSearchDelete = $ResulSearchDelete->rowCount();

                if ($ValidSearchDelete > 0) {
    
                    while ($DatossFotosDelete = $ResulSearchDelete->fetch(PDO::FETCH_ASSOC)) {
        
                        $FotoPreterminado = "image/foto_por_defecto.png";
                        if (!empty($DatossFotosDelete["foto"]) and $DatossFotosDelete["foto"] !== $FotoPreterminado) {
            
                            if (file_exists($DatossFotosDelete["foto"])) {
                
                                unlink($DatossFotosDelete["foto"]);
                            }
                        }
                    }
                }
            } catch (\Exception $error) {

                echo "ERROR AL CONSULTAR LA FOTO => ".$error->getMessage();
            }

            try {
                $MySqlUpdate = "UPDATE usuario SET foto = ? WHERE id = ?";
                $ParamUpdate = array($FotoPreterminado, $eliminar);
                $ResulUpdate = $conexion->prepare($MySqlUpdate);
                $ResulUpdate->execute($ParamUpdate);
                $ValidUpdate = $ResulUpdate->rowCount();

                if ($ValidUpdate > 0) {

                    ?>
                    <script>
                        Swal.fire({
                            title: 'Acción exitosa',
                            text: 'Foto eliminada correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = "index.php";
                        });
                    </script>
                    <?php
                }
            } catch (\Exception $error) {
                echo "Error al eliminar: " . $error->getMessage();
            }
        } else {
            echo "ID eliminar no válido";
        }
    }
    
    //obtener el parametro eliminar
    if (isset($_GET["eliminar"])) {
        # code...
        $eliminar = $_GET["eliminar"];
        //
        if ($eliminar > 0) {
            //
            try {
                //code...
                $MySqlSearchDelete = "SELECT foto FROM usuario WHERE id = ?";
                $ResulSearchDelete = $conexion->prepare($MySqlSearchDelete);
                $ParamSearchDelete = array($eliminar);
                $ResulSearchDelete->execute($ParamSearchDelete);
                $ValidSearchDelete = $ResulSearchDelete->rowCount();

                if ($ValidSearchDelete > 0) {
                    # code...
                    while ($DatossFotosDelete = $ResulSearchDelete->fetch(PDO::FETCH_ASSOC)) {
                        # code...
                        $FotoPreterminado = "image/foto_por_defecto.png";
                        if (!empty($DatossFotosDelete["foto"]) and $DatossFotosDelete["foto"] !== $FotoPreterminado) {
                            # code...
                            if (file_exists($DatossFotosDelete["foto"])) {
                                # code...
                                unlink($DatossFotosDelete["foto"]);
                            }
                        }
                    }
                }
            } catch (\Exception $error) {
                //throw $th;
                echo "ERROR AL CONSULTAR LA FOTO => ".$error->getMessage();
            }

            try {
                $MySqlDelete = "DELETE FROM usuario WHERE id = ?";
                $ParamDelete = array($eliminar);
                $ResulDelete = $conexion->prepare($MySqlDelete);
                $ResulDelete->execute($ParamDelete);
                $ValidDelete = $ResulDelete->rowCount();

                if ($ValidDelete > 0) {
                    # code...
                    ?>
                    <script>
                        Swal.fire({
                            title: 'Acción exitosa',
                            text: 'Usuario eliminado correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            window.location.href = "index.php";
                        });
                    </script>
                    <?php
                }
            } catch (\Exception $error) {
                echo "Error al eliminar: " . $error->getMessage();
            }
        } else {
            echo "ID eliminar no válido";
        }
    }

    // Obtener datos para prellenar el formulario si se está actualizando
    if (isset($_GET["buscar"])) {
        //
        $actualizar = $_GET["buscar"];
        //
        if ($actualizar > 0) {
            # code...
            try {
                $MySqlSearch = "SELECT * FROM usuario WHERE id = ?";
                $ParamSearch = array($actualizar);
                $ResulSearch = $conexion->prepare($MySqlSearch);
                $ResulSearch->execute($ParamSearch);
                $ValidSearch = $ResulSearch->rowCount();

                if ($ValidSearch > 0) {
                    # code...
                    $datos = $ResulSearch->fetch(PDO::FETCH_ASSOC);
                }
            } catch (\Exception $error) {
                echo "Error al obtener datos para prellenar el formulario: " . $error->getMessage();
            }
        } else {
            echo "ID buscar no válido";
        }
    }
}

// Consultar la base de datos
try {
    $MySqlSelect = "SELECT * FROM usuario ORDER BY id ASC";
    $ResulSelect = $conexion->prepare($MySqlSelect);
    $ResulSelect->execute(array());
    $ValidSelect = $ResulSelect->rowCount();

} catch (\Exception $error) {
    echo "Error al consultar la base de datos: " . $error->getMessage();
}

?>
<div id="particles-js"></div>
<script>
    let particles = particlesJS("particles-js", {
    particles: {
        number: { value: 150, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: {
        type: "edge",
        stroke: { width: 0, color: "#000000" },
        polygon: { nb_sides: 5 },
        image: { src: "img/github.svg", width: 100, height: 100 },
        },
        opacity: {
        value: 0.5,
        random: false,
        anim: { enable: false, speed: 1, opacity_min: 0.1, sync: false },
        },
        size: {
        value: 3,
        random: true,
        anim: { enable: false, speed: 40, size_min: 0.1, sync: false },
        },
        line_linked: {
        enable: true,
        distance: 150,
        color: "#ffffff",
        opacity: 0.4,
        width: 1,
        },
        move: {
        enable: true,
        speed: 6,
        direction: "none",
        random: false,
        straight: false,
        out_mode: "out",
        bounce: false,
        attract: { enable: false, rotateX: 600, rotateY: 1200 },
        },
    },
    interactivity: {
        detect_on: "canvas",
        events: {
        onhover: { enable: true, mode: "repulse" },
        onclick: { enable: true, mode: "push" },
        resize: true,
        },
        modes: {
        grab: { distance: 400, line_linked: { opacity: 1 } },
        bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 },
        repulse: { distance: 200, duration: 0.4 },
        push: { particles_nb: 4 },
        remove: { particles_nb: 2 },
        },
    },
    retina_detect: true,
    });
    var count_particles, stats, update;
    stats = new Stats();
    stats.setMode(0);
    stats.domElement.style.position = "absolute";
    stats.domElement.style.left = "0px";
    stats.domElement.style.top = "0px";
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector(".js-count-particles");
    update = function () {
    stats.begin();
    stats.end();
    if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
        count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
    }
    requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>

<div class="container pt-4 px-4 py-4">
<style>
    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #f9f9f9;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #000000;
        background-image: url("");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 50% 50%;
    }
    .container {
        position: relative;
        z-index: 2;
        background-color: #ffffff;
    }

</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var profileImage = document.getElementById("profile");
        var dropdownMenu = document.getElementById("dropdown-menu");

        profileImage.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
        });


        document.addEventListener("click", function (event) {
            var targetElement = event.target;
            if (!dropdownMenu.contains(targetElement) && targetElement !== profileImage) {
                dropdownMenu.style.display = "none";
            }
        });
    });
</script>
<div style="display: flex; justify-content: space-between">
    <h1 style="text-align: right">CRUD PHP PDO ANTI SQL INYECTION</h1>
    <?php
    if (isset($usuario) and $usuario != "") {
        ?>
        <div id="profile">
            
            <div style="position: relative;">
                <img id="profile-image" src="<?php echo isset($imagen) ? $imagen : null; ?>" alt="usuario" width="40px">
                <div id="dropdown-menu" class="dropdown-menu">
                    <a href="destroy.php" style="text-align:right"><p>Cerrar Sesión</p></a>
                </div>
            </div>
            <div>
                <p style="text-align: right"> <?php echo isset($usuario) ? $usuario : null; ?> </p>
            </div>
        </div>
        
        <?php
    }
    ?>
</div>



<div class="mb-4" id="formulario">

<?php if (isset($_GET["buscar"])) { ?> 
    <form action="?actualizar" method="POST" enctype="multipart/form-data" class="row g-3">
<?php } else { ?> 
    <form action="index.php" method="POST" enctype="multipart/form-data" class="row g-3">
<?php } ?>
    
        <input type="hidden" name="id" value="<?php echo $datos['id'] ? $datos["id"] : null; ?>">

        <div class="col-md-6">
            <label for="dni" class="form-label">DNI</label>
            <input type="number" class="form-control" name="dni" id="dni" value="<?php echo $datos['dni'] ? $datos["dni"] : null; ?>">
        </div>

        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $datos['nombre'] ? $datos["nombre"] : null; ?>">
        </div>

        <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" id="apellido" value="<?php echo $datos['apellido'] ? $datos["apellido"] : null; ?>">
        </div>

        <div class="col-md-6">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" id="correo" value="<?php echo $datos['correo'] ? $datos["correo"] : null; ?>">
        </div>

        <div class="col-md-6">
            <label for="celular" class="form-label">Celular</label>
            <input type="text" class="form-control" name="celular" id="celular" value="<?php echo $datos['celular'] ? $datos["celular"] : null; ?>">
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="password" value="">
        </div>

        <div class="col-md-6">
            <label for="foto" class="form-label">Foto</label>
            <br>
            <?php if (isset($_GET["buscar"])) { ?> 
                <?php
                if (isset($datos["foto"]) and $datos["foto"] != "") {
                    # code...
                    ?>
                    <div class="contenedor-imagen">
                        <img src="<?php echo $datos["foto"] ? $datos["foto"] : null; ?>" alt="foto perfil" width="150px" id="imagenInput">
                        <?php
                        if (!empty($datos["foto"]) and $datos["foto"] != "image/foto_por_defecto.png") {
                            # code...
                            ?>
                            <div class="overlay">
                                <a onclick="window.location.href='?eliminarImagen=<?php echo $datos['id']; ?>';">
                                    <button class="btn btn-danger">Eliminar</button>
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php    
                }
                ?>
            <?php } else { ?>
            <!-- Contenedor para la previsualización de la foto -->
            <div class="contenedor-imagen">
                <!-- <img src="image/sube_tu_foto_aqui.jpg" id="imagePreview" width="150px" alt="" > -->
                <div id="previsualizacion"></div>
                <div class="overlay">
                    <button type="button" id="quitarFotoBtn" class="btn btn-danger" onclick="quitarFoto()" style="display: none;">Quitar</button>
                </div>
            </div>
            <?php } ?>
            <input class="form-control" type="file" name="foto" id="foto" accept="image/*" onchange="previsualizarFoto(this);">
            <!-- <input type="file" class="form-control" name="foto" id="foto" accept="image/*" value=""> -->
        </div>

        <div class="col-12">
            <?php if (isset($_GET["buscar"])) { ?> 
                <button type="submit" class="btn btn-primary" name="actualizar">Actualizar</button>
            <?php } else { ?> 
                <button type="submit" class="btn btn-success" name="registrar">Registrar</button>
            <?php } ?>
        </div>
    </form>
</div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>IDENTIFICACION</th>
            <th>NOMBRES</th>
            <th>APELLIDOS</th>
            <th>CORREO ELECTRONICO</th>
            <th>NUMERO DE CELULAR</th>
            <th>FOTO</th>
            <th colspan="2">ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($ValidSelect > 0) {
            while ($datos = $ResulSelect->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td><?php echo $datos["dni"] ?></td>
                <td><?php echo $datos["nombre"] ?></td>
                <td><?php echo $datos["apellido"] ?></td>
                <td><?php echo $datos["correo"] ?></td>
                <td><?php echo $datos["celular"] ?></td>
                <td>
                    <?php
                    // Verificar si ls imagen existe en la base de datos
                    if (isset($datos["foto"]) and $datos["foto"] != "") {
                        # code...
                        ?>
                        <img src="<?php echo $datos["foto"]; ?>" alt="foto perfil" width="40px">
                        <?php
                    }
                    else {
                        // Mostrar la imagen por defecto
                        ?>
                        <img src="image/foto_por_defecto.png" alt="foto normal" width="40px">
                        <?php
                    }
                    ?>
                </td>
                <td>   
                    <a href="?buscar=<?php echo $datos['id']; ?>" class="btn btn-primary btn-sm"">Actualizar</a>
                </td>
                <td>
                    <a href="?eliminar=<?php echo $datos['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
            <?php
            }
        }
        ?>
    </tbody>
</table>
</div>

</body>

<script>
    function previsualizarFoto(input) {
        var previsualizacion = document.getElementById('previsualizacion');
        var quitarFotoBtn = document.getElementById('quitarFotoBtn');

        if (input.files && input.files[0]) {
            var lector = new FileReader();

            lector.onload = function (e) {
                previsualizacion.innerHTML = '<img src="' + e.target.result + '" alt="Vista previa de la foto" style="max-width: 150px; max-height: 150px;" />';
                quitarFotoBtn.style.display = 'block'; // Mostrar el botón de quitar foto
            };

            lector.readAsDataURL(input.files[0]);
        } else {
            previsualizacion.innerHTML = '';
            quitarFotoBtn.style.display = 'none'; // Ocultar el botón de quitar foto
        }
    }

    function quitarFoto() {
        // Limpiar el campo de archivo y la previsualización
        document.getElementById('foto').value = '';
        document.getElementById('previsualizacion').innerHTML = '';
        document.getElementById('quitarFotoBtn').style.display = 'none'; // Ocultar el botón de quitar foto
    }

    document.getElementById('foto').addEventListener('change', function () {
        var archivo = this.files[0];
        if (archivo) {
            var lector = new FileReader();
            lector.onload = function (e) {
                document.getElementById('previsualizacion').src = e.target.result;
            };
            lector.readAsDataURL(archivo);
        }
    });
</script>

</html>