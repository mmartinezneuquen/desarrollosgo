<?php
class Update extends PageBaseSP{

	public function onLoad($param){
		parent::onLoad($param);
		//id='IdCertificacion
		//ido='IdObra'
		//idcontrato= IdContrato'



		if(!$this->IsPostBack){
			$idObra = $this->Request["ido"];
			$idCertificacion = $this->Request["id"];
			$idRendicionCuentas = $this->Request["idc"];
			$this->LoadDataRelated($idCertificacion);

			//$this->hlkVolver->NavigateUrl .= "&idc=$idContrato&ido=$idObra";

			if (!is_null($idCertificacion)) {
				$this->lblAccion->Text = "Rendición de Cuentas s/Aporte Nacional";
				$this->Refresh($idCertificacion);
				if (!is_null($idRendicionCuentas)) {
					$borrar = $this->Request["borrar"];
					if (!is_null($borrar)) {
						$this->borrarRendicionCuentas($idRendicionCuentas);
					}
					else{
						$this->lblAccion->Text = "Guardar Cambios";
						$this->RefreshRendicionCuentas($idRendicionCuentas);
					}
					$aprobado = $this->Request["aprobado"];
					$rechazado = $this->Request["rechazado"];

					if (!is_null($aprobado) or !is_null($rechazado)){
						$this->cambiarEstadoRendicionCuentas($idRendicionCuentas,$aprobado,$rechazado);
					}
				}
			}
		}
	}

	public function LoadDataRelated($idCertificacion){
		$idContrato = $this->Request["idcontrato"];
		$idObra = $this->Request["ido"];

		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['Nombre'] = 'asc';
		$finder = LocalidadRecord::finder();
		$localidades = $finder->findAll($criteria);
		//$this->ddlLocalidad->DataSource = $localidades;
		//$this->ddlLocalidad->dataBind();

		//$this->hlkVolver->NavigateUrl .= "&idc=$idContrato&ido=$idObra";
		$this->hlkVolver->NavigateUrl = "?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra";
		

	}

	public function Refresh($idCertificacion){

		$this->lblAccion->Text = "Rendición de Cuentas";
		$this->LimpiarCampos();

		$idLocalidad = $this->Session->get("usr_sgo_idLocalidad");

		//Si el usuario es una Localidad, Oculto los controles
		//if(!is_null($idLocalidad)){
		//		$this->tcAprobarCuenta->Visible = false;
		//		$this->tcRechazarCuenta->Visible = false;				
		//	}

		$idUsuario = $this->Session->get("usr_id");
			$finder = UsuarioRecord::finder();
			$usuario = $finder->findByPk($idUsuario);
			//$idLocalidad = $this->Session->get("usr_id");
			$idLocalidad = $usuario ->IdLocalidad;
			
			//Si el usuario es una Localidad, Oculto los controles
			if(!is_null($idLocalidad)){				
				$this->tcAprobarCuenta->Visible = false;
				$this->tcRechazarCuenta->Visible = false;				
			}


		$data = $this->CreateDataSource("RendicionCuentasPeer","RendicionesByCertificacion", $idCertificacion);
		 		$this->dgCuentas->DataSource = $data;
		 		$this->dgCuentas->dataBind();		 
		 		if(count($data)){
		 		$this->lblCuentas->Visible = false;
		 		$totalmonto = $this->CreateDataSource("RendicionCuentasPeer","TotalMontoRendicionesByCertificacion", $idCertificacion);

		 		$this->lblTotal->Text = '$' . $totalmonto[0]["monto"];
		 		
		}


		$this->ControlarCertificacionAprobada($idCertificacion);


	}

	public function RefreshRendicionCuentas($idRendicionCuentas){
		$finder = RendicionCuentasRecord::finder();
		$RendicionCuentas = $finder->findByPk($idRendicionCuentas);
		//$this->txtProyecto->Text = $RendicionCuentas->Proyecto;
		//$this->ddlLocalidad->SelectedValue = $RendicionCuentas->IdLocalidad;
		$this->txtEmpresa->Text = $RendicionCuentas->Empresa;
		$this->txtCuit->Text = $RendicionCuentas->Cuit;
		$this->txtFacturaNro->Text = $RendicionCuentas->Factura;
		$this->txtReciboNro->Text = $RendicionCuentas->Recibo;
		if(!is_null($RendicionCuentas->FechaEmision)){
			$fecha = explode("-",$RendicionCuentas->FechaEmision);
			$this->dtpFechaEmision->Text = $fecha[2]."/".$fecha[1]."/".$fecha[0];
		}
		$this->txtConcepto->Text = $RendicionCuentas->Concepto;
		if(!is_null($RendicionCuentas->FechaCancelacion)){
			$fecha = explode("-",$RendicionCuentas->FechaCancelacion);
			$this->dtpFechaCancelacion->Text = $fecha[2]."/".$fecha[1]."/".$fecha[0];
		}		
		$this->txtOrdenPago->Text = $RendicionCuentas->OrdenDePago;
		$this->txtMonto->Text = $RendicionCuentas->Monto;
		$this->txtObservacion->Text = $RendicionCuentas->Observaciones; 
		
	}

	public function LimpiarCampos(){
		//$this->txtProyecto->Text = "";
		//$this->ddlLocalidad->SelectedValue=0;
		$this->txtEmpresa->Text = "";
		$this->txtCuit->Text = "";
		$this->txtFacturaNro->Text = "";
		$this->txtReciboNro->Text = "";
		$this->dtpFechaEmision->Text = "";
		$this->txtConcepto->Text = "";
		$this->dtpFechaCancelacion->Text = "";
		$this->txtOrdenPago->Text = "";
		$this->txtMonto->Text = "";
		$this->txtObservacion->Text = "";

	}

	public function btnCancelar_OnClick($sender, $param){
		$idObra = $this->Request["ido"];
		$idContrato = $this->Request["idcontrato"];		
		$idRendicion = $this->Request["idc"];

		$this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra");
	}

	public function btnCancelarItem_OnClick($sender, $param){
		$idContrato = $this->Request["idcontrato"];
		$idObra = $this->Request["ido"];
		$idCertificacion = $this->Request["id"];

		$this->Response->Redirect("?page=Obra.Contrato.Certificacion.RendicionCuentas.Update&id=$idCertificacion&ido=$idObra&idcontrato=$idContrato");	
	}

	public function btnAceptar_OnClick($sender, $param){
		$this->Response->Redirect("?page=Obra.HomeAdmin");
	}


	public function btnAgregarRendicion_OnClick($sender, $param){
		$idCertificacion = $this->Request["id"];
		$idRendicion = $this->Request["idc"];
		$idObra = $this->Request["ido"];
		$idContrato = $this->Request["idcontrato"];	
		
		if($this->IsValid){
			if(!is_null($idCertificacion)){
				if(!is_null($idRendicion)){
					$finder = RendicionCuentasRecord::finder();
					$rendicioncuenta = $finder->findByPk($idRendicion);					
				}
				else{
					$rendicioncuenta = new RendicionCuentasRecord();
					$orden = $this->CreateDataSource("RendicionCuentasPeer", "ProximaOrderRendicionesByCertificacion", $idCertificacion);
					//echo "<pre>";print_r($orden); die();
					$rendicioncuenta->Orden = $orden[0]["proximo"];
					$rendicioncuenta->IdCertificacion = $idCertificacion;
					$rendicioncuenta->Activo = 1;
					$rendicioncuenta->Estado = 0;
				}								
				//$rendicioncuenta->Proyecto = $this->txtProyecto ->Text;
				//$rendicioncuenta->IdLocalidad = $this->ddlLocalidad ->SelectedValue;
				$rendicioncuenta->Empresa = $this->txtEmpresa ->Text;
				$rendicioncuenta->Cuit = $this->txtCuit->Text;
				$rendicioncuenta->Factura = $this->txtFacturaNro->Text;
				$rendicioncuenta->Recibo = $this->txtReciboNro->Text;

				if($this->dtpFechaEmision->Text!=""){
					$fecha = explode("/", $this->dtpFechaEmision->Text);
					$rendicioncuenta->FechaEmision = $fecha[2]."-".$fecha[1]."-".$fecha[0];
				}

				$rendicioncuenta->Concepto = $this->txtConcepto->Text;

				if($this->dtpFechaCancelacion->Text!=""){
					$fecha = explode("/", $this->dtpFechaCancelacion->Text);
					$rendicioncuenta->FechaCancelacion = $fecha[2]."-".$fecha[1]."-".$fecha[0];
				}

				$rendicioncuenta->OrdenDePago = $this->txtOrdenPago->Text;
				$rendicioncuenta->Monto = $this->txtMonto->Text;
				$rendicioncuenta->Observaciones = $this->txtObservacion->Text;
				//$rendicioncuenta->Estado = $this-> ->Text;
				//$rendicioncuenta->Revision = $this-> ->Text;			
			try{
				$rendicioncuenta->save();
				//$this->Response->Redirect("?page=Obra.Contrato.Certificacion.RendicionCuentas.Update&id=$idCertificacion");	
				$this->Response->Redirect("?page=Obra.Contrato.Certificacion.RendicionCuentas.Update&id=$idCertificacion&ido=$idObra&idcontrato=$idContrato");	
						
				}

			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}
		}
		}
	}

	public function btnAgregarRendicion2_OnClick($sender, $param){
		$finder = RendicionCuentasRecord::finder();
		$rendicioncuenta = $finder->findByPk($idRendicionCuentas);
		$rendicioncuenta->Activo = 0;
		try{
				$rendicioncuenta->save();
				$this->Refresh($idCertificacion);
				}

			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}

	}

	public function borrarRendicionCuentas($idRendicionCuentas){
		$idObra = $this->Request["ido"];
		$idContrato = $this->Request["idcontrato"];
		$idCertificacion = $this->Request["id"];

		$finder = RendicionCuentasRecord::finder();
		$rendicioncuenta = $finder->findByPk($idRendicionCuentas);
		$rendicioncuenta->Activo = 0;
		try{
				$rendicioncuenta->save();
				}

			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}
		$this->Response->Redirect("?page=Obra.Contrato.Certificacion.RendicionCuentas.Update&&id=$idCertificacion&ido=$idObra&idcontrato=$idContrato");	
	}

	public function cambiarEstadoRendicionCuentas($idRendicionCuentas,$aprobado,$rechazado){	
		$idContrato = $this->Request["idcontrato"];
		$idObra = $this->Request["ido"];
		$idCertificacion = $this->Request["id"];

		$finder = RendicionCuentasRecord::finder();
		$rendicioncuenta = $finder->findByPk($idRendicionCuentas);

		if ($aprobado == true) {
			$rendicioncuenta->Estado = 1;
			//Estado 1 = aprobado
		}
		if ($rechazado == true){
			//Estado 2 = rechazado
			$rendicioncuenta->Estado = 2;
		}
		try{
				$rendicioncuenta->save();
			}

			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}			

		//$this->hlkVolver->NavigateUrl .="page=Obra.Contrato.Certificacion.HomeAdmin&idcontrato=$idContrato&ido=$idObra";
		$this->Response->Redirect("?page=Obra.Contrato.Certificacion.RendicionCuentas.Update&id=$idCertificacion&ido=$idObra&idcontrato=$idContrato");	
		//$this->Response->Redirect("?page=Obra.Contrato.Certificacion.HomeAdmin&idc=$idContrato&ido=$idObra");

	}

	public function ControlarCertificacionAprobada($idCertificacion){
		//Si la certificacion esta aprobada, debe esconder todos los controles de edicion

		$finder = CertificacionRecord::finder();
		$certificacion = $finder->findByPk($idCertificacion);


		if ($certificacion->Aprobada == 1){	
			$this->btnCancelar->Display = "None"; // Cancelar 
			$this->btnAgregarRendicion->Display = "None"; //Agregar Rendicion
			$this->btnCancelarItem->Display = "None"; // Cancelar Rendicion
			$this->lblGuardarCuentas->Text ="";
			$this->lblCancelarCambios->Text ="";
			
			$this->tcEditarCuenta->Visible="false";// Editar Rendicion
			$this->tcBorrarCuenta->Visible="false"; // Borrar Rendicion
			$this->tcAprobarCuenta->Visible="false"; // Aprobar Rendicion
			$this->tcRechazarCuenta->Visible="false"; // Rechazar Rendicion
		}
	}
	
}
?>
