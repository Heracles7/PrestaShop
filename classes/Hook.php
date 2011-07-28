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
*  @version  Release: $Revision: 7025 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HookCore extends ObjectModel
{
	/** @var string Name */
	public 		$name;
	
	protected	$fieldsRequired = array('name');
	protected	$fieldsSize = array('name' => 32);
	protected	$fieldsValidate = array('name' => 'isHookName');
	
	protected 	$table = 'hook';
	protected 	$identifier = 'id_hook';
	
	static $preloadModulesFromHooks = null;
	
	public function getFields()
	{
		parent::validateFields();		
		$fields['name'] = pSQL($this->name);
		return $fields;
	}
	
	/**
	  * Return hook ID from name
	  * 
	  * @param string $hookName Hook name
	  * @return integer Hook ID
	  */
	static public function get($hookName)
	{
	 	if (!Validate::isHookName($hookName))
	 		die(Tools::displayError());
	 	
		$result = Db::getInstance()->getRow('
		SELECT `id_hook`, `name`
		FROM `'._DB_PREFIX_.'hook` 
		WHERE `name` = \''.pSQL($hookName).'\'');
		
		return ($result ? $result['id_hook'] : false);
	}
	
	static public function getHooks($position = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'hook` h
		'.($position ? 'WHERE h.`position` = 1' : ''));
	}

	static public function preloadModulesFromHooks()
	{
		if (!is_null(self::$preloadModulesFromHooks))
			return ;

		self::$preloadModulesFromHooks = array();
		$sql = 'SELECT h.id_hook, h.name as h_name, title, description, h.position, live_edit, hm.position as hm_position, m.id_module, m.name, active
				FROM `'._DB_PREFIX_.'hook` h
				INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON (h.id_hook = hm.id_hook)
				INNER JOIN `'._DB_PREFIX_.'module` as m    ON (m.id_module = hm.id_module)
				WHERE hm.id_shop IN('.implode(', ', Context::getContext()->shop->getListOfID()).')
				GROUP BY hm.id_hook, hm.id_module
				ORDER BY hm.position';
		$results = Db::getInstance()->executeS($sql);
		foreach ($results as $result)
		{
			if (!isset(self::$preloadModulesFromHooks[$result['id_hook']]))
				self::$preloadModulesFromHooks[$result['id_hook']] = array();

			self::$preloadModulesFromHooks[$result['id_hook']][$result['id_module']] = array(
				'id_hook' => $result['id_hook'],
				'title' => $result['title'],
				'description' => $result['description'],
				'hm.position' => $result['position'],
				'live_edit' => $result['live_edit'],
				'm.position' => $result['hm_position'],
				'id_module' => $result['id_module'],
				'name' => $result['name'],
				'active' => $result['active'],
			);
		}
	}
	
	static public function getModulesFromHook($hookID, $moduleID = null)
	{
		Hook::preloadModulesFromHooks();

		$list = (isset(self::$preloadModulesFromHooks[$hookID])) ? self::$preloadModulesFromHooks[$hookID] : array();
		if ($moduleID)
			return (isset($list[$moduleID])) ? array($list[$moduleID]) : array();
		return $list;
	}
	
	static public function newOrder($cart, $order, $customer, $currency, $orderStatus)
	{
		return Module::hookExec('newOrder', array(
			'cart' => $cart,
			'order' => $order,
			'customer' => $customer,
			'currency' => $currency,
			'orderStatus' => $orderStatus));
	}

	static public function updateOrderStatus($newOrderStatusId, $id_order)
	{
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);

		$return = ((int)($newOS->id) == Configuration::get('PS_OS_PAYMENT')) ? Module::hookExec('paymentConfirm', array('id_order' => (int)($order->id))) : true;
		$return = Module::hookExec('updateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id))) AND $return;
		return $return;
	}
	
	static public function postUpdateOrderStatus($newOrderStatusId, $id_order)
	{
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);
		$return = Module::hookExec('postUpdateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id)));
		return $return;
	}

	/**
	 * Called when quantity of a product is updated.
	 * 
	 * @param Product
	 * @param Order
	 */
	static public function updateQuantity($product, $order = null)
	{
		return Module::hookExec('updateQuantity', array('product' => $product, 'order' => $order));
	}
	
	static public function productFooter($product, $category)
	{
		return Module::hookExec('productFooter', array('product' => $product, 'category' => $category));
	}
	
	static public function productOutOfStock($product)
	{
		return Module::hookExec('productOutOfStock', array('product' => $product));
	}
	
	static public function addProduct($product)
	{
		return Module::hookExec('addProduct', array('product' => $product));
	}
	
	static public function updateProduct($product)
	{
		return Module::hookExec('updateProduct', array('product' => $product));
	}
	
	static public function deleteProduct($product)
	{
		return Module::hookExec('deleteProduct', array('product' => $product));
	}
	
	static public function updateProductAttribute($id_product_attribute)
	{
		return Module::hookExec('updateProductAttribute', array('id_product_attribute' => $id_product_attribute));
	}
	
	static public function orderConfirmation($id_order)
	{
	    if (Validate::isUnsignedId($id_order))
	    {
			$params = array();
			$order = new Order((int)$id_order);
			$currency = new Currency((int)$order->id_currency);
	    
	    	if (Validate::isLoadedObject($order))
	    	{
				$params['total_to_pay'] = $order->total_paid;
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;
				
				return Module::hookExec('orderConfirmation', $params);
		}
	    }
	    return false;
	}
	
	static public function paymentReturn($id_order, $id_module)
	{
	    if (Validate::isUnsignedId($id_order) AND Validate::isUnsignedId($id_module))
	    {
			$params = array();
			$order = new Order((int)($id_order));
			$currency = new Currency((int)($order->id_currency));
	    
	    	if (Validate::isLoadedObject($order))
	    	{
				$params['total_to_pay'] = $order->total_paid;
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;
				
				return Module::hookExec('paymentReturn', $params, (int)($id_module));
			}
	    }
	    return false;
	}

	static public function PDFInvoice($pdf, $id_order)
	{
		if (!is_object($pdf) OR !Validate::isUnsignedId($id_order))
			return false;
		return Module::hookExec('PDFInvoice', array('pdf' => $pdf, 'id_order' => $id_order));
	}
	
	static public function backBeforePayment($module)
	{
		$params['module'] = strval($module);
		if (!$params['module'])
			return false;
		return Module::hookExec('backBeforePayment', $params);
	}
	
	static public function updateCarrier($id_carrier, $carrier)
	{
		if (!Validate::isUnsignedId($id_carrier) OR !is_object($carrier))
			return false;
		return Module::hookExec('updateCarrier', array('id_carrier' => $id_carrier, 'carrier' => $carrier));
	}
}


