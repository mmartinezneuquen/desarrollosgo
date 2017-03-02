<?php
class RendicionCuentasPeer
{
	public static function RendicionesByCertificacion($idCertificacion){
		$sql= "SELECT 
				  rendicioncuentas.IdRendicionCuentas, 
				  rendicioncuentas.IdCertificacion, 
          		  contrato.IdContrato,
          		  contrato.IdObra,
				  rendicioncuentas.Orden, 
				  rendicioncuentas.Proyecto,  
				  rendicioncuentas.Empresa, 
				  rendicioncuentas.Cuit, 
				  rendicioncuentas.Factura, 
				  rendicioncuentas.Recibo, 
				  DATE_FORMAT(FechaEmision,'%d/%m/%Y') as FechaEmision,
				  rendicioncuentas.Concepto,
				  DATE_FORMAT(FechaCancelacion,'%d/%m/%Y') as FechaCancelacion,
				  rendicioncuentas.OrdenDePago, 
				  rendicioncuentas.Monto, 
				  rendicioncuentas.Observaciones,
				  CASE rendicioncuentas.Estado
		              WHEN 0 THEN 'Sujeto a Revision'
		              WHEN 1 THEN 'Aprobado'
		              WHEN 2 THEN 'Rechazado'
		              WHEN 3 THEN 'Rechazado con Revision'
		              END as Estado, 
				  rendicioncuentas.Revision, 
				  rendicioncuentas.Activo 
			FROM rendicioncuentas 
			inner join certificacion on certificacion.IdCertificacion = rendicioncuentas.IdCertificacion
      		inner join contrato ON contrato.IdContrato = certificacion.IdContrato
			WHERE rendicioncuentas.idCertificacion = $idCertificacion and rendicioncuentas.Activo=1";
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