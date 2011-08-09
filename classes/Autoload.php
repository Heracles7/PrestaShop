<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Autoload
{
	/**
	 * File where classes index is stored
	 */
	const INDEX_FILE = 'cache/class_index.php';

	/**
	 * @var Autoload
	 */
	protected static $instance;

	/**
	 * @var string Root directory
	 */
	protected $root_dir;

	/**
	 *  @var array array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
	 */
	public $index = array();

	protected function __construct()
	{
		$this->root_dir = dirname(dirname(__FILE__)).'/';
		if (file_exists($this->root_dir.Autoload::INDEX_FILE))
			$this->index = include_once($this->root_dir.Autoload::INDEX_FILE);
	}

	/**
	 * Get instance of autoload (singleton)
	 *
	 * @return Autoload
	 */
	public static function getInstance()
	{
		if (!Autoload::$instance)
			Autoload::$instance = new Autoload();

		return Autoload::$instance;
	}

	/**
	 * Retrieve informations about a class in classes index and load it
	 *
	 * @param string $classname
	 */
	public function load($classname)
	{
		// regenerate the class index if the requested class is not found in the index or if the requested file doesn't exists
		if (!isset($this->index[$classname]) || ($this->index[$classname] && !file_exists($this->root_dir.$this->index[$classname])))
			$this->generateIndex();

		// If $classname has not core suffix (E.g. Shop, Product)
		if (substr($classname, -4) != 'Core')
		{
			// If requested class does not exist, load associated core class
		 	if (isset($this->index[$classname]) && !$this->index[$classname])
		 	{
				require_once($this->root_dir.$this->index[$classname.'Core']);
		 		if (file_exists($this->root_dir.'override/'.$this->index[$classname.'Core']))
		 		{
		 			$this->generateIndex();
		 			require_once($this->root_dir.$this->index[$classname]);
		 		}
		 		else
		 		{
					// Since the classname does not exists (we only have a classCore class), we have to emulate the declaration of this class
					$class_infos = new ReflectionClass($classname.'Core');
					eval(($class_infos->isAbstract() ? 'abstract ' : '').'class '.$classname.' extends '.$classname.'Core {}');
				}
			}
			else
			{
				// request a non Core Class load the associated Core class if exists
				if (isset($this->index[$classname.'Core']))
					require_once($this->root_dir.$this->index[$classname.'Core']);

				require_once($this->root_dir.$this->index[$classname]);
			}
		}
		// Call directly ProductCore, ShopCore class
		else
			require_once($this->root_dir.$this->index[$classname]);

		if (!class_exists($classname, false) && !interface_exists($classname, false))
			throw new Exception('Class not found: '.$classname);
	}

	/**
	 * Generate classes index
	 */
	public function generateIndex()
	{
		echo 'oui';
		$classes = array_merge(
							$this->getClassesFromDir('classes/', true),
							$this->getClassesFromDir('override/classes/', false)
						);
		$content = '<?php return '.var_export($classes, true).';';

		// Write classes index on disc to cache it
		$filename = $this->root_dir.Autoload::INDEX_FILE;
		if ((file_exists($filename) && is_writable($filename)) || is_writable(dirname($filename)))
			file_put_contents($filename, $content);
		else
			throw new Exception($filename.' is not writable!');

		$this->index = $classes;
	}

	/**
	 * Retrieve recursively all classes in a directory and its subdirectories
	 *
	 * @param string $path Relativ path from root to the directory
	 * @return array
	 */
	protected function getClassesFromDir($path)
	{
		$classes = array();

		foreach (scandir($this->root_dir.$path) as $file)
		{
			if ($file[0] != '.')
			{
				if (is_dir($this->root_dir.$path.$file))
					$classes = array_merge($classes, $this->getClassesFromDir($path.$file.'/'));
				else if (substr($file, -4) == '.php')
			 	{
			 		$content = file_get_contents($this->root_dir.$path.$file);
			 		if (preg_match('#\W((abstract\s+)?class|interface)\s+(?P<classname>'.basename($file, '.php').'(Core)?)(\s+(extends|implements)\s+[a-z][a-z0-9_]*)?\s*\{#i', $content, $m))
			 		{
			 			$classes[$m['classname']] = $path.$file;
						if (substr($m['classname'], -4) == 'Core')
							$classes[substr($m['classname'], 0, -4)] = '';
			 		}
				}
			}
		}

		return $classes;
	}
}

