<?php
class DocumentoRevisionRecord extends TActiveRecord
{
	const TABLE='documentorevision';

	public $IdRevision;
	public $IdDocumento;
	public $IdCertificacion;
	public $Fecha;
	public $IdEstado;
	public $Revision;
	public $Motivo;
	public $IdUsuario;
	public $Archivo;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>