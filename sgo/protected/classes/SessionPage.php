<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2012
 */
class SessionPage extends TPage{

	public $Session;
	public $GlobalPath;
	public $BasePath;
	public $DocumentRoot;

	public function onLoad($param)
	{
		parent::onLoad($param);

		$srv = $_SERVER["SERVER_NAME"];
		$this->GlobalPath = "http://" . ($srv == "localhost" ? $srv . preg_replace('/^\/([\w\d]+)(\/[\w\W]*)/', "/$1", $_SERVER["REQUEST_URI"]) : $srv);
		$this->BasePath = $this->GlobalPath.'/sgo';
		$this->DocumentRoot = ($srv == "localhost" ? 
			$_SERVER["DOCUMENT_ROOT"] . preg_replace('/^\/([\w\d]+)(\/[\w\W]*)/', "/$1", $_SERVER["REQUEST_URI"]) : 
			$_SERVER["DOCUMENT_ROOT"]).'/sgo';
		$this->Session = new SessionSGO();
		

	}

}
