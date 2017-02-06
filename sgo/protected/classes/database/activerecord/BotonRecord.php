<?php
class BotonRecord extends TActiveRecord
{
	const TABLE='boton';

	public $IdBoton;
	public $Nombre;
	public $Titulo;
	public $Img;
	public $Link;
	public $Activo;
	public $Orden;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>