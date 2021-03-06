<?php
/**
 * TModule class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2013 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TModule.php 3245 2013-01-07 20:23:32Z ctrlaltca $
 * @package System
 */

/**
 * TModule class.
 *
 * TModule implements the basic methods required by IModule and may be
 * used as the basic class for application modules.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: TModule.php 3245 2013-01-07 20:23:32Z ctrlaltca $
 * @package System
 * @since 3.0
 */
abstract class TModule extends TApplicationComponent implements IModule
{
	/**
	 * @var string module id
	 */
	private $_id;

	/**
	 * Initializes the module.
	 * This method is required by IModule and is invoked by application.
	 * @param TXmlElement module configuration
	 */
	public function init($config)
	{
	}

	/**
	 * @return string id of this module
	 */
	public function getID()
	{
		return $this->_id;
	}

	/**
	 * @param string id of this module
	 */
	public function setID($value)
	{
		$this->_id=$value;
	}
}

