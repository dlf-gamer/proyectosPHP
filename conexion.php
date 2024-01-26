<?php 

//SE CREAN LASVARIABLES
$hostname = "localhost";//NOMBRE DEL SERVIDOR
$database = "crud";//NOMBRE DE LA BASE DE DATOS
$username = "root";//NOMBRE DEL USUARIO
$password = "";//CONTRASEÑA

$opciones = [PDO::ATTR_CASE => PDO::CASE_UPPER, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

//CAPTURAR PARA MOSTRAR ERRORES
try {
    //CADENA DE CONEXION PARA CONECTAR A LA BASE DE DATOS
    $conexion = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    //Configurar PDO para mostrar excepciones en caso de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);

    //VERIFICAR SI CONECTO A LA BASE DE DATOS
    if ($conexion) {
        # code...
        //echo "CONEXION EXITOSA";
    }
    //MOSTRAR MENSAJE DE ERRORES
} catch (\Exception $error) {
    //throw $th;
    echo "ERROR DE CONEXION => ". $error->getMessage();
}

?>
