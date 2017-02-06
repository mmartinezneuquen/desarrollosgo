<?php
class RendicionCuentasRecord extends TActiveRecord
{
  const TABLE='rendicioncuentas';

  public $IdRendicionCuentas;
  public $IdCertificacion;
  public $Orden;
  public $Proyecto;
  public $IdLocalidad;
  public $Empresa;
  public $Cuit;
  public $Factura;
  public $Recibo;
  public $FechaEmision;
  public $Concepto;
  public $FechaCancelacion;
  public $OrdenDePago;
  public $Monto;
  public $Observaciones;
  public $Estado;
  public $Revision;
  public $Activo;

  public static function finder($className=__CLASS__)
  {
    return parent::finder($className);
  }
}
?>
