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

class OrderStateCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/** @var string Template name if there is any e-mail to send */
	public $template;

	/** @var boolean Send an e-mail to customer ? */
	public $send_email;

	/** @var boolean Allow customer to view and download invoice when order is at this state */
	public $invoice;

	/** @var string Display state in the specified color */
	public $color;

	public $unremovable;

	/** @var boolean Log authorization */
	public $logable;

	/** @var boolean Delivery */
	public $delivery;

	/** @var boolean Hidden */
	public $hidden;

	/** @var boolean Shipped */
	public $shipped;

 	

	
 	
 	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_state',
		'primary' => 'id_order_state',
		'multilang' => true,
		'fields' => array(
			'send_email' => array('type' => 'FILL_ME', 'validate' => 'isBool'),
			'invoice' => array('type' => 'FILL_ME', 'validate' => 'isBool'),
			'color' => array('type' => 'FILL_ME', 'validate' => 'isColor'),
			'logable' => array('type' => 'FILL_ME', 'validate' => 'isBool'),
			'shipped' => array('type' => 'FILL_ME', 'validate' => 'isBool'),
			'name' => array('type' => 'FILL_ME', 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
			'template' => array('type' => 'FILL_ME', 'lang' => true, 'validate' => 'isTplName', 'size' => 64),
		),
	);


	protected $webserviceParameters = array(
		'fields' => array(
			'unremovable' => array(),
			'delivery' => array(),
			'hidden' => array(),
		),
	);

	public function getFields()
	{
		$this->validateFields();
		$fields['send_email'] = (int)$this->send_email;
		$fields['invoice'] = (int)$this->invoice;
		$fields['color'] = pSQL($this->color);
		$fields['unremovable'] = (int)$this->unremovable;
		$fields['logable'] = (int)$this->logable;
		$fields['delivery'] = (int)$this->delivery;
		$fields['hidden'] = (int)$this->hidden;
		$fields['shipped'] = (int)$this->shipped;
		return $fields;
	}

	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array('name', 'template'));
	}

	/**
	* Get all available order states
	*
	* @param integer $id_lang Language id for state name
	* @return array Order states
	*/
	public static function getOrderStates($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_state` os
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$id_lang.')
		ORDER BY `name` ASC');
	}

	/**
	* Check if we can make a facture when order is in this state
	*
	* @param integer $id_order_state State ID
	* @return boolean availability
	*/
	public static function invoiceAvailable($id_order_state)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `invoice` AS ok
		FROM `'._DB_PREFIX_.'order_state`
		WHERE `id_order_state` = '.(int)$id_order_state);
		return $result['ok'];
	}

	public function isRemovable()
	{
	 	return !($this->unremovable);
	}
}


