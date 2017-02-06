<?php
class RendicionCuentasPeer
{
	public static function RendicionesByCertificacion($idCertificacion){
		$sql= "SELECT 
				  IdRendicionCuentas, 
				  IdCertificacion, 
				  Orden, 
				  Proyecto,  
				  localidad.Nombre as 'Localidad', 
				  Empresa, 
				  Cuit, 
				  Factura, 
				  Recibo, 
				  DATE_FORMAT(FechaEmision,'%d/%m/%Y') as FechaEmision,
				  Concepto,
				  DATE_FORMAT(FechaCancelacion,'%d/%m/%Y') as FechaCancelacion,
				  OrdenDePago, 
				  Monto, 
				  Observaciones,
				  CASE rendicioncuentas.Estado
		              WHEN 0 THEN 'Sujeto a Revision'
		              WHEN 1 THEN 'Aprobado'
		              WHEN 2 THEN 'Rechazado'
		              WHEN 3 THEN 'Rechazado con Revision'
		              END as Estado, 
				  Revision, 
				  Activo 
			FROM rendicioncuentas inner join localidad on rendicioncuentas.IdLocalidad = localidad.IdLocalidad
			WHERE idCertificacion = $idCertificacion and rendicioncuentas.Activo=1";
		return $sql;
	}

	public static function ProximaOrderRendicionesByCertificacion($idCertificacion){
		$sql= "Select max(orden)+1 as proximo from rendicioncuentas where IdCertificacion = $idCertificacion";
		return $sql;
	}

	public static function TotalMontoRendicionesByCertificacion($idCertificacion){
		$sql= "Select sum(monto) as monto from rendicioncuentas where IdCertificacion = $idCertificacion and activo=1";
		return $sql;
	}



}