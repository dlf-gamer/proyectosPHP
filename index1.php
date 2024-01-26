<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>CRUD de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php

// incluir archivo de conexión
require "conexion.php";

// inicializar datos vacíos
$datos = array('id' => '', 'dni' => '', 'nombre' => '', 'apellido' => '', 'correo' => '', 'celular' => '');

// procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Registrar o actualizar datos
    if (isset($_GET["registrar"]) || isset($_GET["actualizar"])) {
        try {
            $dni = $_GET["dni"];
            $nombre = $_GET["nombre"];
            $apellido = $_GET["apellido"];
            $correo = $_GET["correo"];
            $celular = $_GET["celular"];
            $id = isset($_GET["id"]) ? $_GET["id"] : null;

            // Validar campos obligatorios
            if (empty($dni) || empty($nombre) || empty($apellido) || empty($correo) || empty($celular)) {
                echo "Todos los campos son obligatorios.";
                exit();
            }

            // Verificar si el correo ya existe
            $sql = "SELECT * FROM usuario WHERE correo = ? LIMIT 1";
            $resultado = $conexion->prepare($sql);
            $resultado->execute([$correo]);
            $validar = $resultado->rowCount();

            if ($validar > 0 && !isset($_GET["actualizar"])) {
                echo "El correo ya existe.";
                exit();
            } 
            elseif ($validar > 0 && isset($_GET["actualizar"])) {
                // Verificar si el correo ya existe pero no pertenece al usuario que estamos actualizando
                $sql = "SELECT * FROM usuario WHERE correo = ? AND id <> ? LIMIT 1";
                $resultado = $conexion->prepare($sql);
                $resultado->execute([$correo, $id]);
                $validar = $resultado->rowCount();
            
                if ($validar > 0) {
                    echo "El correo ya existe.";
                    exit();
                }
            }

            // Registrar o actualizar datos en la base
            if ($id) {
                $sql = "UPDATE usuario SET dni = ?, nombre = ?, apellido = ?, correo = ?, celular = ? WHERE id = ?";
                $parametros = [$dni, $nombre, $apellido, $correo, $celular, $id];
            } else {
                $sql = "INSERT INTO usuario (dni, nombre, apellido, correo, celular) VALUES (?, ?, ?, ?, ?)";
                $parametros = [$dni, $nombre, $apellido, $correo, $celular];
            }

            $resultado = $conexion->prepare($sql);
            $resultado->execute($parametros);

            // Redirigir después de la operación
            header("Location: index1.php");
            exit();

        } catch (\Exception $error) {
            echo "Error al procesar el formulario: " . $error->getMessage();
        }
    }

    /* if (isset($_GET["actualizar"])) {

        // Capturar datos del formulario
        $dni = $_GET["dni"];
        $nombre = $_GET["nombre"];
        $apellido = $_GET["apellido"];
        $correo = $_GET["correo"];
        $celular = $_GET["celular"];

        $id = isset($_GET["id"]) ? $_GET["id"] : null;

        try {
            //si no exiate el parametro id
            if ($id) {
                // Actualizar datos en la base
                $sql = "UPDATE usuario SET dni = ?, nombre = ?, apellido = ?, correo = ?, celular = ? WHERE id = ?";
                $actualizar = [$dni, $nombre, $apellido, $correo, $celular, $id];
                $resultado = $conexion->prepare($sql);
                $resultado->execute($actualizar);
            }
            // Redirigir después de la operación
            ?>
                <script>
                Swal.fire({
                    title: 'Acción exitosa',
                    text: 'Usuario actualizado correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    window.location.href = "index1.php";
                });
                </script>
                <?php
            exit();

        } catch (\Exception $error) {
            echo "Error al procesar el formulario: " . $error->getMessage();
        }
    } */

    //obtener el parametro eliminar
    if (isset($_GET["eliminar"])) {
        # code...
        $eliminar = $_GET["eliminar"];
        if ($eliminar > 0) {
            try {
                $sql = "DELETE FROM usuario WHERE id = ?";
                $resultado = $conexion->prepare($sql);
                $resultado->execute([$eliminar]);

                ?>
                <script>
                Swal.fire({
                    title: 'Acción exitosa',
                    text: 'Usuario eliminado correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    window.location.href = "index1.php";
                });
                </script>
                <?php

            } catch (\Exception $error) {
                echo "Error al eliminar: " . $error->getMessage();
            }
        } else {
            echo "ID no válido";
        }
    }

    // Obtener datos para prellenar el formulario si se está actualizando
    if (isset($_GET["buscar"])) {
        $actualizar = $_GET["buscar"];
        try {
            $sql = "SELECT * FROM usuario WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$actualizar]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\Exception $error) {
            echo "Error al obtener datos para prellenar el formulario: " . $error->getMessage();
        }
    }
}

// Consultar la base de datos
try {
    $sql = "SELECT * FROM usuario ORDER BY id ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $total = $stmt->rowCount();
} catch (\Exception $error) {
    echo "Error al consultar la base de datos: " . $error->getMessage();
}

?>

<div class="container">
<h1>Usuarios</h1>


<div class="mb-4" id="formulario">

<?php if (isset($_GET["buscar"])) { ?> 
    <form action="?actualizar" method="GET" class="row g-3">
<?php } else { ?> 
    <form action="" method="GET" class="row g-3">
<?php } ?>
    
        <input type="hidden" name="id" value="<?php echo $datos['id']; ?>">

        <div class="col-md-6">
            <label for="dni" class="form-label">DNI</label>
            <input type="number" class="form-control" name="dni" id="dni" value="<?php echo $datos['dni']; ?>">
        </div>

        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $datos['nombre']; ?>">
        </div>

        <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" id="apellido" value="<?php echo $datos['apellido']; ?>">
        </div>

        <div class="col-md-6">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" id="correo" value="<?php echo $datos['correo']; ?>">
        </div>

        <div class="col-md-6">
            <label for="celular" class="form-label">Celular</label>
            <input type="text" class="form-control" name="celular" id="celular" value="<?php echo $datos['celular']; ?>">
        </div>
        <div class="col-md-6">
            <label for="foto-perfil" class="form-label">Foto de perfil</label>
            <br>
            <img src="<?php echo $datos['foto']; ?>" alt="" width="100px">
            <br>
            <br>
            <input type="file" class="form-control" name="foto-perfil" id="foto-perfil">
        </div>

        <div class="col-12">
            <?php if (isset($_GET["buscar"])) { ?> 
                <button id="actualizarBtn" type="submit" class="btn btn-primary" name="actualizar">Actualizar</button>
            <?php } else { ?> 
                <button id="registrarBtn" type="submit" class="btn btn-success" name="registrar">Registrar</button>
            <?php } ?>
        </div>
    </form>
</div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>DNI</th>
            <th>NOMBRE</th>
            <th>APELLIDO</th>
            <th>CORREO</th>
            <th>FOTO DE PERFIL</th>
            <th>CELULAR</th>
            <th colspan="2">ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($total > 0) {
            while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td><?php echo $datos["dni"] ?></td>
                <td><?php echo $datos["nombre"] ?></td>
                <td><?php echo $datos["apellido"] ?></td>
                <td><?php echo $datos["correo"] ?></td>
                <td><?php echo $datos["celular"] ?></td>
                <td><img src="<?php echo $datos['foto']; ?>" alt="" width="40px"></td>
                <td>   
                    <a href="?buscar=<?php echo $datos['id']; ?>" class="btn btn-primary btn-sm" onclick="btn()">Actualizar</a>
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
</html>