<?php
class RolRecord extends TActiveRecord
{
	const TABLE='rol';

	public $IdRol;
	public $Nombre;
	//public $Activo;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>