<?php

class Documentacion extends PageBaseSP {

    public $Municipio;
    public $MunicipioAdmin;

    public function onLoad($param) 
    {
        parent::onLoad($param);

        if (!$this->IsPostBack) 
        {
            $this->Municipio = in_array("10", $this->Session->get("usr_roles")); //>>!! Diseñar un sistema que no use los números de roles!!
            $this->MunicipioAdmin = in_array("11", $this->Session->get("usr_roles")); //>>!! Diseñar un sistema que no use los números de roles!!

            $id = $this->Request["id"];
            $idObra = $this->Request["ido"];
            $idContrato = $this->Request["idc"];

            //if (!is_null($id)) {
                //$this->lblAccion->Text = "Modificar Certificación";
                //$this->Refresh($id, $idObra, $idContrato);
            //} else {
                //$this->SugerirNumeroCertificado($idContrato, 0);
            //}
            
            $this->LoadDataRelated($idObra, $idContrato, $id);
            $this->cambios->Value = "0";

            $items = $this->dgDatos->Items;
            foreach ($items as $i => $item) 
            {
                if ($this->Municipio) $item->tcBotones->pnlAprobar->Visible = "false";
                //if ($this->MunicipioAdmin) $item->tcBotones->pnlSubir->Visible = "false";
            }

        }
    }

    public function LoadDataRelated($idObra, $idContrato, $id) 
    {
        $documentación = $this->CreateDataSource("CertificacionPeer", "getDocumentacion", $id, $idContrato);
        $this->dgDatos->DataSource = $documentación;
        $this->dgDatos->dataBind();

        foreach ($this->dgDatos->Items as $it)
        {
            //$it->tcBotones->hdnCurrentDocumentFile->Value = ;
        }

        /*

        $obra = ObraRecord::finder()->findByPk($idObra);
        $organismo = OrganismoRecord::finder()->findByPk($obra->IdOrganismo);
        $localidades = $this->CreateDataSource("ObraPeer", "LocalidadesPorObra", $idObra);
        $this->lblObra->Text = $organismo->PrefijoCodigo . '-' . $obra->Codigo . ' ' . $obra->Denominacion;
        $this->lblLocalidades->Text = $localidades[0]["Localidades"];

        $contrato = ContratoRecord::finder()->findByPk($idContrato);
        $proveedor = ProveedorRecord::finder()->findByPk($contrato->IdProveedor);
        $periodo = preg_replace('/(\d+)\/(\d+)/','$2$1', $this->dtpPeriodo->Text);
        $certificacionAnterior = $this->CreateDataSource("CertificacionPeer", "getCertificacionAnterior", $idContrato, $periodo);
        $contratoItems = $this->CreateDataSource("ContratoPeer", "ItemsByContratoCertificacion", $idContrato, $periodo, $id);
        //echo "<pre>";print_r($contratoItems);die;
        $this->dgDatos->DataSource = $contratoItems;
        $this->dgDatos->dataBind();
        $this->lblDecreto->Text = $contrato->Decreto;
        $this->lblContratista->Text = $proveedor->RazonSocial;
        $this->lblMontoProvincia->Text = $curr->num($contrato->MontoProvincia);
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
        $this->txtImporteCertifAnterior->Text = $curr->num($certificacionAnterior[0]["ImporteCertifAnterior"]);
        $this->txtPorcentajeCertifAnterior->Text = $prcnt->str($certificacionAnterior[0]["ImporteCertifAnterior"] / $contrato->Monto * 100);
        if ($certificacionAnterior[0]["FechaUltimoPago"] != null) {
            $fechaUltimoPago = explode("-", $certificacionAnterior[0]["FechaUltimoPago"]);
            $fechaUltimoPago = $fechaUltimoPago[2] . "/" . $fechaUltimoPago[1] . "/" . $fechaUltimoPago[0];
        } else {
            $fechaUltimoPago = "-";
        }
        $this->lblFechaUltimoPago->Text = $fechaUltimoPago;

        $this->hlkVolver->NavigateUrl .= "&idc=$idContrato&ido=$idObra";

        $this->CalcularTotales();
        
//            $this->txtAnticipoOtorgadoProv->Text=number_format(0,2,",",".");
//            $this->txtFondoReparo->Text=number_format(0,2,",",".");

        $this->txtAnticipoAcumulado->Text = $curr->str($certificacionAnterior[0]["AnticipoFinancieroAnterior"]);
        $this->txtDescuentoAnticipoActual->Text = $curr->str($this->lblSumaPorcentajeActual->Text * $curr->sumNum($this->txtAnticipoAcumulado->Text, $this->txtAnticipoFinanciero->Text));
        $this->txtDescuentoAnticipoAcumulado->Text = $curr->sumStr($certificacionAnterior[0]["DescuentoAnticipoAnterior"], $this->txtDescuentoAnticipoActual->Text);
        $this->txtTotalPagoMunicipio->Text = 
            $curr->str($this->hdnSumaImporteActual->Value 
            - (
                $this->lblSumaPorcentajeActual->Text 
                * ($curr->str($this->txtAnticipoAcumulado->Text) + $curr->str($this->txtAnticipoFinanciero->Text))
            ) - $this->txtFondoReparo->Text);
        $this->txtTotalPagoProvincia->Text = 
            $curr->str($this->hdnSumaImporteActual->Value 
            - (
                $this->lblSumaPorcentajeActual->Text 
                * ($curr->str($this->txtAnticipoAcumulado->Text) + $curr->str($this->txtAnticipoFinanciero->Text))
            ));
        $criteria = new TActiveRecordCriteria;
        $criteria->OrdersBy['Descripcion'] = 'asc';*/
    }

    /* DESACTIVADO */


    public function Refresh($idCertificacion, $idObra, $idContrato) 
    {
        /*$curr = new Curr();
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
        $this->dtpPeriodo->Enabled = false;
        $this->txtNumero->Text = $certificacion["NroCertificado"];
        $periodo = substr($certificacion["Periodo"], 4, 2) . "/" . substr($certificacion["Periodo"], 0, 4);
        $this->dtpPeriodo->Text = $periodo;
        $this->txtAnticipoFinanciero->Text = $curr->str($certificacion["AnticipoFinanciero"]);
        $this->txtAnticipoOtorgadoProv->Text = $curr->str($certificacion["AnticipoFinanciero"]);
        $this->txtFondoReparo->Text = $certificacion["RetencionFondoReparo"];

        $this->txtAnticipoFinancieroPorcentaje->Text = $prcnt->str($certificacion["AnticipoFinanciero"] / $contrato->Monto * 100);
        if ($certificacion["FechaPagoAnticipo"] != 0) {
            $fechaAnticipo = explode("-", $certificacion["FechaPagoAnticipo"]);
            $this->lblFechaPagoAnticipoFinanciero->Text = $fechaAnticipo[2] . "/" . $fechaAnticipo[1] . "/" . $fechaAnticipo[0];
        } else {
            $this->lblFechaPagoAnticipoFinanciero->Text = null;
        }

        $this->txtPorcentajeAvanceReal->Text = $prcnt->str($certificacion["PorcentajeAvanceReal"]);
        if ($certificacion["FechaMedicion"] != null) {
            $fechaMedicion = explode("-", $certificacion["FechaMedicion"]);
            $this->dtpFechaMedicion->Text = $fechaMedicion[2] . "/" . $fechaMedicion[1] . "/" . $fechaMedicion[0];
        } else {
            $this->dtpFechaMedicion->Text = null;
        }*/
    }

    public function btnCancelar_OnClick($sender, $param) 
    {
        $ido = $this->Request["ido"];
        $idc = $this->Request["idc"];
        $this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idc&ido=$ido");
    }

    protected function limpiar_archivosRechazados() {

    }

    public function btnAceptar_OnClick($sender, $param) 
    {
        $id = $this->Request["id"];
        $idObra = $this->Request["ido"];
        $idContrato = $this->Request["idc"];

        $serverRootPath = $this->DocumentRoot;
        $storePath = $serverRootPath."/output/documentos";
        $storeTmpPath = $serverRootPath."/output/documentos/tmp";
        
                echo "<pre>";
        foreach ($this->dgDatos->Items as $it) 
        {
            // Subida de archivos
            $uploadedFile = $it->tcBotones->hdnDocumentFile->Value;
            if ($uploadedFile) rename("$storeTmpPath/$uploadedFile", "$storePath/$uploadedFile");

            $rejected = explode(';', $it->tcBotones->hdnRejectedDocumentFiles->Value);
            if (sizeof($rejected) &&  $rejected[0])
            {
                foreach ($rejected as $rejectedFile) @unlink("$storeTmpPath/$rejectedFile");
            }

            //guardar si hay cambio => si subio archivo, o si se aprobo o desaprobo

            if ($uploadedFile || $it->tcBotones->hdnAprobado->Value)
            {
                $revision = new DocumentoRevisionRecord();

                $revision->IdDocumento = $it->tcBotones->hdnIdDocumento->Value;
                $revision->IdCertificacion = $id;
                $revision->Fecha = date('Y-m-d h:i:s');
                if ($uploadedFile) $revision->IdEstado = 2;
                if ($it->tcBotones->hdnAprobado->Value == "aprobado") $revision->IdEstado = 3;
                if ($it->tcBotones->hdnAprobado->Value == "desaprobado") $revision->IdEstado = 4;
                $revision->Revision = 0;
                $revision->Motivo = $revision->IdEstado == 4 ? $it->tcBotones->tbMotivo->Text : "";
                $revision->IdUsuario = $this->Session->get("usr_id");
                $revision->Archivo = $uploadedFile ? $uploadedFile : $it->tcBotones->hdnCurrentDocumentFile->Value;

                try {
                    $revision->save();
                }
                catch(exception $e) {
                    $this->Log($e->getMessage(),true);
                }

            }

        }
        $this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra");

die();
        
        
        /*$curr = new Curr();
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

            if ($this->txtNumero->Text != "") {
                $certificacion->NroCertificado = $this->txtNumero->Text;
            } else {
                $certificacion->NroCertificado = null;
            }

            $periodo = explode("/", $this->dtpPeriodo->Text);
            $certificacion->Periodo = $periodo[1] . $periodo[0];
            $certificacion->PorcentajeAvance = floatval($this->lblSumaPorcentajeActual->Text);
            $certificacion->MontoAvance = $this->hdnSumaImporteActual->Value;
            $certificacion->DescuentoAnticipo = floatval($this->txtDescuentoAnticipoActual->Text);
            $certificacion->RetencionFondoReparo = floatval($this->txtFondoReparo->Text);
            if ($this->txtAnticipoFinanciero->Text != "0,00") {
                $certificacion->AnticipoFinanciero = $this->txtAnticipoFinanciero->Text;
            } else {
                $certificacion->AnticipoFinanciero = null;
            }
            if ($this->txtPorcentajeAvanceReal->Text != "") {
                $certificacion->PorcentajeAvanceReal = $prcnt->num($this->txtPorcentajeAvanceReal->Text);
            } else {
                $certificacion->PorcentajeAvanceReal = null;
            }
            if ($this->dtpFechaMedicion->Text != "") {
                $fecha = explode("/", $this->dtpFechaMedicion->Text);
                $certificacion->FechaMedicion = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];
            } else {
                $certificacion->FechaMedicion = null;
            }
            $certificacion->TipoCertificado = 0;
            $certificacion->ImporteNeto = $this->hdnSumaImporteActual->Value + floatval($this->txtAnticipoFinanciero->Text) - floatval($this->txtDescuentoAnticipoActual->Text) - floatval($this->txtFondoReparo->Text);

            try {
                
                $certificacion->save();
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
                        $certificacionItem->PorcentajeActual = floatval($it->tcPorcentajeActual->txtPorcentajeActual->Text);
                        $certificacionItem->ImporteActual = $curr->num($it->tcImporteActual->txtImporteActual->Text);
                        
                        //print_r($certificacionItem->PorcentajeActual ."<br>");
                        $certificacionItem->save();
                    }
                }

                $this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra");
            } catch (exception $e) {
                $this->Log($e->getMessage(), true);
            }
        }*/
    }

    //public function txtImporteActual_OnTextChanged($sender, $param) 
    

    public function cvMontoAvance_OnServerValidate($sender, $param) 
    {
        /*$curr = new Curr();
        
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
        }*/
    }

}

?>