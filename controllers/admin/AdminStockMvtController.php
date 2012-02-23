<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9565 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminStockMvtControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'stock_mvt';
	 	$this->className = 'StockMvt';
	 	$this->identifier = 'id_stock_mvt';
	 	$this->lang = false;
	 	$this->display_multishop_toolbar = false;

		$this->list_no_link = true;
		$this->displayInformation($this->l('This interface allows you to display the stock movements for a selected warehouse.').'<br />');

		$this->fieldsDisplay = array(
			'product_reference' => array(
				'title' => $this->l('Reference'),
				'width' => 100,
				'havingFilter' => true
			),
			'product_ean13' => array(
				'title' => $this->l('EAN 13'),
				'width' => 75,
				'havingFilter' => true
			),
			'product_upc' => array(
				'title' => $this->l('UPC'),
				'width' => 75,
				'havingFilter' => true
			),
			'product_name' => array(
				'title' => $this->l('Name'),
				'havingFilter' => true
			),
			'warehouse_name' => array(
				'title' => $this->l('Warehouse'),
				'havingFilter' => false,
				'orderby' => true,
				'search' => false,
			),
			'sign' => array(
				'title' => $this->l('Sign'),
				'width' => 100,
				'align' => 'center',
				'type' => 'select',
				'filter_key' => 'a!sign',
				'list' => array(
					'1' => $this->l('Increment'),
					'-1' => $this->l('Decrement'),
				),
				'icon' => array(
					-1 => 'remove_stock.png',
					1 => 'add_stock.png'
				),
			),
			'physical_quantity' => array(
				'title' => $this->l('Quantity'),
				'width' => 40,
				'filter_key' => 'a!physical_quantity'
			),
			'price_te' => array(
				'title' => $this->l('Price (TE)'),
				'width' => 70,
				'align' => 'right',
				'type' => 'price',
				'currency' => true,
				'filter_key' => 'a!price_te'
			),
			'reason' => array(
				'title' => $this->l('Label'),
				'width' => 100,
				'havingFilter' => true
			),
			'employee' => array(
				'title' => $this->l('Employee'),
				'width' => 100,
				'havingFilter' => true
			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'width' => 150,
				'align' => 'right',
				'type' => 'datetime',
				'filter_key' => 'a!date_add'
			),
		);

		parent::__construct();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		// removes toolbar btn
		$this->toolbar_btn = array();

		// overrides select
		$this->_select = '
			CONCAT(pl.name, \' \', GROUP_CONCAT(IFNULL(al.name, \'\'), \'\')) product_name,
			CONCAT(a.employee_lastname, \' \', a.employee_firstname) as employee,
			mrl.name as reason,
			stock.reference as product_reference,
			stock.ean13 as product_ean13,
			stock.upc as product_upc,
			w.id_currency as id_currency,
			w.name as warehouse_name';

		// overrides join
		$this->_join = 'INNER JOIN '._DB_PREFIX_.'stock stock ON a.id_stock = stock.id_stock
							LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
								stock.id_product = pl.id_product
								AND pl.id_lang = '.(int)$this->context->language->id.$this->context->shop->addSqlRestrictionOnLang('pl').'
							)
							LEFT JOIN `'._DB_PREFIX_.'stock_mvt_reason_lang` mrl ON (
								a.id_stock_mvt_reason = mrl.id_stock_mvt_reason
								AND mrl.id_lang = '.(int)$this->context->language->id.'
							)
							LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = stock.id_warehouse)
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = stock.id_product_attribute)
							LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
								al.id_attribute = pac.id_attribute
								AND al.id_lang = '.(int)$this->context->language->id.'
							)';
		// overrides group
		$this->_group = 'GROUP BY a.id_stock_mvt';

		// overrides where depending on the warehouse
		$id_warehouse = $this->getCurrentWarehouseId();
		if ($id_warehouse > 0)
			$this->_where = ' AND w.id_warehouse = '.$id_warehouse;

		// sets the current warehouse
		$this->tpl_list_vars['current_warehouse'] = $this->getCurrentWarehouseId();

		// sets the list of warehouses
		$warehouses = Warehouse::getWarehouses(true);
		array_unshift($warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));
		$this->tpl_list_vars['list_warehouses'] = $warehouses;

		// sets toolbar
		$this->initToolbar();

		// renders list
		$list = parent::renderList();

		// if export requested
		if (Tools::isSubmit('csv'))
		{
			if (count($this->_list) > 0)
			{
				$this->renderCSV();
				die;
			}
			else
				$this->displayWarning($this->l('There is nothing to export as CSV.'));
		}

		return $list;
	}

	/**
	 * Gets the current warehouse for this controller
	 *
	 * @return int warehouse_id
	 */
	protected function getCurrentWarehouseId()
	{
		static $warehouse = 0;

		if ($warehouse == 0)
		{
			$warehouse = -1;
			if ((int)Tools::getValue('id_warehouse'))
				$warehouse = (int)Tools::getValue('id_warehouse');
		}

		return $warehouse;
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if (Tools::isSubmit('csv'))
			$limit = false;

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		//If there is a field product_name in the list, check if this field is null and display standard message
		foreach ($this->fieldsDisplay as $key => $value)
			if ($key == 'product_name')
			{
				$nb_items = count($this->_list);

				for ($i = 0; $i < $nb_items; ++$i)
				{
					$item = &$this->_list[$i];

					if (empty($item['product_name']))
						$item['product_name'] = $this->l('The name of this product is not available. Maybe it has been deleted from the system.');
				}
			}
	}

	/**
	 * @see AdminController::initToolbar();
	 */
	public function initToolbar()
	{
		if (Tools::isSubmit('id_warehouse') && (int)Tools::getValue('id_warehouse') != -1)
		{
			$this->toolbar_btn['export-stock-mvt-csv'] = array(
				'short' => 'Export this list as CSV',
				'href' => $this->context->link->getAdminLink('AdminStockMvt').'&amp;csv&amp;id_warehouse='.(int)$this->getCurrentWarehouseId(),
				'desc' => $this->l('Export (CSV)'),
			);
		}
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	/**
	 * Exports CSV
	 */
	public function renderCSV()
	{
		if (!$this->_list)
			return;

		// header
		if (Tools::getValue('id_warehouse') != -1)
			$filename = $this->l('stock_mvt').'_'.Warehouse::getWarehouseNameById((int)Tools::getValue('id_warehouse')).'.csv';
		else
			$filename = $this->l('stock_mvt').'.csv';
		header('Content-type: text/csv');
		header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="'.$filename);

		// puts keys
		$keys = array('id_order', 'id_supply_order', 'emloyee_firstname', 'employee_lastname', 'physical_quantity',
					  'date_add', 'sign', 'price_te', 'product_name', 'label', 'product_reference', 'product_ean13', 'product_upc');
		echo sprintf("%s\n", implode(';', $keys));


		// puts rows
		foreach ($this->_list as $row)
		{
			$row_csv = array($row['id_order'], $row['id_supply_order'], $row['employee_firstname'],
							 $row['employee_lastname'], $row['physical_quantity'], $row['date_add'],
							 $row['sign'], $row['price_te'], $row['product_name'],
							 $row['reason'], $row['product_reference'], $row['product_ean13'], $row['product_upc']
			);

			// puts one row
			echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row_csv)));
		}
	}

}
