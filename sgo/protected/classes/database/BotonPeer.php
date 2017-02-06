<?php
class BotonPeer
{
	public static function BotonesRol($roles) 
	{
		$rolCondition = $roles ? ("IdRol ".(is_array($roles) ? 
			"in (".implode(',', $roles).")" : 
			"= $roles")) : "FALSE";

		$sql = "SELECT DISTINCT
			boton.*
		FROM
			boton
			INNER JOIN rolboton ON boton.IdBoton = rolboton.IdBoton
		WHERE
			boton.Activo = 1 
			AND	$rolCondition
		ORDER BY
			Titulo";

		return $sql;
	}

	public static function Botones() 
	{
		$sql = "SELECT * FROM boton WHERE boton.Activo = 1 ORDER BY Titulo";

		return $sql;
	}

}
?>
