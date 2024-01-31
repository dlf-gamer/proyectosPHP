<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="http://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script> 
    <script src="http://threejs.org/examples/js/libs/stats.min.js"></script>
</head>
<body class="login">
<div id="particles-js"></div>
<script>
    // Configuración de particles.js
    let particles = particlesJS("particles-js", { "particles": { "number": { "value": 150, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#ffffff" }, "shape": { "type": "edge", "stroke": { "width": 0, "color": "#000000" }, "polygon": { "nb_sides": 5 }, "image": { "src": "img/github.svg", "width": 100, "height": 100 } }, "opacity": { "value": 0.5, "random": false, "anim": { "enable": false, "speed": 1, "opacity_min": 0.1, "sync": false } }, "size": { "value": 3, "random": true, "anim": { "enable": false, "speed": 40, "size_min": 0.1, "sync": false } }, "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 }, "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 1200 } } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true }, "modes": { "grab": { "distance": 400, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 }, "repulse": { "distance": 200, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } } }, "retina_detect": true }); var count_particles, stats, update; stats = new Stats; stats.setMode(0); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function () { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);
</script>
<?php

//
require_once ("conexion.php");

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//use League\OAuth2\Client\Provider\Google;
//use League\OAuth2\Client\Provider\GenericProvider;

//Load Composer's autoloader
require 'email/vendor/autoload.php';

//
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
$email = new PHPMailer(true);

//
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

        // Fecha del registro
        $zona = new DateTimeZone("America/Bogota");
        $actual = new DateTime("now", $zona);
        $fecha = $actual->format("Y-m-d H:m:s");

        // 
        try {
            //
            $email->SMTPOptions = array(
                "tls" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                    "allow_self_signed" => true
                ),
            );

            // Configuracion del servidor SMTP del correo
            $email->SMTPDebug = 0;//SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $email->isSMTP();                                            //Send using SMTP
            $email->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $email->SMTPAuth   = true;                                   //Enable SMTP authentication
            $email->Username   = 'danieldelgado999@outlook.com';                     //SMTP username
            $email->Password   = 'Matrix2847';//zkap hlna gkpv xuf                               //SMTP password
            $email->SMTPSecure = 'tls';//PHPMailer::ENCRYPTION_STARTTLS;// PHPMailer::ENCRYPTION_SMTPS;             //Enable implicit TLS encryption
            $email->Port       = 587;// 587 TLS O 465 SSL                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            // Destinatario
            $email->setFrom('danieldelgado999@outlook.com', 'Verificar Email');
            $email->addAddress($correo, "HOLA");     //Add a recipient
            /* $email->addAddress('ellen@example.com');               //Name is optional
            $email->addReplyTo('info@example.com', 'Information');
            $email->addCC('cc@example.com');
            $email->addBCC('bcc@example.com'); */

            //Attachments
        /*  $email->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $email->addAttachment('/tmp/image.jpg', 'new.jpg'); */    //Optional name

            // Configuración de caracteres y codificación
            $email->CharSet = "UTF-8";
            $email->Encoding = "base64";

            // Configuración del contenido HTML
            $email->isHTML(true);                                  //Set email format to HTML
            $email->Subject = 'Verificación de Correo Electrónico';

            //
            $html = 
            '
            <div>
                <div>
                    <h2>BINVENIDO</h2>
                </div>
                <div>
                    <h3>HOLA, '.$nombre." ".$apellido.'</h3>
                    <p>Gracias por registrarse</p>
                    <p>Haz clic en el siguiente enlace para verificar tu correo electrónico: <b><a href="#">Aqui</a></b></p>
                    <p>¡Esperamos verte pronto en nuestro sitio web!</p>
                </div>
                <div>
                    <img src="" width="" height="" alt="">
                </div>
            </div>

            <div>
                <table>
                    <h2>REGISTRO</h2>
                    <tr>
                        <th>NOMBRE</th>
                        <th>APELLIDO</th>
                        <th>CORREO</th>
                        <th>FECHA</th>
                        <th>ACCIONES</th>
                    </tr>
                    <tr>
                        <td>'.$nombre.'</td>
                        <td>'.$apellido.'</td>
                        <td>'.$correo.'</td>
                        <td>'.$fecha.'</td>
                        <td><b><a href="#">Validar</a></b></p></td>
                    </tr>
                </table>
            </div>
            ';

            $email->Body    = $html;
            $email->AltBody = 'Este es el contexto plano para clientes de correo no HTML';


            // Verificar si se envio el correo
            $email->send();
                
        } catch (Exception $error) {
            //throw $th;
            echo "ERROR AL ENVIAR EL EMAIL => ". $error->getMessage();
            exit();
        }

        //
        try {
            // Setencia QSL para registrar datos
            $mysql = ("INSERT INTO usuario (id, dni, nombre, apellido, correo, celular, password, foto, fecha_insert) VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?)");
            $param = array($dni, $nombre, $apellido, $correo, $celular, $encriptar, $imagen, $fecha);// Capturar los parametros en modo de arreglos
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
            $mysql = ("SELECT id, nombre, password, foto FROM usuario WHERE correo = ?");
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
                    // Fecha del registro
                    $zona = new DateTimeZone("America/Bogota");
                    $actual = new DateTime("now", $zona);
                    $fecha = $actual->format("Y-m-d H:m:s");

                    try {
                        //code...
                        $mysql1 = ("UPDATE usuario SET fecha_login = ? WHERE id = ?");
                        $param1 = array($fecha, $datos["id"]);
                        $resul1 = $conexion->prepare($mysql1);
                        $resul1->execute($param1);
                        $valid1 = $resul1->rowCount();
            
                        if ($valid1 > 0) {
                            //
                            $_SESSION["id_usuario"] = $datos["id"];
                            $_SESSION["nombre_usuario"] = $datos["nombre"];
                            $_SESSION["foto_usuario"] = $datos["foto"];// Almacena la ruta o nombre del archivo de la foto
                            $_SESSION["tiempo"] = $_SESSION["tiempo"];// Crear variable

                            header("Location: index.php");
                            exit();
                        }
            
                    } catch (\Exception $error1) {
                        //throw $th;
                        echo "ERROR AL ACTUALIZAR => ".$error1->getMessage();
                    }
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
/* Estilo para el input file */
.custom-file-input {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 2;
        cursor: pointer;
    }

    /* Estilo para el contenedor del input file */
    .file-container {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 200px; /* Ajusta el ancho según tus necesidades */
        height: 40px; /* Ajusta la altura según tus necesidades */
        border: 1px solid #ccc; /* Estilo de borde opcional */
        border-radius: 5px; /* Estilo de borde redondeado opcional */
    }
    label{
        color: white;
    }
    input{
        background: transparent!important;
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
    /* Estilos generales */
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        color: #ffffff
    }
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .card {
        position: relative;
        min-width: 400px;
        transition: transform 0.5s, opacity 0.5s;
        background: rgba(255, 255, 255, 0); 
        border-radius: 10px;
        border:1px solid white;
        padding: 20px;
    }
    .card-register {
        transform: translateY(-100%);
        opacity: 0;
        position: absolute;
        z-index: -1;
    }
    /* Botón de cambio */
    .toggle-button {
        background-color: #8b5bff;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
    }
</style>

</head>
<body>

<div class="container">
    <div class="card card-login">
        <h2 class="text-center">Iniciar Sesión</h2>
        <form action="#" method="POST">
            <!-- Campos de inicio de sesión -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="correo" id="correo" placeholder="name@example.com">
                <label for="correo">Correo</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña">
                <label for="password">Contraseña</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit" name="session">Iniciar Sesión</button>
        </form>
        <hr>
        <p style="text-align:right">Necesitas una cuenta?</p>
        <button class="toggle-button" onclick="toggleForm('register')">Registrate</button>
    </div>

    <div class="card card-register">
        <h2 class="text-center">Registrarse</h2>
            <form action="#" method="POST" enctype="multipart/form-data" class="form-register">
            <div class="row">
                <!-- Columna 1 -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="number" class="form-control" name="dni" id="dni" placeholder="Cédula">
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre">
                    </div>
                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Apellido">
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo">
                    </div>
                </div>
                <!-- Columna 2 -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="text" class="form-control" name="celular" id="celular" placeholder="Celular">
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label">Clave</label>
                        <input type="password" class="form-control" name="clave" id="clave" placeholder="Clave">
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <div class="file-container">
                        <input type="file" class="form-control custom-file-input" name="foto" id="foto">
                        </div>
                        
                    </div>
                </div>
            </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit" name="register">Registrarse</button>
                <hr class="my-4">
            </form>
        <button class="toggle-button" onclick="toggleForm('login')">Volver a Iniciar Sesión</button>
    </div>
</div>

<script>
    function toggleForm (tipo) {
        const cardLogin = document.querySelector('.card-login');
        const cardRegister = document.querySelector('.card-register');
        const body = document.querySelector('body');

        if( tipo === "register" ) {
            cardLogin.style.transform = "translateY(-100%)";
            cardLogin.style.opacity = "0";
            cardRegister.style.transform = "translateY(0)";
            cardRegister.style.opacity = "1";
            cardRegister.style.zIndex = "1";
            cardLogin.style.zIndex = "-1";
        } else if( tipo === "login" ) {
            cardLogin.style.transform = "translateY(0)";
            cardLogin.style.opacity = "1";
            cardRegister.style.transform = "translateY(-100%)";
            cardRegister.style.opacity = "0";
            cardRegister.style.zIndex = "-1";
            cardLogin.style.zIndex = "1";
        }
        
    }
</script>

</div>

</body>
</html>