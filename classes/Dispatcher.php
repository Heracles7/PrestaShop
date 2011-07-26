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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class DispatcherCore
{
	/**
	 * @var Dispatcher
	 */
	public static $instance = null;

	/**
	 * @var array
	 */
	protected $defaultRoutes = array(
		'product_rule' => array(
			'controller' =>	'product',
			'rule' =>		'{number:id_product}-{text}.html',
		),
		'category_rule' => array(
			'controller' =>	'category',
			'rule' =>		'{number:id_category}-{text}',
		),
		'product_rule2' => array(
			'controller' =>	'product',
			'rule' =>		'{text1}/{number:id_product}-{text2}.html',
		),
		'supplier_rule' => array(
			'controller' =>	'supplier',
			'rule' =>		'{number:id_supplier}__{text}',
		),
		'manufacturer_rule' => array(
			'controller' =>	'manufacturer',
			'rule' =>		'{number:id_manufacturer}_{text}',
		),
		'cms_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/{number:id_cms}-{text}',
		),
		'cms_category_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/category/{number:id_cms_category}-{text}',
		),
	);

	/**
	 * @var $useRoutes bool
	 */
	protected $useRoutes = false;

	/**
	 * @var $routes array
	 */
	protected $routes = array();

	/**
	 * @var array
	 */
	protected $keywords = array(
		'number' =>	'[0-9]+',
		'text' =>	'[a-zA-Z0-9-]*',
	);

	/**
	 * @var string
	 */
	protected $controller;

	public static $controllers = array();

	/**
	 * Get current instance of dispatcher (singleton)
	 * 
	 * @return Dispatcher
	 */
	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new Dispatcher();
		return self::$instance;
	}

	/**
	 * Need to be instancied from getInstance() method
	 */
	protected function __construct()
	{
		$this->useRoutes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
		$this->loadRoutes();
	}

	/**
	 * "main" method of dispatcher, call the controller
	 */
	public function dispatch()
	{
		$this->getController();

		$controllers = Dispatcher::getControllers();
		if (!isset($controllers[$this->controller]))
			$this->controller = 'index';
		ControllerFactory::getController($controllers[$this->controller])->run();
	}
	
	/**
	 * Load default routes
	 */
	protected function loadRoutes()
	{
		$context = Context::getContext();
		foreach ($this->defaultRoutes as $id => $route)
			$this->addRoute($id, $route['rule'], $route['controller']);

		// Load routes from meta table
		if ($this->useRoutes)
		{
			$sql = 'SELECT m.page, ml.url_rewrite
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta'.$context->shop->sqlLang('ml').')
					WHERE id_lang = '.(int)$context->language->id;
			if ($results = Db::getInstance()->ExecuteS($sql))
				foreach ($results as $row)
					$this->addRoute($row['page'], $row['url_rewrite'], $row['page']);
		}
	}
	
	/**
	 * 
	 * @param string $id Name of the route (need to be uniq, a second route with same name will override the first)
	 * @param string $rule Url rule
	 * @param string $controller Controller to call if request uri match the rule
	 */
	public function addRoute($routeID, $rule, $controller)
	{	
		$regexp = preg_quote($rule, '#');
		$required = array();
		preg_match_all('#\\\{('.implode('|', array_keys($this->keywords)).')[0-9]*(\\\:([a-z0-9_]+))?\\\}#', $regexp, $m);
		for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			if ($m[3][$i])
			{
				$regexp = str_replace($m[0][$i], '(?P<'.$m[3][$i].'>'.$this->keywords[$m[1][$i]].')', $regexp);
				$required[$m[3][$i]] = $m[1][$i];
			}
			else
				$regexp = str_replace($m[0][$i], '('.$this->keywords[$m[1][$i]].')', $regexp);

		$regexp = '#^/'.$regexp.'#';
		$this->routes[$routeID] = array(
			'rule' =>		$rule,
			'regexp' =>		$regexp,
			'controller' =>	$controller,
			'required' =>	$required,
		);
	}
	
	/**
	 * Check if a route is defined
	 * 
	 * @param string $routeID
	 * @return bool
	 */
	public function routeExists($routeID)
	{
		return isset($this->routes[$routeID]);
	}

	/**
	 * Create an url from
	 * 
	 * @param string $routeID Name the route
	 * @param array $params
	 * @param bool $useRoutes If false, don't use to create this url
	 */
	public function createUrl($routeID, $params = array(), $useRoutes = true)
	{
		if (!is_array($params))
			die('Dispatcher::createUrl() $params must be an array');

		if (!isset($this->routes[$routeID]))
		{
			$query = http_build_query($params);
			return 'index.php?controller='.$routeID.(($query) ? '&'.$query : '');
		}
		$route = $this->routes[$routeID];
		
		// Check required fields
		$queryParams = array();
		foreach (array_keys($route['required']) as $key)
		{
			if (!array_key_exists($key, $params))
				die("Dispatcher::createUrl() miss required parameter '$key'");
			$queryParams[$key] = $params[$key];
		}

		// Build an url which match a route
		if ($this->useRoutes && $useRoutes)
		{
			$url = $route['rule'];
			foreach ($params as $key => $value)
				if (isset($route['required'][$key]))
					$url = str_replace('{'.$route['required'][$key].':'.$key.'}', $params[$key], $url);
				else
					$url = str_replace('{'.$key.'}', $value, $url);
			$url = preg_replace('#\{[a-z0-9]+(:[a-z0-9_]+)?\}#', '', $url);
		}
		// Build a classic url index.php?controller=foo&...
		else
			$url = 'index.php?controller='.$route['controller'].(($queryParams) ? '&'.http_build_query($queryParams) : '');
		
		return $url;
	}

	/**
	 * Retrieve the controller from url or request uri if routes are activated
	 * 
	 * @return string
	 */
	public function getController()
	{
		if ($this->controller)
			return $this->controller;
			
		$controller = Tools::getValue('controller');
		if (isset($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m))
		{
			$controller = $m[1];
			if (isset($_GET['controller']))
				$_GET[$m[2]] = $m[3];
			else if (isset($_POST['controller']))
				$_POST[$m[2]] = $m[3];
		}

		// Use routes ? (for url rewriting)
		if ($this->useRoutes && !$controller)
		{
			// Get request uri (HTTP_X_REWRITE_URL is used by IIS)
			if (isset($_SERVER['REQUEST_URI']))
				$request = $_SERVER['REQUEST_URI'];
			else if (isset($_SERVER['HTTP_X_REWRITE_URL']))
				$request = $_SERVER['HTTP_X_REWRITE_URL'];
			else
				return 'index';

			$controller = 'index';
			foreach ($this->routes as $route)
				if (preg_match($route['regexp'], $request, $m))
				{
					// Route found ! Now fill $_GET with parameters of uri
					$controller = $route['controller'];
					foreach ($m as $k => $v)
						if (!is_numeric($k))
							$_GET[$k] = $v;
					break;
				}

			$this->controller = $controller;
		}
		// Default mode, take controller from url
		else
			$this->controller = (!empty($controller)) ? $controller : 'index';

		$this->controller = str_replace('-', '', strtolower($this->controller));
		return $this->controller;
	}
	
	/**
	 * Get list of available controllers
	 * 
	 * @return array
	 */
	public static function getControllers()
	{
		$controller_files = scandir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'controllers');
		$controllers = array();
		foreach ($controller_files as $controller_filename)
		{
			if (substr($controller_filename, -14, 14) == 'Controller.php')
				$controllers[strtolower(substr($controller_filename, 0, -14))] = basename($controller_filename, '.php');
		}

		// add default controller
		$controllers['index'] = 'IndexController';
		$controllers['authentication'] = $controllers['auth'];
		$controllers['productscomparison'] = $controllers['compare'];
		
		return $controllers;
	}
}
