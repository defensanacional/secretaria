<?php
require 'vendor/autoload.php';
require 'env.php'; // carga variables de entorno

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['website'])) {
        exit(); // Honeypot detectado
    }

    // Sanitización básica
    function limpiar($campo) {
        return htmlspecialchars(trim($campo));
    }

    // Datos
    $nombre = limpiar($_POST['nombre']);
    $apellido = limpiar($_POST['apellido']);
    $rfc_curp = limpiar($_POST['rfc_curp']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $direccion = limpiar($_POST['direccion']);
    $ciudad = limpiar($_POST['ciudad']);
    $estado = limpiar($_POST['estado']);
    $cp = limpiar($_POST['cp']);
    $telefono = limpiar($_POST['telefono']);
    $pregunta = limpiar($_POST['pregunta']);
    $fecha_compra = limpiar($_POST['fecha_compra']);
    $vehiculo_interes = limpiar($_POST['vehiculo_interes']);
    $banco = limpiar($_POST['banco']);

    $ine_frente = $_FILES['ine_frente'];
    $ine_reverso = $_FILES['ine_reverso'];

    $mail = new PHPMailer(true);

    try {
        // SMTP Zoho
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('ZOHO_USER');
        $mail->Password = getenv('ZOHO_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(getenv('ZOHO_USER'), 'Formulario Web');
        $mail->addAddress(getenv('ZOHO_USER'));

        $mail->isHTML(true);
        $mail->Subject = 'Nuevo formulario recibido';
        $mail->Body = "
            <h2>Datos del formulario</h2>
            <p><strong>Nombre:</strong> $nombre $apellido</p>
            <p><strong>RFC/CURP:</strong> $rfc_curp</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Dirección:</strong> $direccion</p>
            <p><strong>Ciudad:</strong> $ciudad</p>
            <p><strong>Estado:</strong> $estado</p>
            <p><strong>CP:</strong> $cp</p>
            <p><strong>Teléfono:</strong> $telefono</p>
            <p><strong>Pregunta:</strong> $pregunta</p>
            <p><strong>Fecha de compra:</strong> $fecha_compra</p>
            <p><strong>Vehículo de interés:</strong> $vehiculo_interes</p>
            <p><strong>Banco:</strong> $banco</p>
        ";

        if ($ine_frente['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($ine_frente['tmp_name'], 'INE_Frente.' . pathinfo($ine_frente['name'], PATHINFO_EXTENSION));
        }
        if ($ine_reverso['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($ine_reverso['tmp_name'], 'INE_Reverso.' . pathinfo($ine_reverso['name'], PATHINFO_EXTENSION));
        }

        $mail->send();
        header("Location: formulario.html?success=1");
        exit;
    } catch (Exception $e) {
        header("Location: formulario.html?error=" . urlencode($mail->ErrorInfo));
        exit;
    }
}
?>
