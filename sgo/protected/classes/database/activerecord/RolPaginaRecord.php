<?php
class RolPaginaRecord extends TActiveRecord
{
	const TABLE='rolpagina';

	public $IdRol;
	public $IdPagina;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>