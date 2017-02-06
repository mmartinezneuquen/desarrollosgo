<?php
class Update extends PageBaseSP{

	public $roles;

	public function onLoad($param)
	{
		parent::onLoad($param);

		if(!$this->IsPostBack){
			$this->LoadDataRelated();
			$id = $this->Request["id"];

			if (!is_null($id)) {
				$this->lblAccion->Text = "Modificar Usuario";
				$this->Refresh($id);
			}

			$dataRoles =  $this->CreateDataSource('RolPeer','Roles');

            $this->CheckBoxListRoles->DataSource = $dataRoles;
            $this->CheckBoxListRoles->dataBind();

			$this->roles = array_map(function($elem){
				return $elem['IdRol'];
			}, $this->CreateDataSource('RolPeer','RolesUsuario',$id));

			$this->hdnRolesResult->setValue(implode(',', $this->roles));

		}

	}

	public function LoadDataRelated()
	{
		$idUsuario = $this->Session->get("usr_id");
		$roles = $this->Session->get("usr_roles");


		$finder = UsuarioRecord::finder();
		$usuario = $finder->findByPk($idUsuario);
		
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['Nombre'] = 'asc';
		$finder = OrganismoRecord::finder();
		$criteria2 = new TActiveRecordCriteria;
		$criteria2->OrdersBy['Nombre'] = 'asc';

		if(!in_array(1, $roles)){
			$criteria->Condition = 'IdOrganismo = :idorganismo ';
			$criteria->Parameters[':idorganismo'] = $usuario->IdOrganismo;
			$criteria2->Condition = 'IdRol not in (1, 6)';
		}
		else{
			$this->ddlOrganismo->PromptValue="0";
			$this->ddlOrganismo->PromptText="Seleccione";
		}

		$organismos = $finder->findAll($criteria);
		$this->ddlOrganismo->DataSource = $organismos;
		$this->ddlOrganismo->dataBind();

		$finder = RolRecord::finder();
		$roles = $finder->findAll($criteria2);
		//$this->ddlRol->DataSource = $idRol;
		//$this->ddlRol->dataBind();
	}



	public function Refresh($idUsuario)
	{
		$idUsuarioAdmin = $this->Session->get("usr_id");
		$roles = $this->Session->get("usr_roles");

		$finder = UsuarioRecord::finder();
		$usuario = $finder->findByPk($idUsuario);
		$usuarioAdmin = $finder->findByPk($idUsuarioAdmin);

		if(!in_array(1, $roles)){

			if($usuarioAdmin->IdOrganismo!=$usuario->IdOrganismo){
				$this->Response->Redirect("?page=Usuario.Home");
			}

		}

		$this->ddlOrganismo->SelectedValue = $usuario->IdOrganismo;
		$this->txtApellidoNombre->Text = $usuario->ApellidoNombre;
		$this->txtUsername->Text = $usuario->Username;
		$this->txtEmail->Text = $usuario->Email;
		//$this->ddlRol->SelectedValue = $usuario->IdRol;
		$this->chkActivo->Checked = $usuario->Activo;
		$this->txtPassword->Visible = false;
		$this->labelContrasena->Visible = true;
		$this->requiredContrasena->Enabled = false;
	}

	public function cvUsername_OnServerValidate($sender, $param)
	{
		$username = $this->txtUsername->Text;
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = 'Username like :username ';
		$criteria->Parameters[':username'] = $username;
		$id = $this->Request["id"];

		if(!is_null($id)){
			$criteria->Condition .=  ' AND IdUsuario <> :username';
			$criteria->Parameters[':username'] = $id;
		}

		$finder = UsuarioRecord::finder();
		$usuario = $finder->find($criteria);

		if (is_object($usuario)) {
			$param->IsValid = false;
		}
		else {
			$param->IsValid = true;
		}

	}
	public function cvEmail_OnServerValidate($sender, $param)
	{
		$email = $this->txtEmail->Text;
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = 'Email like :email ';
		$criteria->Parameters[':email'] = $email;
		$id = $this->Request["id"];

		if(!is_null($id)){
			$criteria->Condition .=  ' AND IdUsuario <> :username';
			$criteria->Parameters[':username'] = $id;
		}

		$finder = UsuarioRecord::finder();
		$usuario = $finder->find($criteria);

		if (is_object($usuario)) {
			$param->IsValid = false;
		}
		else {
			$param->IsValid = true;
		}

	}	

	public function btnCancelar_OnClick($sender, $param)
	{
		$this->Response->Redirect("?page=Admin.Usuario.Home");
	}

	public function btnAceptar_OnClick($sender, $param)
	{

		if($this->IsValid){
			$id = $this->Request["id"];

			if(!is_null($id)){
				$finder = UsuarioRecord::finder();
				$usuario = $finder->findByPk($id);
			}
			else{
				$usuario = new UsuarioRecord();
				$usuario->IdPlanilla = 0;
				$usuario->Email = "";

				//!! Todo esto VUELA cuando esté terminado lo de Roles múltiples
				$usuario->Sgo = 0;
				$usuario->Tablero = 0;
				$usuario->Geo = 0;
				$usuario->GeoCompromisos = 0;
				$usuario->Compromisos = 0;
				$usuario->CertificacionMunicipio = 0;
				$usuario->Calendario = 0;
				$usuario->TableroUnificado = 0;
				////////

				$usuario->Password= md5($this->txtPassword->Text);
			}

			$usuario->ApellidoNombre = mb_strtoupper($this->txtApellidoNombre->Text, 'utf-8');
			$usuario->Username = $this->txtUsername->Text;
			$usuario->Email = $this->txtEmail->Text;

			if($this->ddlOrganismo->SelectedValue != "" and $this->ddlOrganismo->SelectedValue != "0"){
				$usuario->IdOrganismo = $this->ddlOrganismo->SelectedValue;
			}
			else{
				$usuario->IdOrganismo = NULL;
			}

			$usuario->Activo = $this->chkActivo->Checked ? 1 : 0;

			$usuario->IdRol = 6; //>> Numero arbitrario de prueba (todavia no se quita por problemas del ActiveRecord)

			//>> Rol Simple: esta vuela a futuro
			//$usuario->IdRol = $this->ddlRol->SelectedValue;


			//$usuario->IdLocalidad = 1;
			//$usuario->Email = 'eee';
			//F::pd($usuario);
			//F::pd($usuario->save());

			try{
				$usuario->save();

				if(is_null($id)) 
					$id = $this->CreateDataSource('UsuarioPeer','LastId')[0]['IdUsuario'];

				// Roles múltiples:
				$roles = explode(',', $this->hdnRolesResult->getValue());
				// Borra los existentes y los crea nuevamente
				UsuarioRolRecord::finder()->deleteAll('IdUsuario = ?', $id);
				foreach ($roles as $rol) {
					$usuario_rol = new UsuarioRolRecord(); // Lo vuelve a crear por cada ciclo para que no intente un Update
					$usuario_rol->IdUsuario = $id;
					$usuario_rol->IdRol = $rol;
					$usuario_rol->save();
				}

				$this->Response->Redirect("?page=Admin.Usuario.Home");
			}
			catch(exception $e){
				$this->Log($e->getMessage(),true);
			}

		}

	}

}

?>