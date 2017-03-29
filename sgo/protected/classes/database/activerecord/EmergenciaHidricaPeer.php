<?php
class EmergenciaHidricaRecord extends TActiveRecord
{
	const TABLE='emergenciahidrica';

	public $IdEmergenciaHidrica;
	public $IdLocalidad;
	public $FechaSolicitud;
	public $FechaRelevamiento;
	public $Solicitud;
	public $Prioridad;
	public $Presupuesto;
	public $AccionesRealizadas;
	public $IdFufi;
	public $ImporteAsignado;
	public $FechaEnvioAporte;
	public $CombustibleEntregado;
	public $Expediente;
	public $Cumplido;
	public $NrodePedido;
	public $FechaExpediente;
	public $Observaciones;
	public $Activo;
	
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>