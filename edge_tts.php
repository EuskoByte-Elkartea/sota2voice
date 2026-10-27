<?php
// EDGE_TTS.PHP
//
// SOLICITA A MS LA SINTESIS DEL MENSAJE PASADO POR PARAMETRO
//
// NECESARIA LA LIBRERIA EDGETTS DE AFAYA https://github.com/andresayac/edge-tts-php
// -> apt install -y composer && composer require afaya/edge-tts
//
// USA LA APLICACION SOX PARA CONVERTIR EL ARCHIVO MP3 GENERADO A WAV 8KHZ 16BIT MONO:
// -> apt install -y sox libsox-fmt-mp3




// SE CARGAN LIBRERIAS
require 'vendor/autoload.php';

// SE USA LA LIBRERIA EDGETTS DE AFAYA
use Afaya\EdgeTTS\Service\EdgeTTS;

// SE RECOGE EL ARGUMENTO CON EL MENSAJE. SI NO HAY NADA QUEDA NULO
$texto = $argv[1] ?? null;

// SI NO HAY ARGUMENTO SE MUESTRA LA FORMA DE USARLO
if (!$texto) {
    exit("Instrucciones: php edge_tts.php \"Mensaje\"\n");
}

$hecho = false;

// COMO MS PUEDE NO RESPONDER POR CARGA SE ESTABLECE UN BUCLE PARA QUE SIGA INTENTANDO EN CASO DE QUE NO ACEPTE SOLICITUD
while (!$hecho) {
    try {
        // SE INICIALIZA EL OBJETO TTS Y SE PIDE LA SINTESIS. SE GENERA EL ARCHIVO SALIDA MP3 QUE HABRA QUE CONVERTIR A WAV
        $tts = new EdgeTTS();
        $tts->synthesize($texto, 'es-ES-AlvaroNeural', ['rate' => '-25%']);
        $tts->toFile('mensaje');
        $hecho = true;
    } catch (\Throwable $e) {
        // EN CASO DE QUE NO SE LOGRE SE REINTENTA
        echo "FALLO: Reintentando\n";
        sleep(3);
    }
}

// SE USA SOX PARA CONVERTIR EL MP3 A WAV 8KHZ 16BIT Y MONO Y SE ENVIA AL ARCHIVO MENSAJE.WAV
exec("sox mensaje.mp3 -r 8000 -c 1 -b 16 mensaje.wav");

// SE ELIMINA EL MP3 TEMPORAL
if (file_exists('mensaje.mp3')) {
    unlink('mensaje.mp3');
}

echo "mensaje.wav generado.\n";
