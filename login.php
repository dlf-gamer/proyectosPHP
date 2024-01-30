<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<?php

//
require_once ("conexion.php");

//
session_start();

//
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //
    if (isset($_POST["session"])) {
        //
        $correo = $_POST["correo"];
        $password = $_POST["password"];

        //
        if (empty($correo) or empty($password)) {
            # code...
            print_r("DATOS OGLIGATORIOS");
            exit();
        }

        //print $correo." ".$password;

        //
        try {
            //code...
            $mysql = "SELECT id, nombre, password, foto FROM usuario WHERE correo = ?";
            $param = array($correo);
            $resul = $conexion->prepare($mysql);
            $resul->execute($param);
            $valid = $resul->rowCount();
            $datos = $resul->fetch(PDO::FETCH_ASSOC);

            if ($valid > 0) {
                //echo "Contraseña ingresada: $password<br>";
                //echo "Contraseña en la base de datos: {$datos["password"]}<br>";
                // Verificar la contraseña encriptada
                $encriptado = password_verify($password, $datos["password"]);

                if ($encriptado) {
                    # code...
                    $_SESSION["id_usuario"] = $datos["id"];
                    $_SESSION["nombre_usuario"] = $datos["nombre"];
                    $_SESSION["foto_usuario"] = $datos["foto"];// Almacena la ruta o nombre del archivo de la foto
                    $_SESSION["tiempo"] = $_SESSION["tiempo"];// Crear variable

                    header("Location: index.php");
                    exit();
                    
                } else {
                    print_r("CLAVE INCORRECTA");
                }
            } else {
                print_r("CREDENCIALES INCORRECTAS");
            }

        } catch (\Exception $error) {
            //throw $th;
            echo "ERROR AL INICIAR SESSION => ".$error->getMessage();
        }
    }
} 

?>

<div>
    <div>
        <h2>Iniciar Sesion</h2>
    </div>
    <form action="login.php" method="POST">
        <div>
            <label for="correo">Correo</label>
            <input type="text" name="correo" id="correo" placeholder="Correo">
        </div>
        <div>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" placeholder="Contraseña">
        </div>
        <div>
            <button type="submit" name="session">Iniciar Sesion</button>
        </div>
        <div>
            <button type="button" name="register"><a href="register.php">Registrarse</a></button>
        </div>
    </form>
</div>
    
</body>
</html>