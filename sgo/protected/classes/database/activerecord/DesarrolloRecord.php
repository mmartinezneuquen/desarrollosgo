<?php
class DesarrolloRecord extends TActiveRecord
{
	const TABLE='desarrollo';

	public $Pass;
	public $EnDesarrollo;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>