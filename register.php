<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

<?php

//
require_once ("conexion.php");

//
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //
    if (isset($_POST["register"])) {
        //
        //echo "<script>alert('si llega')</script>";

        // Capturar datos enviados del formulario
        $dni = $_POST["dni"];
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $correo = $_POST["correo"];
        $celular = $_POST["celular"];
        $clave = $_POST["clave"];

        // Encriptar password capturado
        $encriptar = password_hash($clave, PASSWORD_DEFAULT);

        // Imprimir datos capturados en modo de arreglos
        //print_r(array("dni: " => $dni, "nombre: " => $nombre, "apellido: " => $apellido, "correo: " => $correo, "celular: " => $celular, "clave: " => $clave));

        // Validar datos capturados
        if (empty($dni) or empty($nombre) or empty($apellido) or empty($correo) or empty($celular) or empty($clave)) {
            # code...
            echo "DATOS OBLIGATORIOS\n";
            exit();
        }

        //
        function imagen() {
            // Image
            $foto = $_FILES["foto"] ? $_FILES["foto"] : null;// Si no es obligatorio

            $name = $foto["name"];
            $type = $foto["type"];
            $tmpn = $foto["tmp_name"];
            $erro = $foto["error"];
            $size = $foto["size"];

            //
            if (!empty($name)) { 
                /* print_r("FOTO: => ". $_FILES["foto"]["name"]);
                exit(); */

                // Definir el tipo de archivo permitidos (en este caso imagenes)
                $extension = array("image/jpeg", "image/jpg", "image/png", "image/gif");

                //
                $validar = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                //
                $limite = 5 * 1024 * 1024;

                // Se verifica si el tipo de archivo es permitido
                if (!in_array($type, $extension)) {
                    //
                    die("ERROR: Solo se permiten archivos de => \n". implode(", ", $extension));
                    exit();

                }

                //
                if (!is_uploaded_file($tmpn)) {
                    //
                    die("ERROR: El archivo temporal no es valido => \n". $tmpn);
                    exit();
                }

                // Validar el codigo de error
                if ($erro == UPLOAD_ERR_OK) {
                    //
                    echo "La carga del archivo fue exitosa \n";
                }

                //
                if ($limite <= $size) {
                    //
                    die("ERROR: El limite del archivo es de => \n".$limite." bytes");
                    exit();
                }

                //
                $directorio = "upload/";
                $nombre_unico = uniqid("", true);
                $fecha_actual = date("YmdHis");

                //
                if (!is_dir($directorio) and !file_exists($directorio)) {
                    //
                    mkdir($directorio, 0777, true);
                }

                // Crear el nuevo nombre del archivo conservando la extension
                $archivo = $directorio . $nombre_unico. "_" .$fecha_actual. "." .$validar;
                //$destino = $directorio . $archivo;// Ruta completa del nuevo destino
                
                //
                /* $arreglo = array(
                    array(
                        "name" => $name,
                        "type" => $type,
                        "tmp_name" => $tmpn,
                        "error" => $erro,
                        "size" => $size
                    ),
                );
                echo "<pre>";
                print_r($archivo);
                print_r($arreglo);
                echo "/<pre>";
                exit(); */

                if ($archivo) {
                    // Mover la foto a la carpeta destino
                    move_uploaded_file($tmpn, $archivo);
                    return $archivo;// Devolver la ruta final de la foto
                }
                //
            } else {
                return "image/foto_por_defecto.png";
            }
        }
        $imagen = imagen();//

        //
        try {
            // Setencia QSL para registrar datos
            $mysql = ("INSERT INTO usuario (id, dni, nombre, apellido, correo, celular, password, foto) VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
            $param = array($dni, $nombre, $apellido, $correo, $celular, $encriptar, $imagen);// Capturar los parametros en modo de arreglos
            $resul = $conexion->prepare($mysql);// Prepara la sentencia
            $resul->execute($param);// Ejecutar la sentencia
            $valid = $resul->rowCount();// Obtener el numero de filas afectadas

            // Verificar el numero de filas afectadas
            if ($valid > 0) {
                //
                /* echo "EL REGISTRO SE REALIZO CON EXITO \n".$valid;
                $arreglo = array($param);
                echo "<pre>";
                print_r($arreglo);
                echo "/<pre>"; */
                header("Location: login.php");
                exit();

            } else {
                echo "NO SE PUDO REALIZAR EL REGISTRO \n";
            }

        } catch (\Exception $error) {
            //throw $th;
            echo "ERROR AL REGISTRAR DATOS => \n". $error->getMessage();
        }
    }
}

?>

<div>
    <div>
        <h2>Registrarse</h2>
    </div>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="dni">DNI</label>
            <input type="number" name="dni" id="dni" placeholder="Cedula">
        </div>
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre">
        </div>
        <div>
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" placeholder="Apellido">
        </div>
        <div>
            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" placeholder="Correo">
        </div>
        <div>
            <label for="celular">Celular</label>
            <input type="text" name="celular" id="celular" placeholder="Celular">
        </div>
        <div>
            <label for="clave">Clave</label>
            <input type="password" name="clave" id="clave" placeholder="Clave">
        </div>
        <div>
            <label for="foto">Foto</label>
            <input type="file" name="foto" id="foto">
        </div>
        <div>
            <input type="submit" name="register" value="Registrare">
        </div>
        <div>
            <button type="button" name="login"><a href="login.php">Iniciar Sesion</a></button>
        </div>
    </form>
</div>
    
</body>

<script type="">

</script>

</html>