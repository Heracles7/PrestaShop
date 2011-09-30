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

/**
 * @since 1.5.0
 */
abstract class ControllerCore
{
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var array list of css files
	 */
	public $css_files = array();

	/**
	 * @var array list of javascript files
	 */
	public $js_files = array();

	/**
	 * @var bool check if header will be displayed
	 */
	protected $display_header;

	/**
	 * @var string template name for page content
	 */
	protected $template;

	/**
	 * @var string check if footer will be displayed
	 */
	protected $display_footer;

	/**
	 * @var bool If ajax parameter is detected in request, set this flag to true
	 */
	protected $ajax = false;

	/**
	 * Initialize the page
	 */
	abstract public function init();

	/**
	 * Do the page treatment : post process, ajax process, etc.
	 */
	abstract public function postProcess();

	/**
	 * Display page view
	 */
	abstract public function display();

	/**
	 * Set default media list for controller
	 */
	abstract public function setMedia();

	/**
	 * Get an instance of a controller
	 *
	 * @param string $class_name
	 * @param bool $auth
	 * @param bool $ssl
	 */
	public static function getController($class_name, $auth = false, $ssl = false)
	{
		return new $class_name($auth, $ssl);
	}

	public function __construct()
	{
		if (is_null($this->display_header))
			$this->display_header = true;

		if (is_null($this->display_footer))
			$this->display_footer = true;

		$this->context = Context::getContext();
		$this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');
	}

	/**
	 * Start controller process (this method shouldn't be overriden !)
	 */
	public function run()
	{
		$this->init();

		if ($this->ajax && method_exists($this, 'ajaxProcess'))
			$this->ajaxProcess();
		else
			$this->postProcess();

		if ($this->display_header)
		{
			$this->setMedia();
			$this->initHeader();
		}

		$this->initContent();
		if ($this->display_footer)
			$this->initFooter();

		if ($this->ajax && method_exists($this, 'displayAjax'))
			$this->displayAjax();
		else
			$this->display();
	}

	public function displayHeader($display = true)
	{
		$this->display_header = $display;
	}

	public function displayFooter($display = true)
	{
		$this->display_footer = $display;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * Assign smarty variables for the page header
	 */
	abstract public function initHeader();

	/**
	 * Assign smarty variables for the page main content
	 */
	abstract public function initContent();

	/**
	 * Assign smarty variables for the page footer
	 */
	abstract public function initFooter();

	/**
	 * Add a new stylesheet in page header.
	 *
	 * @param mixed $css_uri Path to css file, or list of css files like this : array(array(uri => media_type), ...)
	 * @param string $css_media_type
	 * @return true
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		if (is_array($css_uri))
			foreach($css_uri as $css_file => $media)
			{
				if (is_string($css_file))
				{
					if ($css_path = Media::getCSSPath($css_file, $media))
							$this->css_files = array_merge($css_path, $this->css_files);
				}
				else if ($css_path = Media::getCSSPath($media, $css_media_type))
					$this->css_files = array_merge($css_path, $this->css_files);
			}

		else if ($css_path = Media::getCSSPath($css_uri, $css_media_type))
			$this->css_files = array_merge($css_path, $this->css_files);
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		if (is_array($js_uri))
			foreach($js_uri as $js_file)
				if ($js_path = Media::getJSPath($js_file))
					$this->js_files[] = $js_path;
		else if ($js_path = Media::getJSPath($js_uri))
			$this->js_files[] = $js_path;
	}
	
	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJquery($version = null, $folder = null, $minifier = true)
	{
		$this->addJS(Media::getJqueryPath($version, $folder, $minifier));
	}
	
	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJqueryUI($component)
	{
		if (is_array($component))
			foreach($component as $ui)
				$ui_path = Media::getJqueryUIPath($ui);
		else
			$ui_path = Media::getJqueryUIPath($component);
		$this->addJS($ui_path);
	}
	
	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJqueryPlugin($name)
	{
		$plugin_path = array();
		if (is_array($name))
		{
			foreach($name as $plugin)
			{
				$plugin_path = Media::getJqueryPluginPath($plugin);
				$this->addJS($plugin_path['js']);
				$this->addCSS($plugin_path['css']);				
			}
		}
		else
			$plugin_path = Media::getJqueryPluginPath($name);
		
		$this->addCSS($plugin_path['css']);
		$this->addJS($plugin_path['js']);
	}
}
