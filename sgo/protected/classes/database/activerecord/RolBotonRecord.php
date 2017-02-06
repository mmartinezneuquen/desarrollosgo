<?php
class RolBotonRecord extends TActiveRecord
{
	const TABLE='rolboton';

	public $IdRol;
	public $IdBoton;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>