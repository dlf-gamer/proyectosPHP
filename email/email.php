<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//use League\OAuth2\Client\Provider\Google;
//use League\OAuth2\Client\Provider\GenericProvider;

//Load Composer's autoloader
require 'vendor/autoload.php';

//
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

//Create an instance; passing `true` enables exceptions
$email = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    # code...
    if (isset($_POST["register"])) {
        # code...
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $correo = $_POST["correo"];

        //
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
            if ($email->send()) {
                # code...
                //echo "error al enviar el email".$email->ErrorInfo;
                header("Location: ../register.php");
                exit();
            } else {
                echo "no se envio el email";
                exit();
            }

        } catch (Exception $error) {
            //throw $th;
            echo "ERROR AL ENVIAR EL EMAIL => ". $error->getMessage();
            exit();
        }
    }
}

?>