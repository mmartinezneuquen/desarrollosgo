<?php
// datos para la coneccion a mysql
define('DB_SERVER','localhost');
define('DB_NAME','mospneuquen_db');
define('DB_USER','mospneuquen_usr');
define('DB_PASS','0wHBzSyyT7');
// datos para el envío de mail
define('MAIL_USER','no-reply@mospneuquen.com.ar');
define('MAIL_PASS','noreply$123');

include_once("protected/classes/phpgmailer/PHPGMailer.php");

$con = mysql_connect(DB_SERVER,DB_USER,DB_PASS) or die(mysql_error());
mysql_select_db(DB_NAME,$con);
mysql_set_charset ('utf8');

//$sql = "select * from usuario where nullif(Email, '') is not null and Activo=1";
$query = mysql_query($sql) or die(mysql_error());	

// recorro usuarios con mail
while($row = mysql_fetch_object($query)) {
	$email = $row->Email;
	//$email="matias.muller@puntogap.com"; //>> para prueba de desarrollo
	$idRol = $row->IdRol;

	$idOrganismo = $row->IdOrganismo;
	$sqlAlarmas = "select a.* from alarma a inner join alarmarol ar on a.IdAlarma=ar.IdAlarma where
					a.Activo=1 and a.Alcance in (1, 3) and ar.IdRol=$idRol order by a.Nombre";
	$queryAlarmas = mysql_query($sqlAlarmas) or die(mysql_error());
	$alarmasUsuario = array();
	$where = (!is_null($idOrganismo) ? "where v.IdOrganismo=".$idOrganismo : "");
	//recorro alarmas activas para ese mail
	while($rowAlarmas = mysql_fetch_object($queryAlarmas)) {
		$sqlResultados =  "select count(*) as Cantidad from (" . $rowAlarmas->Query . ") v $where";
		$queryResultados = mysql_query($sqlResultados) or die(mysql_error());
		$count = 0;

		while($rowResultados = mysql_fetch_object($queryResultados)) {
			
			if($rowResultados->Cantidad>0){
				$alarmasUsuario[] = array('Nombre' => $rowAlarmas->Nombre, 'Cantidad' => $rowResultados->Cantidad);
			}

		}

	}
//echo "<pre>";print_r($alarmasUsuario); die();

	if(count($alarmasUsuario)){
		$body = "<h3 style='color: #c00;'>Existen alarmas pendientes2</h3>";
		$body .= "<table><tr><td align='center'><strong>Alarma</strong></td><td align='center'><strong>Cantidad</strong></td></tr>";

		foreach ($alarmasUsuario as $a) {
			$body .= "<tr><td>".$a['Nombre']."</td><td align='center'>".$a['Cantidad']."</td></tr>";
		}

		$body .= "</table>";
		$mail = new PHPGMailer();
		$mail->CharSet = "utf-8";
		$mail->ContentType = "text/html";
		$mail->Username = MAIL_USER;
		$mail->Password = MAIL_PASS;
		$mail->From = MAIL_USER;
		$mail->FromName = 'S.G.O. - Sistema de Gestión de Obras';
		$mail->Subject = 'Resumen semanal de alarmas';
		$mail->Body = $body;
		$mail->AddAddress($email);
		$mail->Send();
	}
	
}


?>
