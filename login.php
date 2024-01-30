<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<style>
        body {
            background-color: #f5f5f5;
        }
        .form-signin {
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Iniciar Sesión</h2>
                        <form action="login.php" method="POST" class="form-signin">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="correo" id="correo" placeholder="name@example.com">
                                <label for="correo">Correo</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña">
                                <label for="password">Contraseña</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary" type="submit" name="session">Iniciar Sesión</button>
                            <hr class="my-4">
                            <div class="d-flex justify-content-center">
                                <a href="register.php" class="btn btn-outline-secondary">Registrarse</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>