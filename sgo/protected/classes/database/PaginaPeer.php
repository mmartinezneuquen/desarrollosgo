<?php
class PaginaPeer
{
	public static function PaginasRol($roles) 
	{
		$rolCondition = $roles ? ("IdRol ".(is_array($roles) ? 
			"in (".implode(',', $roles).")" : 
			"= $roles")) : "FALSE";

		$sql = "SELECT DISTINCT
			pagina.*
		FROM
			pagina
			INNER JOIN rolpagina ON pagina.IdPagina = rolpagina.IdPagina
		WHERE
			pagina.Activa = 1 
			AND	$rolCondition
		ORDER BY
			Descripcion";

		return $sql;
	}

	public static function Paginas() 
	{
		$sql = "SELECT * FROM pagina WHERE pagina.Activa = 1 ORDER BY Descripcion";

		return $sql;
	}

}
?>
