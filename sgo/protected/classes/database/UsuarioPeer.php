<?php
class UsuarioPeer
{
	public static function Authorization($username, $page)
	{
		$sql = "SELECT
			*
		FROM
			usuario u 
			INNER JOIN usuario_rol ur ON ur.IdUsuario = u.IdUsuario
			INNER JOIN rolpagina rp ON ur.IdRol = rp.IdRol 
			INNER JOIN pagina p ON rp.IdPagina = p.IdPagina
		WHERE
			u.Username = '$username' AND
			p.Pagina = '$page' AND
			u.Activo = 1 AND
			p.Activa = 1";

		return $sql;
	}


	public static function UsuariosHome($idOrganismo, $idEstado, $filter)
	{
		$where = [];

		if ($idOrganismo != "" && $idOrganismo != "0")
        	$where[] = "u.IdOrganismo = $idOrganismo";

		if ($idEstado != "-1" && $idEstado != "")
        	$where[] = "u.Activo = $idEstado";

		if ($filter != "")
        	$where[] = "u.Username like '%$filter%' or u.ApellidoNombre like '%$filter%'";

		$where = sizeof($where) ? "WHERE ".implode(' AND ', array_map(function($elem){
            return "($elem)";
        }, $where)) : "";


		$sql = "SELECT
			u.IdUsuario,
			u.ApellidoNombre,
			u.Username,
			(SELECT GROUP_CONCAT(DISTINCT r.Nombre SEPARATOR ' - ') 
			 FROM 
			 	usuario_rol ur 
				INNER JOIN rol r ON ur.IdRol = r.IdRol
			 WHERE ur.IdUsuario = u.IdUsuario
			) AS Rol,
			og.Nombre AS Organismo,
			IF(u.Activo = 1, 'Si', 'No' ) AS ActivoDesc
		FROM
			usuario u 
			LEFT JOIN organismo og ON u.IdOrganismo = og.IdOrganismo
		$where
		ORDER BY
			u.ApellidoNombre";

		return $sql;
	}


	public static function IngresosHome($idOrganismo, $orden, $tipo)
	{

		$where = [];

		$where[] = "u.Activo = 1";

		if ($idOrganismo != "" && $idOrganismo != "0")
        	$where[] = "u.IdOrganismo = $idOrganismo";

		$where = sizeof($where) ? "WHERE ".implode(' AND ', array_map(function($elem){
            return "($elem)";
        }, $where)) : "";


		switch ($orden) {
			case "1":
				$order = "Usuario";
				break;
			case "2":
				$order = "Organismo";
				break;
			default:
				$order = "UltimoLogin";
				break;
		}

		$type = ($tipo == "1") ? "DESC" : "ASC";

		$sql = "SELECT
			o.Nombre AS Organismo,
			u.ApellidoNombre AS Usuario,
			(SELECT GROUP_CONCAT(DISTINCT r.Nombre SEPARATOR ' - ') 
			 FROM 
			 	usuario_rol ur 
				INNER JOIN rol r ON ur.IdRol = r.IdRol
			 WHERE ur.IdUsuario = u.IdUsuario
			) AS Rol,
			(SELECT max(FechaHoraLogin) FROM ingreso WHERE IdUsuario = u.IdUsuario) AS UltimoLogin,
			(SELECT max(FechaHoraLogout) FROM ingreso WHERE IdUsuario = u.IdUsuario) AS UltimoLogout
		FROM
			usuario u 
			INNER JOIN organismo o ON u.IdOrganismo = o.IdOrganismo 
		$where 
		ORDER BY $order $type";

		return $sql;
	}

	public static function LastId() 
	{
		$sql = "SELECT max(IdUsuario) AS IdUsuario FROM usuario";

		return $sql;
	}
	
}
?>