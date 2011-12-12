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
*  @version  Release: $Revision: 9927 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class SupplyOrderReceiptHistoryCore extends ObjectModel
{
	/**
	 * @var int Detail of the supply order
	 */
	public $id_supply_order_detail;

	/**
	 * @var int Employee
	 */
	public $id_employee;

	/**
	 * @var string The first name of the employee responsible of the movement
	 */
	public $employee_firstname;

	/**
	 * @var string The last name of the employee responsible of the movement
	 */
	public $employee_lastname;

	/**
	 * @var int State
	 */
	public $id_supply_order_state;

	/**
	 * @var int Quantity delivered
	 */
	public $quantity;

	/**
	 * @var string Date of delivery
	 */
	public $date_add;

	

	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supply_order_receipt_history',
		'primary' => 'id_supply_order_receipt_history',
		'fields' => array(
			'id_supply_order_detail' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_supply_order_state' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'employee_firstname' => array('type' => 'FILL_ME', 'validate' => 'isName'),
			'employee_lastname' => array('type' => 'FILL_ME', 'validate' => 'isName'),
			'quantity' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedInt', 'required' => true),
			'date_add' => array('type' => 'FILL_ME', 'validate' => 'isDate'),
		),
	);


	public function getFields()
	{
		$this->validateFields();

		$fields['id_supply_order_detail'] = (int)$this->id_supply_order_detail;
		$fields['id_supply_order_state'] = (int)$this->id_supply_order_state;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['employee_lastname'] = pSQL($this->employee_lastname);
		$fields['employee_firstname'] = pSQL(Tools::ucfirst($this->employee_firstname));
		$fields['quantity'] = (int)$this->quantity;

		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}
}