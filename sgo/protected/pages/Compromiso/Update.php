<?php
class Update extends PageBaseSP{

	public function onLoad($param){
		parent::onLoad($param);

		if(!$this->IsPostBack){
			$this->LoadDataRelated();
			$id = $this->Request["id"];
			if (!is_null($id)) {
				$this->lblAccion->Text = "Modificar Compromiso";
				$this->MostrarControlesSoloLectura();				
				$this->Refresh($id);
			}			
		}		
	}

	public function MostrarControlesSoloLectura(){
		$this->dtpFecha->Enabled = false;
		$this->ddlLocalidad->Enabled = false;
		//$this->ddlResponsable->Enabled = false;
		$this->txtDenominacion->Enabled = false;
		$this->txtPlazo->Enabled = false;		
	}

	public function CompromisoCumplido(){
		$this->ddlResponsable->Enabled = false;
		$this->txtLatitud->Enabled= false;
		$this->txtLongitud->Enabled= false;
		$this->chkCerrado->Checked = true;
		$this->chkCerrado->Enabled = false;
		$this->btnAgregarRevision->Visible ="false";
		$this->btnAceptar->Visible ="false";
		$this->btnCancelar->Visible ="false";
		$this->tcEditarRevision->Visible = "false";
	}
	
	public function LoadDataRelated(){		
		
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['Nombre'] = 'asc';
		$finder = LocalidadRecord::finder();
		$localidades = $finder->findAll($criteria);
		$this->ddlLocalidad->DataSource = $localidades;
		$this->ddlLocalidad->dataBind();
		
		$responsables = $this->CreateDataSource("CompromisoResponsablePeer","CompromisoResponsableSelect");		
		$this->ddlResponsable->DataSource = $responsables;
		$this->ddlResponsable->dataBind();

	}
		
	public function Refresh($idCompromiso){
		
		$finder = CompromisoRecord::finder();
		$compromiso = $finder->findByPk($idCompromiso);

		$idUsuario = $compromiso->IdUsuario;

		if(!is_null($compromiso->Fecha)){
			$fecha = explode("-",$compromiso->Fecha);
			$this->dtpFecha->Text = $fecha[2]."/".$fecha[1]."/".$fecha[0];
		} 
		
		if ($idUsuario == 79){
			//Los compromiso creados por el usuario con Id 79, son compromisos que se importaron en la etapa inicial
			$this->lblActualizacion->Text = "Compromiso generado automaticamente en la importación de datos iniciales";		
		}
		else
		{
			$finder = UsuarioRecord::finder();
			$usuario = $finder->findByPk($idUsuario);

			$this->lblActualizacion->Text = "Compromiso creado por el usuario " . $usuario->ApellidoNombre;			
		}

		$date=date_create($compromiso->FechaRegistro);
		
		$this->lblActualizacionFecha->Text = "Ultima actualizacion del compromiso: " . date_format($date,"d/m/Y");
		$this->txtDenominacion->Text = $compromiso->Compromiso;
		$this->ddlResponsable->Text = $compromiso->IdResponsable;
		$this->ddlLocalidad->Text = $compromiso->IdLocalidad;
		$this->txtPlazo->Text = $compromiso->Plazo;
		$this->txtLatitud->Text = $compromiso->Latitud;
		$this->txtLongitud->Text = $compromiso->Longitud;

		if ($compromiso->Cerrado ==1){
			$this->CompromisoCumplido();					
		}

		$this->pnlRevision->Visible = true;
		$this->btnAgregarRevision->NavigateUrl .= "&idCompromiso=".$idCompromiso;
		$data = $this->CreateDataSource("CompromisoPeer","RevisionesDelCompromiso", $idCompromiso);
		$this->dgRevisiones->DataSource = $data;
		$this->dgRevisiones->dataBind();

		if(count($data)){
			$this->lblRevisiones->Visible = false;
		}

	}


	public function btnCancelar_OnClick($sender, $param){
		$this->Response->Redirect("?page=Compromiso.Home");
	}

	public function btnAceptar_OnClick($sender, $param)	{ 
		if($this->IsValid){
			$id = $this->Request["id"];

			if(!is_null($id)){
				$finder = CompromisoRecord::finder();
				$compromiso = $finder->findByPk($id);
			}
			else{
				$compromiso = new CompromisoRecord();				
			}
			
			if($this->dtpFecha->Text!=""){
				$fecha = explode("/", $this->dtpFecha->Text);
				$compromiso->Fecha = $fecha[2]."-".$fecha[1]."-".$fecha[0];
			}
			else{
				$compromiso->Fecha = null;
			}

			if($this->ddlLocalidad->SelectedValue!="" and $this->ddlLocalidad->SelectedValue!="0"){
				$compromiso->IdLocalidad = $this->ddlLocalidad->SelectedValue;
			}
			else{
				$compromiso->IdLocalidad = null;
			}
			$compromiso->Compromiso = $this->txtDenominacion->Text;
			
			if($this->ddlResponsable->SelectedValue!="" and $this->ddlResponsable->SelectedValue!="0"){
				$compromiso->IdResponsable = $this->ddlResponsable->SelectedValue;
			}

			$compromiso->Plazo = $this->txtPlazo->Text;
			$compromiso->Latitud = $this->txtLatitud->Text;
			$compromiso->Longitud = $this->txtLongitud->Text;

			if($this->chkCerrado->Checked){
				$compromiso->Cerrado = True;
			}
			else{
				$compromiso->Cerrado = False;
			}

			$idUsuario = $this->Session->get("usr_id");
			$finder = UsuarioRecord::finder();
			$usuario = $finder->findByPk($idUsuario);
			$compromiso->IdUsuario = $usuario->IdUsuario;						
			$compromiso->Activo = True;
			
			try{
				$compromiso->FechaRegistro = date('Y-m-d H:i:s');
				$compromiso->save();
				$this->Response->Redirect("?page=Compromiso.Home");
			}
			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}
		}
	 }

	// public function btnVerTodos_OnClick($sender, $param)
	// {
	// 	$to = "mauriciodmartinez@gmail.com";
	// 	//echo "<pre>";print_r($to); die();
	// 	$subject = "SGO";
	// 	$txt = "Hello world!";
	// 	$headers = "From: mauriciodmartinez@gmail.com" . "\r\n" .
	// 	"CC: mauriciodmartinez@gmail.com";

	// 	mail($to,$subject,$txt,$headers);
		

	// }

}

	
?>
