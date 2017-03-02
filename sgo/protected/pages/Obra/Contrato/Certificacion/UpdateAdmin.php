<?php

//>> Clases para manejar números string... poner en archivo aparte
abstract class StrNum 
{
    abstract protected function strFormat($valor);
    abstract protected function numFormat($valor);

    const NUMERIC_STRING = '/^\s*\d+\.\d+\s*$/';

    public function num($valor = "0")
    {
        return is_numeric($valor) || preg_match(self::NUMERIC_STRING, $valor) ? $valor * 1 : $this->numFormat($valor);
    }

    public function str($valor = 0)
    {
        if (preg_match(self::NUMERIC_STRING, $valor)) $valor = $valor * 1;
        return is_string($valor) ? $this->num($valor) : $this->strFormat($valor);
    }

    public function sumNum($a = "0", $b = "0")
    {
        return $this->num($a) + $this->num($b);
    }

    public function sumStr($a = "0", $b = "0")
    {
        return $this->str($this->sumNum($a, $b));
    }
}

class Curr extends StrNum
{
    protected function strFormat($valor)
    {
        return number_format($valor, 2, ',', '.');
    }

    protected function numFormat($valor)
    {
        return floatval(str_replace(",", ".", str_replace(".", "", $valor)));
    }
}


class Prcnt extends StrNum
{
    protected function strFormat($valor)
    {
        return number_format($valor, 2, '.', '');
    }

    protected function numFormat($valor)
    {
        return floatval($valor);
    }
}





class UpdateAdmin extends PageBaseSP 
{
    public $MunicipioAdmin;
    protected $cambiando;

    public function onLoad($param) 
    {
        parent::onLoad($param);

        if (!$this->IsPostBack) 
        {
            $this->cambiando = false;
            $this->MunicipioAdmin = in_array("11", $this->Session->get("usr_roles")); //>>!! Diseñar un sistema que no use los números de roles!!
            
            $idObra = $this->Request["ido"];
            $idContrato = $this->Request["idc"];
            $id = $this->Request["id"];
            $this->MunicipioAdmin = in_array("11", $this->Session->get("usr_roles")); // Actualiza para no hacer un hdn molesto
            if (!is_null($id)) {
                $this->lblAccion->Text = "Modificar Certificación";
                $this->cargarDatos($id, $idObra, $idContrato);
            } else {
                $this->btnAprobarCertificacion->Visible = $this->MunicipioAdmin;
                if ($this->SugerirNumeroCertificado($idContrato, 0) == 0)
                {
                    //echo "NUEVO";
                }
                else
                {
                    //echo "SEGUNDO";
                }
            }
            $this->LoadDataRelated($idObra, $idContrato, $id);
            $this->cambios->Value = "0";
            $this->inicializarListaCamposHabiles();
        }
    }

    public function LoadDataRelatedAcum($idObra, $idContrato, $id) 
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $periodo = preg_replace('/(\d+)\/(\d+)/','$2$1', $this->dtpPeriodo->Text);
        $contrato = ContratoRecord::finder()->findByPk($idContrato); //>> resolver usando estos valores como propiedades del objeto

        $certificacionAnterior = $this->CreateDataSource("CertificacionPeer", "getCertificacionAnterior", $idContrato, $periodo);
        
        $this->txtImporteCertifAnterior->Text = $curr->str($certificacionAnterior[0]["MontoAvance"]); //$prcnt->str($certificacionAnterior[0]["ImporteCertifAnterior"]);
        $this->txtPorcentajeCertifAnterior->Text = $curr->str($certificacionAnterior[0]["PorcentajeAvance"]);  //$prcnt->str($certificacionAnterior[0]["ImporteCertifAnterior"] / $contrato->Monto * 100);

        $this->lblFechaUltimoPago->Text = (isset($id) && $id && ($result = $this->CreateDataSource("PagoPeer","PagosByCertificadoAnterior", $id, $idContrato))) ? 
            $result[0]['Fecha']
            : "--/--/--";

        //>>?? Si sólamente se puede cargar en el 1er certificado... de qué sirve el acumulado?
        $this->txtAnticipoAcumulado->Text = $curr->str($certificacionAnterior[0]["DescuentoAnticipoAnterior"]);
    }

    public function LoadDataRelated($idObra, $idContrato, $id) 
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $this->LoadDataRelatedAcum($idObra, $idContrato, $id);

        $obra = ObraRecord::finder()->findByPk($idObra);
        $organismo = OrganismoRecord::finder()->findByPk($obra->IdOrganismo);
        $contrato = ContratoRecord::finder()->findByPk($idContrato);
        $proveedor = ProveedorRecord::finder()->findByPk($contrato->IdProveedor);
        $periodo = preg_replace('/(\d+)\/(\d+)/','$2$1', $this->dtpPeriodo->Text);

        $localidades = $this->CreateDataSource("ObraPeer", "LocalidadesPorObra", $idObra);
        $this->lblProveedor->Text = $proveedor->RazonSocial;
        $this->lblObra->Text = $organismo->PrefijoCodigo . '-' . $obra->Codigo . ' ' . $obra->Denominacion;
        $this->lblLocalidades->Text = $localidades[0]["Localidades"];

        //die("eeee$periodo");
        
        $certificacionAnterior = $this->CreateDataSource("CertificacionPeer", "getCertificacionAnterior", $idContrato, $periodo);
        if ($certificacionAnterior[0]["AnticipoFinancieroAnterior"]) 
        {
            $this->txtAnticipoFinanciero->Text = $curr->str($certificacionAnterior[0]["AnticipoFinancieroAnterior"]);
            $this->txtAnticipoFinancieroPorcentaje->Text = $prcnt->str($certificacionAnterior[0]["AnticipoFinancieroAnterior"] / $contrato->Monto * 100);
            $this->txtAnticipoFinancieroPorcentaje->Enabled = false;
            $this->lblFechaPagoAnticipoFinanciero->Enabled = false;
        }

        $this->txtAnticipoOtorgadoProv->Text = $this->txtAnticipoFinanciero->Text;

        $contratoItems = $this->CreateDataSource("ContratoPeer", "ItemsByContratoCertificacion", $idContrato, $periodo, $id);
        //echo "<pre>";print_r($certificacionAnterior);die;
        $this->dgDatos->DataSource = $contratoItems;
        $this->dgDatos->dataBind();
        $this->lblDecreto->Text = $contrato->NormaLegalAutorizacion;
        $this->lblExpediente->Text = $obra->Expediente ? $obra->Expediente : "---";
        $this->lblContratista->Text = $proveedor->RazonSocial;

        //$this->lblMontoProvincia->Text = $curr->str($contrato->MontoProvincia); // Sustituido temporalmente por:
        $this->lblMontoProvincia->Text = $curr->str($contrato->Monto);

        $this->hdnMontoContrato->Value = $contrato->Monto;
        $this->lblMontoContrato->Text = $curr->str($contrato->Monto);
        if ($contrato->FechaInicio != null) {
            $fechaInicio = explode("-", $contrato->FechaInicio);
            $fechaInicio = $fechaInicio[2] . "/" . $fechaInicio[1] . "/" . $fechaInicio[0];
        } else {
            $fechaInicio = "-";
        }

        $this->lblFechaInicio->Text = $fechaInicio;
        $this->lblPlazoEjecucion->Text = $contrato->PlazoEjecucion;
        
        ////$this->txtImporteCertifAnterior->Text = $curr->str($certificacionAnterior[0]["ImporteCertifAnterior"]);
        ////$this->txtPorcentajeCertifAnterior->Text = $prcnt->str($certificacionAnterior[0]["ImporteCertifAnterior"] / $contrato->Monto * 100);
        ////if ($certificacionAnterior[0]["FechaUltimoPago"] != null) {
        ////    $fechaUltimoPago = explode("-", $certificacionAnterior[0]["FechaUltimoPago"]);
        ////    $fechaUltimoPago = $fechaUltimoPago[2] . "/" . $fechaUltimoPago[1] . "/" . $fechaUltimoPago[0];
        ////} else {
        ////    $fechaUltimoPago = "--/--/--";
        ////}
        //$this->lblFechaUltimoPago->Text = $fechaUltimoPago;

        $this->hlkVolver->NavigateUrl .= "&idc=$idContrato&ido=$idObra";

        ////$this->txtAnticipoAcumulado->Text = $curr->str($certificacionAnterior[0]["AnticipoFinancieroAnterior"]);

        $this->CalcularTotales();

//            $this->txtAnticipoOtorgadoProv->Text=number_format(0,2,",",".");
//            $this->txtFondoReparo->Text=number_format(0,2,",",".");

        /*$this->txtDescuentoAnticipoActual->Text = 
            $curr->str($this->lblSumaPorcentajeActual->Text 
            * $curr->sumNum($this->txtAnticipoAcumulado->Text, $this->txtAnticipoFinanciero->Text));*/
        
        $criteria = new TActiveRecordCriteria;
        $criteria->OrdersBy['Descripcion'] = 'asc';
    }

    public function SugerirNumeroCertificado($idContrato, $tipo, $idOrdenTrabajo = "")
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $numero = $this->CreateDataSource("CertificacionPeer", "SiguienteNumeroCertificado", $idContrato, $tipo, $idOrdenTrabajo);
        $this->txtNumero->Text = $numero[0]["Numero"];
        $this->dtpPeriodo->Text = $numero[0]["Periodo"];
        $this->txtPorcentajeAvanceReal->Text = "";
        
        return $numero[0]["Numero"];
    }

    /* DESACTIVADO */

    public function cvNumero_OnServerValidate($sender, $param) 
    {
        $idContrato = $this->Request["idc"];
        $numero = $this->txtNumero->Text;

        $criteria = new TActiveRecordCriteria;
        $criteria->Condition = 'IdContrato = :idcontrato AND NroCertificado = :numero ';
        $criteria->Parameters[':idcontrato'] = $idContrato;
        $criteria->Parameters[':numero'] = $numero;

        $id = $this->Request["id"];

        if (!is_null($id)) {
            $criteria->Condition .= ' AND IdCertificacion <> :idcertificacion';
            $criteria->Parameters[':idcertificacion'] = $id;
        }

        $finder = CertificacionRecord::finder();
        $certificacion = $finder->find($criteria);

        if (is_object($certificacion)) {
            $param->IsValid = false;
        } else {
            $param->IsValid = true;
        }
    }

    public function dtpPeriodo_OnServerValidate($sender, $param) {
        $idContrato = $this->Request["idc"];
        $periodo = explode("/", $this->dtpPeriodo->Text);
        $periodo = $periodo[1] . $periodo[0];

        $criteria = new TActiveRecordCriteria;
        $criteria->Condition = 'IdContrato = :idcontrato AND Periodo = :periodo ';
        $criteria->Parameters[':idcontrato'] = $idContrato;
        $criteria->Parameters[':periodo'] = $periodo;

        $id = $this->Request["id"];

        if (!is_null($id)) {
            $criteria->Condition .= ' AND IdCertificacion <> :idcertificacion';
            $criteria->Parameters[':idcertificacion'] = $id;
        }

        $finder = CertificacionRecord::finder();
        $certificacion = $finder->find($criteria);

        if (is_object($certificacion)) {
            $param->IsValid = false;
        } else {
            $param->IsValid = true;
        }
    }

    protected function controlesEditables() 
    {
        return [
            $this->txtNumero,
            $this->dtpPeriodo,
            $this->dtpFechaMedicion,
            $this->txtPorcentajeAvanceReal,
            //$this->txtAnticipoFinanciero,
            $this->txtAnticipoFinancieroPorcentaje,
            //$this->dtpFechaPagoAnticipoFinanciero, // lo toma de tesorería
            $this->dgDatos,

            //$this->txtAnticipoOtorgadoProv,
            //$this->txtAnticipoAcumulado,
            //$this->txtDescuentoAnticipoActual,
            //$this->txtDescuentoAnticipoAcumulado,
            $this->txtFondoReparo,

            //$this->txtTotalPagoMunicipio,
            //$this->txtTotalPagoProvincia,

            $this->btnAprobarCertificacion,
        ];
    }

    protected function inicializarListaCamposHabiles() 
    {
        $controles = $this->controlesEditables();
        $camposHabiles = [];

        foreach ($controles as $i => $control) 
        {
            $camposHabiles[$i] = $control->Enabled;
        }
        $this->hdnCamposHabiles->Value = json_encode($camposHabiles);
    }


    protected function habilitacionControlesPrimerCertificado($numero = true) 
    {
        $controles = [
            $this->pnlDatos,
            $this->txtFondoReparo,
        ];

        foreach ($controles as $i => $control)
            $control->Enabled = $numero ? true : false;

        $this->pnlDatos->Visible = $numero ? true : false;

    }



    protected function listaCamposHabiles() 
    {
        return json_decode($this->hdnCamposHabiles->Value, true);
    }

    protected function habilitacionControlesSegunAprobacion($aprobada)
    {
        $habilitado = !$aprobada;
        $controles = $this->controlesEditables();
        $camposHabiles = $this->listaCamposHabiles();

        foreach ($controles as $i => $control) {
            if ($habilitado) {
                $control->Enabled = isset($camposHabiles[$i]) ? $camposHabiles[$i] : true;
            } else {
                $control->Enabled = false;
            }
        }

        $this->btnAprobarCertificacion->Visible = $habilitado && $this->MunicipioAdmin;
        
    }

    public function cargarDatos($idCertificacion, $idObra, $idContrato) 
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $idOrganismo = $this->Session->get("usr_sgo_idOrganismo");
        $finder = ObraRecord::finder();
        $obra = $finder->findByPk($idObra);
        $contrato = ContratoRecord::finder()->findByPk($idContrato);
        if (!$this->ValidarObraOrganismo($idOrganismo, $idObra)) {
            $this->Response->Redirect("?page=Obra.Home");
        }

        $certificacion = $this->CreateDataSource("CertificacionPeer", "getCertificacion", $idCertificacion);
        $certificacion = $certificacion[0];

        $this->hdnAprobada->Value = $certificacion["Aprobada"];
        $this->habilitacionControlesSegunAprobacion($certificacion["Aprobada"]);

        

        //$this->dtpPeriodo->Enabled = false;
        $this->txtNumero->Text = $certificacion["NroCertificado"];

        $this->habilitacionControlesPrimerCertificado($certificacion["NroCertificado"]);
        
        $periodo = substr($certificacion["Periodo"], 4, 2) . "/" . substr($certificacion["Periodo"], 0, 4);
        $this->dtpPeriodo->Text = $periodo;
        $this->txtAnticipoFinanciero->Text = $curr->str($certificacion["AnticipoFinanciero"]);

        $this->txtAnticipoOtorgadoProv->Text = $curr->str($certificacion["AnticipoFinanciero"]);
        //$this->txtAnticipoOtorgadoProv->Enabled = false;
        $this->txtFondoReparo->Text = $certificacion["RetencionFondoReparo"];

        $this->txtAnticipoFinancieroPorcentaje->Text = $prcnt->str($certificacion["AnticipoFinanciero"] / $contrato->Monto * 100);

        // esto está de más... //>> evaluar en la consulta qué significa "FechaPagoAnticipo"
        if ($certificacion["FechaPagoAnticipo"] != 0) {
            $fechaAnticipo = explode("-", $certificacion["FechaPagoAnticipo"]);
            //$this->lblFechaPagoAnticipoFinanciero->Text = $fechaAnticipo[2] . "/" . $fechaAnticipo[1] . "/" . $fechaAnticipo[0];
        } else {
            //$this->lblFechaPagoAnticipoFinanciero->Text = "--/--/--";
        }

        $this->txtPorcentajeAvanceReal->Text = $prcnt->str($certificacion["PorcentajeAvanceReal"]);

        if ($certificacion["FechaMedicion"] != null) {
            $fechaMedicion = explode("-", $certificacion["FechaMedicion"]);
            $this->dtpFechaMedicion->Text = $fechaMedicion[2] . "/" . $fechaMedicion[1] . "/" . $fechaMedicion[0];
        } else {
            $this->dtpFechaMedicion->Text = "";
        }


        /*$this->dtpFechaPagoAnticipoFinanciero->Text = $contrato->FechaPagoAnticipoFinanciero ?
            preg_replace('/(\d+)[\/-](\d+)[\/-](\d+)/', "$3/$2/$1", $contrato->FechaPagoAnticipoFinanciero)
            : "";*/

        // El pago lo busca directamente desde Tesoreria (tabla "pagos"):
        $this->lblFechaPagoAnticipoFinanciero->Text = ($result = $this->CreateDataSource("ContratoPeer", "FechaPagoAnticipoFinanciero", $idContrato)) ? 
            $result[0]['Fecha'] 
            : "--/--/--";

    }

    public function btnCancelar_OnClick($sender, $param) 
    {
        $ido = $this->Request["ido"];
        $idc = $this->Request["idc"];
        $this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idc&ido=$ido");

    }

    public function btnAprobarCertificacion_OnClick($sender, $param) 
    {
        $this->hdnAprobada->Value = true;
        $this->habilitacionControlesSegunAprobacion(true);

        $this->btnCancelarAprobacion->Visible = true;
        $this->btnCancelarAprobacion->Enabled = true;
    }


    public function btnCancelarAprobacion_OnClick($sender, $param) 
    {
        $this->hdnAprobada->Value = false;
        $this->habilitacionControlesSegunAprobacion(false);

        $this->btnCancelarAprobacion->Visible = false;
        $this->btnCancelarAprobacion->Enabled = false;
    }

    public function btnAceptar_OnClick($sender, $param) 
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $this->CalcularTotales();
        if ($this->IsValid) {

            $id = $this->Request["id"];
            $idObra = $this->Request["ido"];
            $idContrato = $this->Request["idc"];

            if (!is_null($id)) {
                $finder = CertificacionRecord::finder();
                $certificacion = $finder->findByPk($id);
            } else {
                $certificacion = new CertificacionRecord();
                $certificacion->IdContrato = $idContrato;
            }

            if ($this->dtpPeriodo->Enabled) 
            {
                $periodo = explode("/", $this->dtpPeriodo->Text);
                $certificacion->Periodo = $periodo[1] . $periodo[0];
            }

            if ($this->txtNumero->Text != "") {
                $certificacion->NroCertificado = $this->txtNumero->Text;
            } else {
                $certificacion->NroCertificado = null;
            }

            $certificacion->Aprobada = $this->hdnAprobada->Value;
            $certificacion->PorcentajeAvance = $prcnt->num($this->lblSumaPorcentajeActual->Text);
            $certificacion->MontoAvance = $this->hdnSumaImporteActual->Value;
            $certificacion->DescuentoAnticipo = $curr->num($this->txtDescuentoAnticipoActual->Text);
            $certificacion->RetencionFondoReparo = $curr->num($this->txtFondoReparo->Text);
            
            $certificacionAnterior = $this->CreateDataSource("CertificacionPeer", "getCertificacionAnterior", $idContrato, $certificacion->Periodo);

            //>> esto verificaba que si no es la primera, se asignaba cero, pero mejor directamente la guardamos igual
            /*if (!$certificacionAnterior[0]["AnticipoFinancieroAnterior"] && $curr->num($this->txtAnticipoFinanciero->Text)) 
            {*/
                $certificacion->AnticipoFinanciero = $curr->num($this->txtAnticipoFinanciero->Text);
            /*} else {
                $certificacion->AnticipoFinanciero = 0;
            }*/

            $certificacion->PorcentajeAvanceReal = $this->txtPorcentajeAvanceReal->Text ?
                $prcnt->num($this->txtPorcentajeAvanceReal->Text) : "";

            $certificacion->FechaMedicion = $this->dtpFechaMedicion->Text ?
                preg_replace('/(\d+)\/(\d+)\/(\d+)/', "$3-$2-$1", $this->dtpFechaMedicion->Text) //>> Insisto, hacer Helper
                : null;

            $certificacion->TipoCertificado = 0;

            if ($this->txtNumero->Text == '0')
            {
                $certificacion->ImporteNeto = $certificacion->AnticipoFinanciero;
            } 
            else 
            {
                $certificacion->ImporteNeto = 
                    $this->hdnSumaImporteActual->Value 
                    - $curr->num($this->txtDescuentoAnticipoActual->Text) 
                    - $curr->num($this->txtFondoReparo->Text);
            }



            try {
                
                $certificacion->save();

                // Guarda la fecha de pago de anticipo financiero //>> debería también a futuro almacenarse su importe también acá
                $contrato = ContratoRecord::finder()->findByPk($idContrato);
                $contrato->FechaPagoAnticipoFinanciero = $this->lblFechaPagoAnticipoFinanciero->Text != '--/--/--' ?
                    preg_replace('/(\d+)\/(\d+)\/(\d+)/', "$3-$2-$1", $this->lblFechaPagoAnticipoFinanciero->Text) //>> Insisto, hacer Helper
                    : null;
                $contrato->save();

                foreach ($this->dgDatos->Items as $it) 
                {
                    $esPadre = (strpos($it->tcPrecioTotal->txtPrecioTotal->CssClass, 'padre') > 0);

                    if (!$esPadre) 
                    {
                        if ($it->tcPrecioTotal->hdnIdCertificacionItem->Value != 0) {
                            $finder = CertificacionItemRecord::finder();
                            $certificacionItem = $finder->findByPk($it->tcPrecioTotal->hdnIdCertificacionItem->Value);
                        } else {
                            $certificacionItem = new CertificacionItemRecord();
                            $certificacionItem->IdContratoItem = $it->tcPrecioTotal->hdnIdContratoItem->value;
                            $certificacionItem->IdCertificacion = $certificacion->IdCertificacion;
                        }
                        $certificacionItem->PorcentajeActual = $curr->num($it->tcPorcentajeActual->txtPorcentajeActual->Text);
                        $certificacionItem->ImporteActual = $curr->num($it->tcImporteActual->txtImporteActual->Text);
                        
                        $certificacionItem->save();
                    }
                }

                $this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra");
            } catch (exception $e) {
                $this->Log($e->getMessage(), true);
            }
        }
    }

    public function txtImporteActual_OnTextChanged($sender, $param) 
    {
        if (!$this->cambiando)
        {
            $this->cambiando = true;

            $curr = new Curr();
            $prcnt = new Prcnt();

            $monto = $curr->num($sender->Text);
            $sender->Text = $curr->str($monto);
            
            if ($monto) {
                $sender->Parent->Parent->tcPorcentajeActual->txtPorcentajeActual->Text = $prcnt->str($monto / $curr->num($sender->Parent->Parent->tcPrecioTotal->txtPrecioTotal->Text) * 100);
                $sender->Parent->Parent->tcPorcentajeAcum->txtPorcentajeAcum->Text = $prcnt->sumStr($sender->Parent->Parent->tcPorcentajeAnterior->txtPorcentajeAnterior->Text, $sender->Parent->Parent->tcPorcentajeActual->txtPorcentajeActual->Text);
                $sender->Parent->Parent->tcImporteAcum->txtImporteAcum->Text = $curr->sumStr($sender->Parent->Parent->tcImporteAnterior->txtImporteAnterior->Text, $monto);
            } //>> Al "sumaMontos" hacerle un verificador automático de tipo de variables para que se pueda a un texto, sumar o bien otro texto o un número.
            
            
            $this->CalcularTotales();

            $this->cambiando = false;
        }
    }

    public function txtNumero_OnTextChanged($sender, $param) 
    {
        //$sender->Text = floatval($sender->Text) ? '222' : "nnn";
        $this->habilitacionControlesPrimerCertificado(floatval($sender->Text));
    }


    public function txtPorcentajeActual_OnTextChanged($sender, $param) 
    {
        if (!$this->cambiando)
        {
            $this->cambiando = true;

            $curr = new Curr();
            $prcnt = new Prcnt();

            $sender->Text = $prcnt->str($prcnt->num($sender->Text));

            $porcentaje = $sender->Text;
            if ($porcentaje != "") {
                $sender->Parent->Parent->tcImporteActual->txtImporteActual->Text = $curr->str($curr->num($sender->Parent->Parent->tcPrecioTotal->txtPrecioTotal->Text) * $porcentaje / 100);
                $sender->Parent->Parent->tcPorcentajeAcum->txtPorcentajeAcum->Text = $prcnt->str($sender->Parent->Parent->tcPorcentajeAnterior->Text + $porcentaje);
                $sender->Parent->Parent->tcImporteAcum->txtImporteAcum->Text = $curr->sumStr(
                    $sender->Parent->Parent->tcImporteAnterior->txtImporteAnterior->Text, 
                    $sender->Parent->Parent->tcImporteActual->txtImporteActual->Text
                );
            }
            $this->CalcularTotales();

            $this->cambiando = false;
        }
    }

    public function txtAnticipoFinancieroPorcentaje_OnTextChanged($sender, $param) 
    {
        $curr = new Curr();

        $porcentaje = $sender->Text;
        if ($porcentaje != "") {
            $this->txtAnticipoFinanciero->Text = $this->txtAnticipoOtorgadoProv->Text = $curr->str($porcentaje * $this->hdnMontoContrato->Value / 100);
            //$this->txtDescuentoAnticipoActual->Text = $curr->str($this->lblSumaPorcentajeActual->Text * ($this->txtAnticipoAcumulado->Text + $porcentaje * $this->hdnMontoContrato->Value / 100));
        }
        $this->CalcularTotales();

    }

    /*public function txtDescuentoAnticipoActual_OnTextChanged($sender, $param) 
    {
        $curr = new Curr();

        $monto = $sender->Text;
        if ($monto != "") {
            $this->txtTotalPagoMunicipio->Text = $curr->str($this->hdnSumaImporteActual->Value - $monto - $this->txtFondoReparo->Text);
            $this->txtTotalPagoProvincia->Text = $curr->str($this->hdnSumaImporteActual->Value - $monto);
        }
    }*/

    public function txtFondoReparo_OnTextChanged($sender, $param) 
    {
        $this->CalcularTotales();
    }

    public function cvMontoAvance_OnServerValidate($sender, $param) 
    {
        $curr = new Curr();
        
        $idObra = $this->Request["ido"];
        $idContrato = $this->Request["idc"];
        $id = $this->Request["id"];
        $idOrdenTrabajo = $this->ddlOrdenTrabajo->SelectedValue;

        if ($idOrdenTrabajo != "" and $idOrdenTrabajo != "0") {
            $data = $this->CreateDataSource("OrdenTrabajoPeer", "PorcentajeAvance", $idOrdenTrabajo, $id);
        } else {
            $data = $this->CreateDataSource("ContratoPeer", "PorcentajeAvance", $idContrato, $id);
        }

        $porcentaje = $data[0]['PorcentajeAvance'] + $this->txtPorcentajeAvance->Text;
        $porcentaje = number_format($porcentaje, 2);

        if (number_format($porcentaje, 2) > '100.00') {
            $param->IsValid = false;
            $this->cvMontoAvance->ErrorMessage = "No puede certificar más del 100% de la obra ($porcentaje %)";
        } else {
            $param->IsValid = true;
        }
    }

    public function CalcularTotales() 
    {
        $curr = new Curr();
        $prcnt = new Prcnt();

        $sumaPrecioTotal = $incidenciaTotal = $sumaImporteAnterior = $sumaImporteActual = $sumaImporteAcum = 0;
        $items = $this->dgDatos->Items;
        foreach ($items as $i => $item) {
            if (strpos($item->tcPrecioTotal->txtPrecioTotal->CssClass, 'padre') > 0) {
                $padre = $i;
                $items[$padre]->tcPrecioTotal->txtPrecioTotal->Text = 0;  
                $items[$padre]->tcIncidencia->txtIncidencia->Text = 0;
                $items[$padre]->tcIncidenciaPorcentaje->txtIncidenciaPorcentaje->Text = 0;
                $items[$padre]->tcImporteAnterior->txtImporteAnterior->Text = 0;
                $items[$padre]->tcPorcentajeAnterior->txtPorcentajeAnterior->Text = 0;
                $items[$padre]->tcImporteActual->txtImporteActual->Text = 0;
                $items[$padre]->tcPorcentajeActual->txtPorcentajeActual->Text = 0;
                $items[$padre]->tcImporteAcum->txtImporteAcum->Text = 0;
                $items[$padre]->tcPorcentajeAcum->txtPorcentajeAcum->Text = 0;
            } else {
                if (strpos($item->tcPrecioTotal->txtPrecioTotal->CssClass, 'hijo') > 0) {
                    $items[$padre]->tcPrecioTotal->txtPrecioTotal->Text =
                        $curr->sumStr($items[$padre]->tcPrecioTotal->txtPrecioTotal->Text, $item->tcPrecioTotal->txtPrecioTotal->Text);

                    $items[$padre]->tcIncidencia->txtIncidencia->Text =
                        floatval($items[$padre]->tcIncidencia->txtIncidencia->Text) + floatval($item->tcIncidencia->txtIncidencia->Text);
                    $items[$padre]->tcIncidenciaPorcentaje->txtIncidenciaPorcentaje->Text =
                        $prcnt->sumNum($items[$padre]->tcIncidenciaPorcentaje->txtIncidenciaPorcentaje->Text, $item->tcIncidenciaPorcentaje->txtIncidenciaPorcentaje->Text);
                    
                    $items[$padre]->tcImporteAnterior->txtImporteAnterior->Text =
                        $curr->sumStr($items[$padre]->tcImporteAnterior->txtImporteAnterior->Text, $item->tcImporteAnterior->txtImporteAnterior->Text);
                    $items[$padre]->tcImporteActual->txtImporteActual->Text =
                        $curr->sumStr($items[$padre]->tcImporteActual->txtImporteActual->Text, $item->tcImporteActual->txtImporteActual->Text);
                    $items[$padre]->tcImporteAcum->txtImporteAcum->Text =
                        $curr->sumStr($items[$padre]->tcImporteAcum->txtImporteAcum->Text, $item->tcImporteAcum->txtImporteAcum->Text);
                    
                    $items[$padre]->tcPorcentajeAnterior->txtPorcentajeAnterior->Text =
                        $prcnt->sumStr($items[$padre]->tcPorcentajeAnterior->txtPorcentajeAnterior->Text, $item->tcPorcentajeAnterior->txtPorcentajeAnterior->Text);
                    $items[$padre]->tcPorcentajeActual->txtPorcentajeActual->Text =
                        $prcnt->sumStr($items[$padre]->tcPorcentajeActual->txtPorcentajeActual->Text, $item->tcPorcentajeActual->txtPorcentajeActual->Text);
                    $items[$padre]->tcPorcentajeAcum->txtPorcentajeAcum->Text =
                        $prcnt->sumStr($items[$padre]->tcPorcentajeAcum->txtPorcentajeAcum->Text, $item->tcPorcentajeAcum->txtPorcentajeAcum->Text);
                }
                $sumaPrecioTotal += $curr->num($item->tcPrecioTotal->txtPrecioTotal->Text);
                //echo "<script>console.log('".  ($item->tcPrecioTotal->txtPrecioTotal->Text)   .  "<br>')</script>";
                //echo "<script>console.log('".  $curr->num($item->tcPrecioTotal->txtPrecioTotal->Text)   .  "<br>')</script>";
                $incidenciaTotal += $item->tcIncidencia->txtIncidencia->Text;
                $sumaImporteAnterior += $curr->num($item->tcImporteAnterior->txtImporteAnterior->Text);
                $sumaImporteActual += $curr->num($item->tcImporteActual->txtImporteActual->Text);
                $sumaImporteAcum += $curr->num($item->tcImporteAcum->txtImporteAcum->Text);
            }
        }
        $this->lblPrecioTotal->Text = $curr->str($sumaPrecioTotal);
        $this->lblIncidenciaTotal->Text = $prcnt->str($incidenciaTotal);
        $this->lblIncidenciaPorcentajeTotal->Text = $prcnt->str($incidenciaTotal * 100);
        $this->lblSumaImporteAnterior->Text = $curr->str($sumaImporteAnterior);
        $this->lblSumaPorcentajeAnterior->Text = $prcnt->str($sumaImporteAnterior / $this->hdnMontoContrato->Value * 100);
        $this->hdnSumaImporteActual->Value = $sumaImporteActual;
        $this->lblSumaImporteActual->Text = $curr->str($sumaImporteActual);
        $this->hdnSumaPorcentajeActual->Value = $sumaImporteActual / $this->hdnMontoContrato->Value * 100;
        $this->lblSumaPorcentajeActual->Text = $prcnt->str($this->hdnSumaPorcentajeActual->Value);
        $this->lblSumaImporteAcum->Text = $curr->str($sumaImporteAcum);
        $this->lblSumaPorcentajeAcum->Text = $prcnt->str($sumaImporteAcum / $this->hdnMontoContrato->Value * 100);
        
        $this->txtDescuentoAnticipoActual->Text = $curr->str($this->hdnSumaPorcentajeActual->Value 
                    * $curr->num($this->txtAnticipoOtorgadoProv->Text) / 100);

        $this->txtDescuentoAnticipoAcumulado->Text = $curr->sumStr(
            $this->txtAnticipoAcumulado->Text, 
            $this->txtDescuentoAnticipoActual->Text);
        
        $this->txtTotalPagoProvincia->Text = $curr->str(
            $sumaImporteActual 
            - $curr->num($this->txtDescuentoAnticipoActual->Text));

        $this->txtTotalPagoMunicipio->Text = $curr->str(
            $curr->num($this->txtTotalPagoProvincia->Text)
            - $curr->num($this->txtFondoReparo->Text));


        // Activa los cambios realizados del lado del cliente
        $this->cambios->Value++;
    }

    public function dtpPeriodo_OnTextChanged($sender, $param) {

        $idObra = $this->Request["ido"];
        $idContrato = $this->Request["idc"];
        $id = $this->Request["id"];

        $this->LoadDataRelatedAcum($idObra, $idContrato, $id);
    }


}

?>