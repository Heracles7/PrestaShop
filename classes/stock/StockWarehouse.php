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
class StockWarehouseCore extends ObjectModel
{
	public $id_address;
	public $reference;
	public $name;
	public $id_employee;
	public $stock_management;

	protected $fieldsRequired = array('id_address', 'reference', 'name', 'id_employee', 'stock_management');
	protected $fieldsSize = array('stock_management' => 32, 'reference' => 45, 'name' => 45);

	protected $fieldsValidate = array(
		'id_address' => 'isUnsignedId',
		'reference' => 'isString',
		'name' => 'isString',
		'id_employee' => 'isUnsignedId',
		'stock_management' => 'isStockManagement');

	protected $table = 'warehouse';
	protected $identifier = 'id_warehouse';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_address'] = (int)$this->id_addresse;
		$fields['reference'] = $this->reference;
		$fields['name'] = $this->name;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['stock_management'] = $this->$stock_management;
		return $fields;
	}

	/**
	 * Get a list of shop ids linked to a warehouse
	 */
	public function getIdShopList()
	{
		$query = new DbQuery();
		$query->select('ws.id_shop');
		$query->from(_DB_PREFIX_.'warehouse_shop ws');
		$query->where($this->identifier.' = '.(int)$this->id);
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
	}
	

	/**
	 * Linked a list of shop ids to a warehouse
	 * 
	 * @param $shopIdList List of shop ids to linked to the warehouse
	 */
	public function setIdShopList($id_shop_list)
	{
		$row_to_instert = array();
		foreach ($id_shop_list as $id_shop)
		{
			$row_to_instert = array(
				$this->reference => $this->id,
				'id_shop' => $id_shop);
		}
		Db::getInstance()->ExecuteS('DELETE INTO `warehouse_shop` ws WHERE ws.'.$this->identifier.' = '.(int)$this->id);
		Db::getInstance()->autoExecute('warehouse_shop', $row_to_instert, 'INSERT');
	}
	
	/**
	 * Check if a warehouse exists or not
	 * 
	 * @param $id_warehouse warehouse identifier
	 */
	public static function exists($id_warehouse)
	{
		return (bool)Db::getInstance()->getValue('SELECT count(*) INTO `warehouse` w WHERE w.id_warehouse = '.(int)$id_warehouse);
	}
}