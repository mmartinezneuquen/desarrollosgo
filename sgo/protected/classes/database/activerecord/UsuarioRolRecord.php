<?php
class UsuarioRolRecord extends TActiveRecord
{
	const TABLE='usuario_rol';

	public $IdUsuario;
	public $IdRol;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>