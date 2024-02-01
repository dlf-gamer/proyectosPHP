<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="register">

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
}

?>

<div class="container p-5">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="text-center mb-4">Registrarse</h2>
                    <form action="register.php" method="POST" enctype="multipart/form-data" class="form-register">
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
                            <input type="file" class="form-control" name="foto" id="foto">
                        </div>
                        <button class="w-100 btn btn-lg btn-primary" type="submit" name="register">Registrarse</button>
                        <hr class="my-4">
                        <div class="d-flex justify-content-center">
                            <a href="login.php" class="btn btn-outline-secondary">Iniciar Sesión</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    
</body>

<script type="">

</script>

</html>