<?php

$maxFileSize = 5 * 1024 * 1024; //5 Mb Tamaño maximo del documento adjunto

$mimeTypes = [
    'pdf' => 'application/pdf',
	'jpg' => 'image/jpeg',
	'png' => 'image/png',
    'xls'=>'application/vnd.ms-excel',
    'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'doc'=>'application/msword',
    'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
	//'gif' => 'image/gif',
];
$storePath = dirname(__FILE__).'/../../output/documentos/tmp/';
//$storeTmpPath = dirname(__FILE__).'/../../output/documentos/tmp';

$doc = isset($_FILES['file']) ? $_FILES['file'] : false;

$n = $_REQUEST['n'];
$cert = $_REQUEST['cert'];


try 
{
    if (!isset($doc['error']) || is_array($doc['error']))
        throw new RuntimeException('No se selecciono ningún archivo');

    switch ($doc['error']) 
    {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No se selecciono ningún archivo');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('El archivo supera el tamaño permitido (5MB)');
        default:
            throw new RuntimeException('Error desconocido');
    }

	// restringe el Tamaño
    if ($doc['size'] > $maxFileSize)
        throw new RuntimeException('El archivo supera el tamaño permitido (5MB)');

    // restringe el Contenido (deberia mejorarse mucho esta verificación)
    if (!($ext = array_search($doc['type'], $mimeTypes, true)))
        throw new RuntimeException('Formato Inválido');

    $filename = "cert_".$cert."__document_".$n."_".date('YmdHis').".$ext";

    if (!move_uploaded_file($doc['tmp_name'], $storePath.$filename))
        throw new RuntimeException('No pudo almacenarse el archivo en su lugar de destino');

    $msg = 'Archivo cargado correctamente';
    $success = true;
} 
catch (RuntimeException $e) 
{
    $msg = $e->getMessage();
    $success = false;
    $filename = "";
}


exit(json_encode([
	'numeroDoc' => $n, // vuela
	'cert' => $cert, // vuela
	'success' => $success,
	'message' => $msg,
	'tmp_name' => $doc['tmp_name'], // vuela
	'filename' => $filename // vuela
]));


?>