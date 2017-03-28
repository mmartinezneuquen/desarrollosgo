<?php
class CompromisoPeer
{

	public static function CompromisoSelect(){
		$sql = "
				Select compromiso.IdCompromiso as IdCompromiso, compromiso.fecha as fecha,
				compromiso.compromiso as compromiso,
				 
				 usuario.ApellidoNombre as responsable, compromisoorganismo.tag as organismo
				From compromiso 
				join compromisoresponsable on compromiso.IdResponsable = compromisoresponsable.IdCompromisoResponsable
				join usuario on compromisoresponsable.IdUsuario = usuario.IdUsuario
				join compromisoorganismo on compromisoorganismo.IdCompromisoOrganismo = compromisoresponsable.IdOrganismo
				Where compromiso.activo = 1
				Order by compromiso.Fecha DESC";
		return $sql;	
	}

	public static function CompromisoHome($idLocalidad, $idOrganismo, $IdResponsable, $estadoCompromiso, $revisionCompromiso ,$busqueda){
		//$where = "";
		$where = " Where compromiso.activo = 1 ";

		if($idLocalidad!="" and $idLocalidad!="0"){
			$where .= " and (compromiso.IdLocalidad = $idLocalidad) ";
		}

		if($idOrganismo!="" and $idOrganismo!="0"){
			$where .= " and (compromisoorganismo.IdCompromisoOrganismo = $idOrganismo) ";
		}

		if($IdResponsable!="" and $IdResponsable!="0"){
			$where .= " and (compromisoresponsable.IdCompromisoResponsable = $IdResponsable) ";
		}

		if($estadoCompromiso!="" and $estadoCompromiso!="2"){
			$where .= " and (compromiso.Cerrado =  $estadoCompromiso) ";
		}

		if($revisionCompromiso!="2"){
				if($revisionCompromiso=="1"){
				$where .= " and (Select count(idcompromiso) from compromisorevision where compromisorevision.IdCompromiso = compromiso.IdCompromiso) >= $revisionCompromiso";
			}
			else{
				$where .= " and (Select count(idcompromiso) from compromisorevision where compromisorevision.IdCompromiso = compromiso.IdCompromiso) = $revisionCompromiso";
			}			
		}		

		if($busqueda!=""){
			$where .= " and (compromiso.compromiso like '%$busqueda%') ";
		}

		$sql = 
				"Select compromiso.IdCompromiso as IdCompromiso, 
				DATE_FORMAT(compromiso.fecha,'%d-%m-%Y') as fecha,
				localidad.Nombre, localidad.Nombre,
				CONCAT (SUBSTRING(compromiso.compromiso,1, 100),'...') as compromiso,				
				usuario.ApellidoNombre as responsable , compromisoorganismo.tag as organismo,
				ifnull((Select count(idcompromiso) from compromisorevision where compromisorevision.IdCompromiso = compromiso.IdCompromiso) ,0) as Revisiones,
				(case
				  	when compromiso.Cerrado=0 then 'Abierto'
				  	when compromiso.Cerrado=1 then 'Cumplido'
				  end) as estadoCompromiso
				From compromiso 
				join compromisoresponsable on compromiso.IdResponsable = compromisoresponsable.IdCompromisoResponsable
				join usuario on compromisoresponsable.IdUsuario = usuario.IdUsuario
				join compromisoorganismo on compromisoorganismo.IdCompromisoOrganismo = compromisoresponsable.IdOrganismo
				join localidad on localidad.IdLocalidad = compromiso.IdLocalidad
				$where
				Order by compromiso.Fecha DESC";

		//echo "<pre>";print_r($sql); die();
		return $sql;
	}

	public static function UltimaCreacion($idLocalidad, $idOrganismo, $IdResponsable, $busqueda){
		$where = " Where compromiso.activo = 1 ";

		if($idLocalidad!="" and $idLocalidad!="0"){
			$where .= " and (compromiso.IdLocalidad = $idLocalidad) ";
		}

		if($idOrganismo!="" and $idOrganismo!="0"){
			$where .= " and (compromisoorganismo.IdCompromisoOrganismo = $idOrganismo) ";
		}

		if($IdResponsable!="" and $IdResponsable!="0"){
			$where .= " and (compromisoresponsable.IdCompromisoResponsable = $IdResponsable) ";
		}		

		if($busqueda!=""){
			$where .= " and (compromiso.compromiso like '%$busqueda%') ";
		}

		$sql = "Select DATE_FORMAT(MAX(FechaRegistro),'%d-%m-%Y') as ultimo From compromiso
				join compromisoresponsable on compromiso.IdResponsable = compromisoresponsable.IdCompromisoResponsable
				join usuario on compromisoresponsable.IdUsuario = usuario.IdUsuario
				join compromisoorganismo on compromisoorganismo.IdCompromisoOrganismo = compromisoresponsable.IdOrganismo
				join localidad on localidad.IdLocalidad = compromiso.IdLocalidad
				$where";

		//echo "<pre>";print_r($sql); die();
		return $sql;
	}

	public static function UltimaRevision($idLocalidad, $idOrganismo, $IdResponsable, $busqueda){
		$where = " Where compromiso.activo = 1 ";

		if($idLocalidad!="" and $idLocalidad!="0"){
			$where .= " and (compromiso.IdLocalidad = $idLocalidad) ";
		}

		if($idOrganismo!="" and $idOrganismo!="0"){
			$where .= " and (compromisoorganismo.IdCompromisoOrganismo = $idOrganismo) ";
		}

		if($IdResponsable!="" and $IdResponsable!="0"){
			$where .= " and (compromisoresponsable.IdCompromisoResponsable = $IdResponsable) ";
		}		

		if($busqueda!=""){
			$where .= " and (compromiso.compromiso like '%$busqueda%') ";
		}
		$sql = 
			"Select DATE_FORMAT(MAX(compromisorevision.Fecha),'%d-%m-%Y') as ultimo From compromisorevision
				join compromiso on compromiso.IdCompromiso = compromisorevision.IdCompromiso
				join compromisoresponsable on compromiso.IdResponsable = compromisoresponsable.IdCompromisoResponsable
				join usuario on compromisoresponsable.IdUsuario = usuario.IdUsuario
				join compromisoorganismo on compromisoorganismo.IdCompromisoOrganismo = compromisoresponsable.IdOrganismo
				join localidad on localidad.IdLocalidad = compromiso.IdLocalidad
				$where";
		return $sql;
	}

	public static function LocalidadesConCompromisoSelect(){
		$sql = 
				"Select l.idLocalidad, l.Nombre 
				From compromiso c
					inner join localidad l  on l.IdLocalidad = c.IdLocalidad  
				Group by l.IdLocalidad
				Order by c.idLocalidad";
		return $sql;
	}

	public static function OrganismosConCompromisoSelect(){
		$sql = "Select IdCompromisoOrganismo, Tag From compromisoorganismo Order by Tag;";
		return $sql;
	}



	public static function RevisionesDelCompromiso($IdCompromiso){

		$sql = "Select cr.IdCompromisoRevision, cr.IdCompromiso, u.ApellidoNombre as Usuario, DATE_FORMAT(cr.Fecha,'%d-%m-%Y') as Fecha, cr.Revision
			from compromisorevision cr inner join usuario u on cr.IdUsuario = u.IdUsuario
			where cr.IdCompromiso = $IdCompromiso;";
		return $sql;
	}


}
?>
