<?php
class CompromisoRecord extends TActiveRecord
{
	const TABLE='compromiso';

	public $IdCompromiso;
	public $Fecha;
	public $IdLocalidad;
	public $Compromiso;
	public $IdResponsable;
	public $Plazo;
	public $Latitud;
	public $Longitud;
	public $FechaRegistro;
	public $IdUsuario;
	public $IdObra;
	public $Activo;
	public $Cerrado;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}	
}
?>