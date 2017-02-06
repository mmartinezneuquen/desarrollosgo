<?php
class RolPeer
{
	public static function Alarmas($roles) 
	{
		$rolCondition = "ar.IdRol ".(is_array($roles) ? 
			"in (".implode(',', $roles).")" : 
			"= $roles");

		$sql = "SELECT
			a.*
		FROM
			alarma a 
			INNER JOIN alarmarol ar ON a.IdAlarma = ar.IdAlarma
		WHERE
			a.Activo = 1 
			AND	a.Alcance in (1, 3) 
			AND	$rolCondition
		ORDER BY
			a.Nombre";

		return $sql;
	}

	public static function Roles($filter = "")
	{
		$where = $filter ? "WHERE Nombre like '%$filter%' " : "";

		$sql = "SELECT IdRol, Nombre FROM rol $where ORDER BY Nombre";

		return $sql;
	}	

	public static function RolesHome($filter = "")
	{
		$where = $filter ? "WHERE Nombre like '%$filter%' " : "";

		$sql = "SELECT 
			IdRol,
			Nombre, 
			(SELECT GROUP_CONCAT(DISTINCT (CONCAT(' <span class=''b''>',IFNULL(boton.Titulo,''),'</span> ')) SEPARATOR '')
			 FROM 
			 	rolboton
			 	INNER JOIN boton ON rolboton.IdBoton = boton.IdBoton
			 WHERE rol.IdRol = IdRol AND boton.Activo
			) as Botones,
			(SELECT GROUP_CONCAT(DISTINCT (CONCAT(' <span class=''i''>',IFNULL(pagina.Descripcion,''),'</span> ')) SEPARATOR '')
			 FROM 
			 	rolpagina
			 	INNER JOIN pagina ON rolpagina.IdPagina = pagina.IdPagina
			 WHERE rol.IdRol = IdRol AND pagina.Activa
			) as PaginasSgo 
		FROM 
			rol 
		$where 
		ORDER BY Nombre";

		return $sql;
	}	

	public static function RolesUsuario($idUsuario) 
	{
		$sql = "SELECT IdRol FROM usuario_rol WHERE IdUsuario = '$idUsuario'";

		return $sql;	
	}

	public static function LastId() 
	{
		$sql = "SELECT max(IdRol) AS IdRol FROM rol";

		return $sql;
	}

}

?>
