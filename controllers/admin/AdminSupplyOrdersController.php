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
*  @version  Release: $Revision: 10019 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminSupplyOrdersControllerCore extends AdminController
{

	/*
	 * @var array List of warehouses
	 */
	private $warehouses;

	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'supply_order';
	 	$this->className = 'SupplyOrder';
	 	$this->identifier = 'id_supply_order';
	 	$this->lang = false;
	 	$this->is_template_list = false;

		$this->addRowAction('updatereceipt');
		$this->addRowAction('changestate');
		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('details');
		$this->list_no_link = true;

		$this->fieldsDisplay = array(
			'reference' => array(
				'title' => $this->l('Reference'),
				'width' => 130,
				'havingFilter' => true
			),
			'supplier' => array(
				'title' => $this->l('Supplier'),
				'width' => 130,
				'filter_key' => 's!name'
			),
			'warehouse' => array(
				'title' => $this->l('Warehouse'),
				'width' => 130,
				'filter_key' => 'w!name'
			),
			'state' => array(
				'title' => $this->l('Status'),
				'width' => 200,
				'filter_key' => 'stl!name',
				'color' => 'color',
			),
			'date_add' => array(
				'title' => $this->l('Creation'),
				'width' => 150,
				'align' => 'right',
				'type' => 'date',
				'havingFilter' => true,
				'filter_key' => 'a!date_add'
			),
			'date_upd' => array(
				'title' => $this->l('Last modification'),
				'width' => 150,
				'align' => 'right',
				'type' => 'date',
				'havingFilter' => true,
				'filter_key' => 'a!date_upd'
			),
			'date_delivery_expected' => array(
				'title' => $this->l('Delivery (expected)'),
				'width' => 150,
				'align' => 'right',
				'type' => 'date',
				'havingFilter' => true,
				'filter_key' => 'a!date_delivery_expected'
			),
			'id_export' => array(
				'title' => $this->l('Export'),
				'width' => 80,
				'callback' => 'printExportIcons',
				'orderby' => false,
				'search' => false
			),
		);

		// gets the list of warehouses available
		$this->warehouses = Warehouse::getWarehouses(true);
		// gets the final list of warehouses
		array_unshift($this->warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));

		parent::__construct();
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		parent::init();

		if (Tools::isSubmit('addsupply_order') ||
			Tools::isSubmit('submitAddsupply_order') ||
			(Tools::isSubmit('updatesupply_order') && Tools::isSubmit('id_supply_order')))
		{
			// override table, lang, className and identifier for the current controller
		 	$this->table = 'supply_order';
		 	$this->className = 'SupplyOrder';
		 	$this->identifier = 'id_supply_order';
		 	$this->lang = false;

		 	$this->action = 'new';
			$this->display = 'add';

			if (Tools::isSubmit('updatesupply_order'))
				if ($this->tabAccess['edit'] === '1')
					$this->display = 'edit';
				else
					$this->_errors[] = Tools::displayError($this->l('You do not have permission to edit here.'));
		}

		if (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supply_order'))
		{
			// change the display type in order to add specific actions to
			$this->display = 'update_receipt';

			// display correct toolBar
			$this->initToolbar();
		}
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		if (Tools::isSubmit('addsupply_order') ||
			Tools::isSubmit('updatesupply_order') ||
			Tools::isSubmit('submitAddsupply_order') ||
			Tools::isSubmit('submitUpdatesupply_order'))
		{

			if (Tools::isSubmit('addsupply_order') ||	Tools::isSubmit('submitAddsupply_order'))
				$this->toolbar_title = $this->l('Stock : Create new supply order');

			if (Tools::isSubmit('updatesupply_order') || Tools::isSubmit('submitUpdatesupply_order'))
				$this->toolbar_title = $this->l('Stock : Manage supply order');

			if (Tools::isSubmit('mod') && Tools::getValue('mod') === 'template' || $this->object->is_template)
				$this->toolbar_title .= ' '.$this->l('template');

			$this->addJqueryUI('ui.datepicker');

			//get warehouses list
			$warehouses = Warehouse::getWarehouses(true);

			//get currencies list
			$currencies = Currency::getCurrencies();

			//get suppliers list
			$suppliers = Supplier::getSuppliers();

			//get languages list
			$languages = Language::getLanguages(true);

			$this->fields_form = array(
				'legend' => array(
					'title' => $this->l('Order informations'),
					'image' => '../img/admin/edit.gif'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Reference:'),
						'name' => 'reference',
						'size' => 50,
						'required' => true,
						'desc' => $this->l('This is the reference of your order.'),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Supplier:'),
						'name' => 'id_supplier',
						'required' => true,
						'options' => array(
							'query' => $suppliers,
							'id' => 'id_supplier',
							'name' => 'name'
						),
						'desc' => $this->l('Select the supplier you are buying product from.'),
						'hint' => $this->l('Be careful ! When changing this field, all products already added to the order will be removed.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Warehouse:'),
						'name' => 'id_warehouse',
						'required' => true,
						'options' => array(
							'query' => $warehouses,
							'id' => 'id_warehouse',
							'name' => 'name'
						),
						'desc' => $this->l('Select the warehouse where you want the order to be sent to.'),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Currency:'),
						'name' => 'id_currency',
						'required' => true,
						'options' => array(
							'query' => $currencies,
							'id' => 'id_currency',
							'name' => 'name'
						),
						'desc' => $this->l('The currency of the order.'),
						'hint' => $this->l('Be careful ! When changing this field, all products already added to the order will be removed.')
					),
					array(
						'type' => 'select',
						'label' => $this->l('Order Language:'),
						'name' => 'id_lang',
						'required' => true,
						'options' => array(
							'query' => $languages,
							'id' => 'id_lang',
							'name' => 'name'
						),
						'desc' => $this->l('The language of the order.')
					),
					array(
						'type' => 'date',
						'label' => $this->l('Delivery date:'),
						'name' => 'date_delivery_expected',
						'size' => 20,
						'required' => true,
						'desc' => $this->l('This is the expected delivery date for this order.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Global discount rate (%):'),
						'name' => 'discount_rate',
						'size' => 7,
						'required' => true,
						'desc' => $this->l('This is the global discount rate in percents for the order.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Automatically load products:'),
						'name' => 'load_products',
						'size' => 7,
						'required' => false,
						'hint' => 'This will reset the order',
						'desc' => $this->l('If specified, each product which quantity is less or equal to this value will be loaded.'),
					),
				),
				'submit' => array(
					'title' => $this->l('   Save order   '),
				)
			);

			if (Tools::isSubmit('mod') && Tools::getValue('mod') === 'template' ||
				$this->object->is_template)
			{

				$this->fields_form['input'][] = array(
					'type' => 'hidden',
					'name' => 'is_template'
				);
			}

			//specific discount display
			$this->object->discount_rate = Tools::ps_round($this->object->discount_rate, 4);

			return parent::renderForm();
		}

	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if (Tools::isSubmit('csv_orders') || Tools::isSubmit('csv_orders_details') || Tools::isSubmit('csv_order_details'))
			$limit = false;

		// defines button specific for non-template supply orders
		if (!$this->is_template_list)
		{
			// adds export csv buttons
			$this->toolbar_btn['export-csv-orders'] = array(
				'short' => 'Export Orders',
				'href' => $this->context->link->getAdminLink('AdminSupplyOrders').'&amp;csv_orders&id_warehouse='.$this->getCurrentWarehouse(),
				'desc' => $this->l('Export Orders (CSV)'),
			);

			$this->toolbar_btn['export-csv-details'] = array(
				'short' => 'Export Orders Details',
				'href' => $this->context->link->getAdminLink('AdminSupplyOrders').'&amp;csv_orders_details&id_warehouse='.$this->getCurrentWarehouse(),
				'desc' => $this->l('Export Orders Details (CSV)'),
			);

			unset($this->toolbar_btn['new']);
			if ($this->tabAccess['add'] === '1')
			{
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
					'desc' => $this->l('Add new')
				);
			}
		}

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		// actions filters on supply orders list
		if ($this->table == 'supply_order')
		{
			$nb_items = count($this->_list);

			for ($i = 0; $i < $nb_items; $i++)
			{
				// if the current state doesn't allow order edit, skip the edit action
				if ($this->_list[$i]['editable'] == 0)
					$this->addRowActionSkipList('edit', $this->_list[$i]['id_supply_order']);
				if ($this->_list[$i]['enclosed'] == 1)
					$this->addRowActionSkipList('changestate', $this->_list[$i]['id_supply_order']);
				if (1 != $this->_list[$i]['pending_receipt'])
					$this->addRowActionSkipList('updatereceipt', $this->_list[$i]['id_supply_order']);
			}
		}
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		$this->displayInformation($this->l('This interface allows you to manage supply orders.').'<br />');
		$this->displayInformation($this->l('Also, you can create templates that you can later use to generate actual orders.').'<br />');

		// assigns warehouses
		$this->tpl_list_vars['warehouses'] = $this->warehouses;
		$this->tpl_list_vars['current_warehouse'] = $this->getCurrentWarehouse();
		$this->tpl_list_vars['filter_status'] = $this->getFilterStatus();

		// overrides query
		$this->_select = '
			s.name AS supplier,
			w.name AS warehouse,
			stl.name AS state,
			st.delivery_note,
			st.editable,
			st.enclosed,
			st.receipt_state,
			st.pending_receipt,
			st.color AS color,
			a.id_supply_order as id_export';

		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'supply_order_state_lang` stl ON
						(
							a.id_supply_order_state = stl.id_supply_order_state
							AND stl.id_lang = '.(int)$this->context->language->id.'
						)
						LEFT JOIN `'._DB_PREFIX_.'supply_order_state` st ON a.id_supply_order_state = st.id_supply_order_state
						LEFT JOIN `'._DB_PREFIX_.'supplier` s ON a.id_supplier = s.id_supplier
						LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = a.id_warehouse)';

		$this->_where = ' AND a.is_template = 0';

		if ($this->getCurrentWarehouse() != -1)
			$this->_where .= ' AND a.id_warehouse = '.$this->getCurrentWarehouse();
		if ($this->getFilterStatus() != 0)
			$this->_where .= ' AND st.enclosed != 1';
		$first_list = parent::renderList();

		if (Tools::isSubmit('csv_orders') || Tools::isSubmit('csv_orders_details') || Tools::isSubmit('csv_order_details'))
		{
			if (count($this->_list) > 0)
			{
				$this->renderCSV();
				die;
			}
			else
				$this->displayWarning($this->l('There is nothing to export as CSV.'));
		}

		// second list : templates
		$second_list = null;
		$this->is_template_list = true;
		unset($this->tpl_list_vars['warehouses']);
		unset($this->tpl_list_vars['current_warehouse']);
		unset($this->tpl_list_vars['filter_status']);

		// unsets actions
		$this->actions = array();
		unset($this->toolbar_btn['export-csv-orders']);
		unset($this->toolbar_btn['export-csv-details']);
		// adds actions
		$this->addRowAction('delete');
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('createsupplyorder');
		// unsets some fields
		unset($this->fieldsDisplay['state'],
			  $this->fieldsDisplay['date_upd'],
			  $this->fieldsDisplay['id_pdf'],
			  $this->fieldsDisplay['date_delivery_expected']);
		// adds filter, to gets only templates
		unset($this->_where);
		$this->_where = ' AND a.is_template = 1';
		if ($this->getCurrentWarehouse() != -1)
			$this->_where .= ' AND a.id_warehouse = '.$this->getCurrentWarehouse();

		// re-defines toolbar & buttons
		$this->toolbar_title = $this->l('Stock : Supply orders templates');
		$this->initToolbar();
		unset($this->toolbar_btn['new']);
		$this->toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&amp;add'.$this->table.'&mod=template&amp;token='.$this->token,
			'desc' => $this->l('Add new template')
		);
		// inits list
		$second_list = parent::renderList();

		return $first_list.$second_list;
	}

	/**
	 * Init the content of change state action
	 */
	public function initChangeStateContent()
	{
		$id_supply_order = (int)Tools::getValue('id_supply_order', 0);

		if ($id_supply_order <= 0)
		{
			$this->_errors[] = Tools::displayError($this->l('The specified supply order is not valid'));
			return parent::initContent();
		}

		$supply_order = new SupplyOrder($id_supply_order);

		if (!Validate::isLoadedObject($supply_order))
		{
			$this->_errors[] = Tools::displayError($this->l('The specified supply order is not valid'));
			return parent::initContent();
		}

		// change the display type in order to add specific actions to
		$this->display = 'update_order_state';
		// overrides parent::initContent();
		$this->initToolbar();

		// given the current state, loads available states
		$states = SupplyOrderState::getSupplyOrderStates($supply_order->id_supply_order_state);
		// loads languages
		$this->getlanguages();

		// defines the fields of the form to display
		$this->fields_form[]['form'] = array(
			'legend' => array(
				'title' => $this->l('Supply Order Status'),
				'image' => '../img/admin/cms.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_supply_order',
				),
				array(
					'type' => 'select',
					'label' => $this->l('New status of the order:'),
					'name' => 'id_supply_order_state',
					'required' => true,
					'options' => array(
						'query' => $states,
						'id' => 'id_supply_order_state',
						'name' => 'name'
					),
					'desc' => $this->l('Choose the new status of your order')
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		// sets up the helper
		$helper = new HelperForm();
		$helper->submit_action = 'submitChangestate';
		$helper->currentIndex = self::$currentIndex;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->toolbar_fix = false;
		$helper->token = $this->token;
		$helper->id = null; // no display standard hidden field in the form
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = array(
			'id_supply_order_state' => Tools::getValue('id_supplier', ''),
			'id_supply_order' => $id_supply_order,
		);

		// generates the form to display
		$this->content = $helper->generateForm($this->fields_form);

		// assigns our content
		$this->tpl_form_vars['show_change_state_form'] = true;
		$this->tpl_form_vars['state_content'] = $this->content;

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	/**
	 * Init the content of change state action
	 */
	public function initUpdateSupplyOrderContent()
	{
		$this->addJqueryPlugin('autocomplete');

		// load supply order
		$id_supply_order = (int)Tools::getValue('id_supply_order', null);

		if ($id_supply_order != null)
		{
			$supply_order = new SupplyOrder($id_supply_order);

			$currency = new Currency($supply_order->id_currency);

			if (Validate::isLoadedObject($supply_order))
			{
				// load products of this order
				$products = $supply_order->getEntries();
				$product_ids = array();

				if (isset($this->order_products_errors) && is_array($this->order_products_errors))
				{
					//for each product in error array, check if it is in products array, and remove it to conserve last user values
					foreach ($this->order_products_errors as $pe)
						foreach ($products as $index_p => $p)
							if (($p['id_product'] == $pe['id_product']) && ($p['id_product_attribute'] == $pe['id_product_attribute']))
								unset($products[$index_p]);

					// then merge arrays
					$products = array_merge($this->order_products_errors, $products);
				}

				foreach ($products as &$item)
				{
					// calculate md5 checksum on each product for use in tpl
					$item['checksum'] = md5(_COOKIE_KEY_.$item['id_product'].'_'.$item['id_product_attribute']);
					$item['unit_price_te'] = Tools::ps_round($item['unit_price_te'], 2);

					// add id to ids list
					$product_ids[] = $item['id_product'].'_'.$item['id_product_attribute'];
				}

				$this->tpl_form_vars['products_list'] = $products;
				$this->tpl_form_vars['product_ids'] = implode($product_ids, '|');
				$this->tpl_form_vars['product_ids_to_delete'] = '';
				$this->tpl_form_vars['supplier_id'] = $supply_order->id_supplier;
				$this->tpl_form_vars['currency'] = $currency;
			}
		}

		$this->tpl_form_vars['content'] = $this->content;
		$this->tpl_form_vars['token'] = $this->token;
		$this->tpl_form_vars['show_product_management_form'] = true;

		// call parent initcontent to render standard form content
		parent::initContent();
	}

	/**
	 * Inits the content of 'update_receipt' action
	 * Called in initContent()
	 * @see AdminSuppliersOrders::initContent()
	 */
	public function initUpdateReceiptContent()
	{
		$id_supply_order = (int)Tools::getValue('id_supply_order', null);

		// if there is no order to fetch
		if (null == $id_supply_order)
			return parent::initContent();

		$supply_order = new SupplyOrder($id_supply_order);

		// if it's not a valid order
		if (!Validate::isLoadedObject($supply_order))
			return parent::initContent();

		// re-defines fieldsDisplay
		$this->fieldsDisplay = array(
			'reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'center',
				'width' => 30,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'ean13' => array(
				'title' => $this->l('EAN13'),
				'align' => 'center',
				'width' => 30,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'upc' => array(
				'title' => $this->l('UPC'),
				'align' => 'center',
				'width' => 30,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'name' => array(
				'title' => $this->l('Name'),
				'align' => 'center',
				'width' => 350,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'quantity_received_today' => array(
				'title' => $this->l('Quantity received today'),
				'align' => 'center',
				'width' => 20,
				'type' => 'editable',
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'quantity_received' => array(
				'title' => $this->l('Quantity received'),
				'align' => 'center',
				'width' => 20,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'quantity_expected' => array(
				'title' => $this->l('Quantity expected'),
				'align' => 'center',
				'width' => 20,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'quantity_left' => array(
				'title' => $this->l('Quantity left to receive'),
				'align' => 'center',
				'width' => 20,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			)
		);

		// attributes override
		unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
		$this->table = 'supply_order_detail';
		$this->identifier = 'id_supply_order_detail';
	 	$this->className = 'SupplyOrderDetail';
	 	$this->list_simple_header = true;
	 	$this->list_no_link = true;
	 	$this->bulk_actions = array('Update' => array('text' => $this->l('Update selected'), 'confirm' => $this->l('Update selected items?')));
		$this->addRowAction('details');

		// sets toolbar title with order reference
		$this->toolbar_title = sprintf($this->l('Reception of products for supply order #%s'), $supply_order->reference);

		$this->lang = false;
		$lang_id = (int)$this->context->language->id; //employee lang

		// gets values corresponding to fieldsDisplay
		$this->_select = '
			a.id_supply_order_detail as id,
			a.quantity_received as quantity_received,
			a.quantity_expected as quantity_expected,
			IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_left,
			IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_received_today';

		$this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;

		$this->_group = 'GROUP BY a.id_supply_order_detail';

		// gets the list ordered by price desc, without limit
		$this->getList($lang_id, 'quantity_expected', 'DESC', 0, false, false);

		// defines action for POST
		$action = '&id_supply_order='.$id_supply_order;

		// renders list
		$helper = new HelperList();
		$this->setHelperDisplay($helper);
		$helper->actions = array('details');
		$helper->override_folder = 'supply_orders_receipt_history/';

		$helper->currentIndex = self::$currentIndex.$action;

		// display these global order informations
		$this->displayInformation($this->l('This interface allows you to update the quantities of this on-going order.').'<br />');
		$this->displayInformation($this->l('Be careful : once you update, you cannot go back unless you add new negative stock movements.').'<br />');

		// generates content
		$content = $helper->generateList($this->_list, $this->fieldsDisplay);

		// assigns var
		$this->context->smarty->assign(array(
			'content' => $content,
		));
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		// Manage the add stock form
		if (Tools::isSubmit('changestate'))
			$this->initChangeStateContent();
		else if (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supply_order'))
			$this->initUpdateReceiptContent();
		else if (Tools::isSubmit('viewsupply_order') && Tools::isSubmit('id_supply_order'))
		{
			$this->action = 'view';
			$this->display = 'view';
			parent::initContent();
		}
		else if (Tools::isSubmit('updatesupply_order'))
			$this->initUpdateSupplyOrderContent();
		else
			parent::initContent();
	}

	/**
	 * Ths method manage associated products to the order when updating it
	 */
	public function manageOrderProducts()
	{
		// load supply order
		$id_supply_order = (int)Tools::getValue('id_supply_order', null);
		$products_already_in_order = array();

		if ($id_supply_order != null)
		{
			$supply_order = new SupplyOrder($id_supply_order);

			if (Validate::isLoadedObject($supply_order))
			{
				// tests if the supplier or currency have changed in the supply order
				$new_supplier_id = (int)Tools::getValue('id_supplier');
				$new_currency_id = (int)Tools::getValue('id_currency');

				if (($new_supplier_id != $supply_order->id_supplier) ||
					($new_currency_id != $supply_order->id_currency))
				{
					// resets all products in this order
					$supply_order->resetProducts();
				}
				else
				{
					$products_already_in_order = $supply_order->getEntries();
					$currency = new Currency($supply_order->id_ref_currency);

					// gets all product ids to manage
					$product_ids_str = Tools::getValue('product_ids', null);
					$product_ids = explode('|', $product_ids_str);
					$product_ids_to_delete_str = Tools::getValue('product_ids_to_delete', null);
					$product_ids_to_delete = array_unique(explode('|', $product_ids_to_delete_str));

					//delete products that are not managed anymore
					foreach ($products_already_in_order as $paio)
					{
						$product_ok = false;

						foreach ($product_ids_to_delete as $id)
						{
							$id_check = $paio['id_product'].'_'.$paio['id_product_attribute'];
							if ($id_check == $id)
								$product_ok = true;
						}

						if ($product_ok === true)
						{
							$entry = new SupplyOrderDetail($paio['id_supply_order_detail']);
							$entry->delete();
						}
					}

					// manage each product
					foreach ($product_ids as $id)
					{
						$errors = array();

						// check if a checksum is available for this product and test it
						$check = Tools::getValue('input_check_'.$id, '');
						$check_valid = md5(_COOKIE_KEY_.$id);

						if ($check_valid != $check)
							continue;

						$pos = strpos($id, '_');
						if ($pos === false)
							continue;

						// Load / Create supply order detail
						$entry = new SupplyOrderDetail();
						$id_supply_order_detail = (int)Tools::getValue('input_id_'.$id, 0);
						if ($id_supply_order_detail > 0)
						{
							$existing_entry = new SupplyOrderDetail($id_supply_order_detail);
							if (Validate::isLoadedObject($supply_order))
								$entry = &$existing_entry;
						}

						// get product informations
						$entry->id_product = substr($id, 0, $pos);
						$entry->id_product_attribute = substr($id, $pos + 1);
						$entry->unit_price_te = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_unit_price_te_'.$id, 0));
						$entry->quantity_expected = (int)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_quantity_expected_'.$id, 0));
						$entry->discount_rate = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_discount_rate_'.$id, 0));
						$entry->tax_rate = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_tax_rate_'.$id, 0));
						$entry->reference = Tools::getValue('input_reference_'.$id, '');
						$entry->ean13 = Tools::getValue('input_ean13_'.$id, '');
						$entry->upc = Tools::getValue('input_upc_'.$id, '');

						//get the product name in the order language
						$entry->name = Product::getProductName($entry->id_product, $entry->id_product_attribute, $supply_order->id_lang);

						if (empty($entry->name))
							$entry->name = '';

						//get the product supplier reference
						$entry->supplier_reference = ProductSupplier::getProductSupplierReference($entry->id_product, $entry->id_product_attribute, $supply_order->id_supplier);

						if ($entry->supplier_reference == null)
							$entry->supplier_reference = '';

						$entry->exchange_rate = $currency->conversion_rate;
						$entry->id_currency = $currency->id;
						$entry->id_supply_order = $supply_order->id;

						$errors = $entry->validateController();

						//get the product name displayed in the backoffice according to the employee language
						$entry->name_displayed = Tools::getValue('input_name_displayed_'.$id, '');

						// if there is a problem, handle error for the current product
						if (count($errors) > 0)
						{
							// add the product to error array => display again product line
							$this->order_products_errors[] = array(
								'id_product' =>	$entry->id_product,
								'id_product_attribute' => $entry->id_product_attribute,
								'unit_price_te' =>	$entry->unit_price_te,
								'quantity_expected' => $entry->quantity_expected,
								'discount_rate' =>	$entry->discount_rate,
								'tax_rate' => $entry->tax_rate,
								'name' => $entry->name,
								'name_displayed' => $entry->name_displayed,
								'reference' => $entry->reference,
								'ean13' => $entry->ean13,
								'upc' => $entry->upc,
							);

							$error_str = '<ul>';
							foreach ($errors as $e)
								$error_str .= '<li>'.$this->l('field ').$e.'</li>';
							$error_str .= '</ul>';

							$this->_errors[] = Tools::displayError($this->l('Please verify the informations of the product: ').$entry->name.' '.$error_str);
						}
						else
							$entry->save();
					}
				}
			}
		}
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		$this->is_editing_order = false;

		// Checks access
		if (Tools::isSubmit('submitAddsupply_order') && !($this->tabAccess['add'] === '1'))
			$this->_errors[] = Tools::displayError($this->l('You do not have the required permission to add a supply order.'));
		if (Tools::isSubmit('submitBulkUpdatesupply_order_detail') && !($this->tabAccess['edit'] === '1'))
			$this->_errors[] = Tools::displayError($this->l('You do not have the required permission to edit an order.'));
		// Global checks when add / update a supply order
		if (Tools::isSubmit('submitAddsupply_order') || Tools::isSubmit('submitAddsupply_orderAndStay'))
		{
			$this->action = 'save';
			$this->is_editing_order = true;

			// get supplier ID
			$id_supplier = (int)Tools::getValue('id_supplier', 0);
			if ($id_supplier <= 0 || !Supplier::supplierExists($id_supplier))
				$this->_errors[] = Tools::displayError($this->l('The selected supplier is not valid.'));

			// get warehouse id
			$id_warehouse = (int)Tools::getValue('id_warehouse', 0);
			if ($id_warehouse <= 0 || !Warehouse::exists($id_warehouse))
				$this->_errors[] = Tools::displayError($this->l('The selected warehouse is not valid.'));

			// get currency id
			$id_currency = (int)Tools::getValue('id_currency', 0);
			if ($id_currency <= 0 || ( !($result = Currency::getCurrency($id_currency)) || empty($result) ))
				$this->_errors[] = Tools::displayError($this->l('The selected currency is not valid.'));

			// get delivery date
			$delivery_expected = new DateTime(pSQL(Tools::getValue('date_delivery_expected')));
			// converts date to timestamp
			if ($delivery_expected <= (new DateTime('yesterday')))
				$this->_errors[] = Tools::displayError($this->l('The date you specified cannot be in the past.'));

			// gets threshold
			$quantity_threshold = null;
			if (Tools::getValue('load_products') && Validate::isInt(Tools::getValue('load_products')))
				$quantity_threshold = (int)Tools::getValue('load_products');

			if (!count($this->_errors))
			{
				// specify initial state
				$_POST['id_supply_order_state'] = 1; //defaut creation state

				// specify global reference currency
				$_POST['id_ref_currency'] = Currency::getDefaultCurrency()->id;

				// specify supplier name
				$_POST['supplier_name'] = Supplier::getNameById($id_supplier);

				//specific discount check
				$_POST['discount_rate'] = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('discount_rate', 0));

			}

			// manage each associated product
			$this->manageOrderProducts();
		}

		// Manage state change
		if (Tools::isSubmit('submitChangestate')
			&& Tools::isSubmit('id_supply_order')
			&& Tools::isSubmit('id_supply_order_state'))
		{
			if ($this->tabAccess['edit'] != '1')
				$this->_errors[] = Tools::displayError($this->l('You do not have permissions to change order status.'));

			// get state ID
			$id_state = (int)Tools::getValue('id_supply_order_state', 0);
			if ($id_state <= 0)
				$this->_errors[] = Tools::displayError($this->l('The selected supply order status is not valid.'));

			// get supply order ID
			$id_supply_order = (int)Tools::getValue('id_supply_order', 0);
			if ($id_supply_order <= 0)
				$this->_errors[] = Tools::displayError($this->l('The supply order id is not valid.'));

			if (!count($this->_errors))
			{
				// try to load supply order
				$supply_order = new SupplyOrder($id_supply_order);

				if (Validate::isLoadedObject($supply_order))
				{
					// get valid available possible states for this order
					$states = SupplyOrderState::getSupplyOrderStates($supply_order->id_supply_order_state);

					foreach ($states as $state)
					{
						// if state is valid, change it in the order
						if ($id_state == $state['id_supply_order_state'])
						{

							$new_state = new SupplyOrderState($id_state);
							$old_state = new SupplyOrderState($supply_order->id_supply_order_state);

							// special case of validate state - check if there are products in the order and the required state is not an enclosed state
							if ($supply_order->isEditable() && !$supply_order->hasEntries() && !$new_state->enclosed)
								$this->_errors[] = Tools::displayError(
									$this->l('It is not possible to change the status of this order because you did not order any products')
								);

							if (!count($this->_errors))
							{
								$supply_order->id_supply_order_state = $state['id_supply_order_state'];
								if ($supply_order->save())
								{
									// if pending_receipt,
									// or if the order is being canceled,
									// synchronizes StockAvailable
									if (($new_state->pending_receipt && !$new_state->receipt_state) ||
										($old_state->receipt_state && $new_state->enclosed && !$new_state->receipt_state))
									{
										$supply_order_details = $supply_order->getEntries();
										$products_done = array();
										foreach ($supply_order_details as $supply_order_detail)
										{
											if (!in_array($supply_order_detail['id_product'], $products_done))
											{
												StockAvailable::synchronize($supply_order_detail['id_product']);
												$products_done[] = $supply_order_detail['id_product'];
											}
										}
									}

									$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
									$redirect = self::$currentIndex.'&token='.$token;
									$this->redirect_after = $redirect.'&conf=5';
								}
							}
						}
					}
				}
				else
					$this->_errors[] = Tools::displayError($this->l('The selected supplier is not valid.'));
			}
		}

		// updates receipt
		if (Tools::isSubmit('submitBulkUpdatesupply_order_detail') && Tools::isSubmit('id_supply_order'))
			$this->postProcessUpdateReceipt();

		// use template to create a supply order
		if (Tools::isSubmit('create_supply_order') && Tools::isSubmit('id_supply_order'))
			$this->postProcessCopyFromTemplate();

		if ((!count($this->_errors) && $this->is_editing_order) || !$this->is_editing_order)
			parent::postProcess();

		// if the threshold is defined and we are saving the order
		if (Tools::isSubmit('submitAddsupply_order') && $quantity_threshold != null)
			$this->loadProducts($quantity_threshold);
	}

	/**
	 * Exports CSV
	 */
	protected function renderCSV()
	{
		// exports orders
		if (Tools::isSubmit('csv_orders'))
		{
			$ids = array();
			foreach ($this->_list as $entry)
				$ids[] = $entry['id_supply_order'];

			if (count($ids) <= 0)
				return;

			$orders = new Collection('SupplyOrder', $id_lang = (int)Context::getContext()->language->id);
			$orders->where('is_template = 0');
			$orders->where('id_supply_order IN('.implode(', ', $ids).')');
			$id_warehouse = $this->getCurrentWarehouse();
			if ($id_warehouse != -1)
				$orders->where('id_warehouse = '.(int)$id_warehouse);
			$orders->getAll();
			$csv = new CSV($orders, $this->l('supply_orders'));
    		$csv->export();
		}
		// exports details for all orders
		else if (Tools::isSubmit('csv_orders_details'))
		{
			// header
			header('Content-type: text/csv');
			header('Cache-Control: no-store, no-cache');
        	header('Content-disposition: attachment; filename="'.$this->l('supply_orders_details').'.csv"');

        	// echoes details
			$ids = array();
			foreach ($this->_list as $entry)
				$ids[] = $entry['id_supply_order'];

			if (count($ids) <= 0)
				return;

			// for each supply order
			$keys = array('id_product', 'id_product_attribute', 'reference', 'supplier_reference', 'ean13', 'upc', 'name',
						  'unit_price_te', 'quantity_expected', 'quantity_received', 'price_te', 'discount_rate', 'discount_value_te',
						  'price_with_discount_te', 'tax_rate', 'tax_value', 'price_ti', 'tax_value_with_order_discount',
						  'price_with_order_discount_te', 'id_supply_order');
			echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $keys)));

			foreach ($ids as $id)
			{
				$query = new DbQuery();
				$query->select(implode(', sod.', $keys));
				$query->from('supply_order_detail sod');
				$query->leftJoin('supply_order so ON (so.id_supply_order = sod.id_supply_order)');
				$id_warehouse = $this->getCurrentWarehouse();
				if ($id_warehouse != -1)
					$query->where('so.id_warehouse = '.(int)$id_warehouse);
				$query->where('sod.id_supply_order = '.(int)$id);
				$query->orderBy('sod.id_supply_order_detail DESC');
				$resource = Db::getInstance()->query($query);
				// gets details
				while ($row = Db::getInstance()->nextRow($resource))
       				 echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row)));
			}

		}
		// exports details for the given order
		else if (Tools::isSubmit('csv_order_details') && Tools::getValue('id_supply_order'))
		{
			$supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));
			if (Validate::isLoadedObject($supply_order))
			{
				$details = $supply_order->getEntriesCollection();
				$details->getAll();
    			$csv = new CSV($details, $this->l('supply_order').'_'.$supply_order->reference.'_details');
    			$csv->export();
			}
		}
	}

	/**
	 * Helper function for AdminSupplyOrdersController::postProcess()
	 *
	 * @see AdminSupplyOrdersController::postProcess()
	 */
	protected function postProcessUpdateReceipt()
	{
		// gets all box selected
		$rows = Tools::getValue('supply_order_detailBox');
		if (!$rows)
		{
			$this->_errors[] = Tools::displayError($this->l('You did not select any product to update'));
			return;
		}

		// final array with id_supply_order_detail and value to update
		$to_update = array();
		// gets quantity for each id_order_detail
		foreach ($rows as $row)
		{
			if (Tools::getValue('quantity_received_today_'.$row))
				$to_update[$row] = (int)Tools::getValue('quantity_received_today_'.$row);
		}

		// checks if there is something to update
		if (!count($to_update))
		{
			$this->_errors[] = Tools::displayError($this->l('You did not select any product to update'));
			return;
		}

		foreach ($to_update as $id_supply_order_detail => $quantity)
		{
			$supply_order_detail = new SupplyOrderDetail($id_supply_order_detail);
			$supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));

			if (Validate::isLoadedObject($supply_order_detail) && Validate::isLoadedObject($supply_order))
			{
				// checks if quantity is valid
				// It's possible to receive more quantity than expected in case of a shipping error from the supplier
				if (!Validate::isInt($quantity) || $quantity <= 0)
					$this->_errors[] = sprintf(Tools::displayError($this->l('Quantity (%d) for product #%d is not valid')), (int)$quantity, (int)$id_supply_order_detail);
				else // everything is valid :  updates
				{
					// creates the history
					$supplier_receipt_history = new SupplyOrderReceiptHistory();
					$supplier_receipt_history->id_supply_order_detail = (int)$id_supply_order_detail;
					$supplier_receipt_history->id_employee = (int)$this->context->employee->id;
					$supplier_receipt_history->employee_firstname = pSQL($this->context->employee->firstname);
					$supplier_receipt_history->employee_lastname = pSQL($this->context->employee->lastname);
					$supplier_receipt_history->id_supply_order_state = (int)$supply_order->id_supply_order_state;
					$supplier_receipt_history->quantity = (int)$quantity;

					// updates quantity received
					$supply_order_detail->quantity_received += (int)$quantity;

					// if current state is "Pending receipt", then we sets it to "Order received in part"
					if (3 == $supply_order->id_supply_order_state)
						$supply_order->id_supply_order_state = 4;

					// Adds to stock
					$warehouse = new Warehouse($supply_order->id_warehouse);
					if (!Validate::isLoadedObject($warehouse))
					{
						$this->_errors[] = Tools::displayError($this->l('Warehouse could not be loaded'));
						return;
					}

					$price = $supply_order_detail->unit_price_te;
					// converts the unit price to the warehouse currency if needed
					if ($supply_order->id_currency != $warehouse->id_currency)
					{
						// first, converts the price to the default currency
						$price_converted_to_default_currency = Tools::convertPrice($supply_order_detail->unit_price_te, $supply_order->id_currency, false);

						// then, converts the newly calculated price from the default currency to the needed currency
						$price = Tools::ps_round(Tools::convertPrice($price_converted_to_default_currency,
																	 $warehouse->id_currency,
																	 true),
												 6);
					}

					$manager = StockManagerFactory::getManager();
					$res = $manager->addProduct($supply_order_detail->id_product,
										 		$supply_order_detail->id_product_attribute,
										 		$warehouse,
										 		(int)$quantity,
										 		Configuration::get('PS_STOCK_MVT_SUPPLY_ORDER'),
										 		$price,
										 		true,
										 		$supply_order->id);
					if ($res) // if product has been added
					{
						$supplier_receipt_history->add();
						$supply_order_detail->save();
						$supply_order->save();
					}
					else
						$this->_errors[] = Tools::displayError($this->l('Something went wrong when adding products in warehouse'));
				}
			}
		}

		if (!count($this->_errors))
		{
			// display confirm message
			$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
			$redirect = self::$currentIndex.'&token='.$token;
			$this->redirect_after = $redirect.'&conf=4';
		}
	}

    /**
	 * Display state action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
    public function displayUpdateReceiptLink($token = null, $id)
    {
        if (!array_key_exists('Receipt', self::$cache_lang))
            self::$cache_lang['Receipt'] = $this->l('Update on-going receptions');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&update_receipt&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['Receipt'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/helper/list/list_action_supply_order_receipt.tpl');
    }

    /**
	 * Display receipt action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
    public function displayChangestateLink($token = null, $id)
    {
        if (!array_key_exists('State', self::$cache_lang))
            self::$cache_lang['State'] = $this->l('Change state');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&changestate&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['State'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/helper/list/list_action_supply_order_change_state.tpl');
    }

    /**
	 * Display state action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
    public function displayCreateSupplyOrderLink($token = null, $id)
    {
        if (!array_key_exists('CreateSupplyOrder', self::$cache_lang))
            self::$cache_lang['CreateSupplyOrder'] = $this->l('Use this template to create a supply order');

        if (!array_key_exists('CreateSupplyOrderConfirm', self::$cache_lang))
            self::$cache_lang['CreateSupplyOrderConfirm'] = $this->l('Are you sure you want to use this template?');

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
            	'&'.$this->identifier.'='.$id.
            	'&create_supply_order&token='.($token != null ? $token : $this->token),
        	'confirm' => self::$cache_lang['CreateSupplyOrderConfirm'],
            'action' => self::$cache_lang['CreateSupplyOrder'],
        ));

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/helper/list/list_action_supply_order_create_from_template.tpl');
    }

	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcess()
	{
		// tests if an id is submit
		if (Tools::isSubmit('id'))
		{
			// overrides attributes
			$this->identifier = 'id_supply_order_history';
			$this->table = 'supply_order_history';

			$this->display = 'list';
			$this->lang = false;
			// gets current lang id
			$lang_id = (int)$this->context->language->id;
			// gets supply order id
			$id_supply_order = (int)Tools::getValue('id');

			// creates new fieldsDisplay
			unset($this->fieldsDisplay);
			$this->fieldsDisplay = array(
				'history_date' => array(
					'title' => $this->l('Last update'),
					'width' => 50,
					'align' => 'left',
					'type' => 'datetime',
					'havingFilter' => true
				),
				'history_employee' => array(
					'title' => $this->l('Employee'),
					'width' => 100,
					'align' => 'left',
					'havingFilter' => true
				),
				'history_state_name' => array(
					'title' => $this->l('Status'),
					'width' => 100,
					'align' => 'left',
					'color' => 'color',
					'havingFilter' => true
				),
			);
			// loads history of the given order
			unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
			$this->_select = '
			a.`date_add` as history_date,
			CONCAT(a.`employee_lastname`, \' \', a.`employee_firstname`) as history_employee,
			sosl.`name` as history_state_name,
			sos.`color` as color';

			$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'supply_order_state` sos ON (a.`id_state` = sos.`id_supply_order_state`)
			LEFT JOIN `'._DB_PREFIX_.'supply_order_state_lang` sosl ON
			(
				a.`id_state` = sosl.`id_supply_order_state`
				AND sosl.`id_lang` = '.(int)$lang_id.'
			)';

			$this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;
			$this->_orderBy = 'a.`date_add`';
			$this->_orderWay = 'DESC';

			// gets list and forces no limit clause in the request
			$this->getList($lang_id, 'date_add', 'DESC', 0, false, false);

			// renders list
			$helper = new HelperList();
			$helper->no_link = true;
			$helper->show_toolbar = false;
			$helper->toolbar_fix = false;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			//$helper->colorOnBackground = true;
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}
		else if (Tools::isSubmit('id_supply_order_detail'))
		{
			$this->identifier = 'id_supply_order_receipt_history';
			$this->table = 'supply_order_receipt_history';
			$this->display = 'list';
			$this->lang = false;
			$lang_id = (int)$this->context->language->id;
			$id_supply_order_detail = (int)Tools::getValue('id_supply_order_detail');

			unset($this->fieldsDisplay);
			$this->fieldsDisplay = array(
				'date_add' => array(
					'title' => $this->l('Last update'),
					'width' => 50,
					'align' => 'left',
					'type' => 'datetime',
					'havingFilter' => true
				),
				'employee' => array(
					'title' => $this->l('Employee'),
					'width' => 100,
					'align' => 'left',
					'havingFilter' => true
				),
				'quantity' => array(
					'title' => $this->l('Quantity received'),
					'width' => 100,
					'align' => 'left',
					'havingFilter' => true
				),
			);

			// loads history of the given order
			unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
			$this->_select = 'CONCAT(a.`employee_lastname`, \' \', a.`employee_firstname`) as employee';
			$this->_where = 'AND a.`id_supply_order_detail` = '.(int)$id_supply_order_detail;

			// gets list and forces no limit clause in the request
			$this->getList($lang_id, 'date_add', 'DESC', 0, false, false);

			// renders list
			$helper = new HelperList();
			$helper->no_link = true;
			$helper->show_toolbar = false;
			$helper->toolbar_fix = false;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			$helper->colorOnBackground = true;
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}

		die;
	}

	/**
	 * method call when ajax request is made for search product to add to the order
	 * @TODO - Update this method to retreive the reference, ean13, upc corresponding to a product attribute
	 */
	public function	ajaxProcessSearchProduct()
	{
		// Get the search pattern
		$pattern = pSQL(Tools::getValue('q', false));

		if (!$pattern || $pattern == '' || strlen($pattern) < 1)
			die();

		// get supplier id
		$id_supplier = (int)Tools::getValue('id_supplier', false);

		// get lang from context
		$id_lang = (int)Context::getContext()->language->id;

		$query = new DbQuery();
		$query->select('
			CONCAT(p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\')) as id,
			IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
			IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
			IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc,
			md5(CONCAT(\''._COOKIE_KEY_.'\', p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\'))) as checksum,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as name
		');

		$query->from('product p');

		$query->innerJoin('product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = '.$id_lang.')');
		$query->leftJoin('product_attribute pa ON (pa.id_product = p.id_product)');
		$query->leftJoin('product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)');
		$query->leftJoin('attribute atr ON (atr.id_attribute = pac.id_attribute)');
		$query->leftJoin('attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang.')');
		$query->leftJoin('attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang.')');

		$query->where('pl.name LIKE \'%'.$pattern.'%\' OR p.reference LIKE \'%'.$pattern.'%\'');
		$query->where('p.id_product NOT IN (SELECT pd.id_product FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))');
		$query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');

		if ($id_supplier)
			$query->where('p.id_supplier = '.$id_supplier);

		$query->groupBy('pa.id_product_attribute');

		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		if ($items)
			die(Tools::jsonEncode($items));

		die();
	}

	/**
	 * @see AdminController::renderView()
	 */
	public function renderView()
	{
		$this->show_toolbar = true;
		$this->toolbar_fix = false;
		$this->table = 'supply_order_detail';
		$this->identifier = 'id_supply_order_detail';
	 	$this->className = 'SupplyOrderDetail';
	 	$this->colorOnBackground = false;
		$this->lang = false;
		$this->list_simple_header = true;
		$this->list_no_link = true;

		// gets the id supplier to view
		$id_supply_order = (int)Tools::getValue('id_supply_order');

		// gets global order information
		$supply_order = new SupplyOrder((int)$id_supply_order);

		if (Validate::isLoadedObject($supply_order))
		{
			if (!$supply_order->is_template)
				$this->displayInformation($this->l('This interface allows you to display detailed informations on your order.').'<br />');
			else
				$this->displayInformation($this->l('This interface allows you to display detailed informations on your template of order.').'<br />');

			$lang_id = (int)$supply_order->id_lang;

			// just in case..
			unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);

			// gets all information on the products ordered
			$this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;

			// gets the list ordered by price desc, without limit
			$this->getList($lang_id, 'price_te', 'DESC', 0, false, false);

			// gets the currency used in this order
			$currency = new Currency($supply_order->id_currency);

			// gets the warehouse where products will be received
			$warehouse = new Warehouse($supply_order->id_warehouse);

			// sets toolbar title with order reference
			if (!$supply_order->is_template)
				$this->toolbar_title = sprintf($this->l('Details on supply order #%s'), $supply_order->reference);
			else
				$this->toolbar_title = sprintf($this->l('Details on supply order template #%s'), $supply_order->reference);
			// re-defines fieldsDisplay
			$this->fieldsDisplay = array(
				'supplier_reference' => array(
					'title' => $this->l('Supplier Reference'),
					'align' => 'center',
					'width' => 120,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'reference' => array(
					'title' => $this->l('Reference'),
					'align' => 'center',
					'width' => 120,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'ean13' => array(
					'title' => $this->l('EAN13'),
					'align' => 'center',
					'width' => 100,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'upc' => array(
					'title' => $this->l('UPC'),
					'align' => 'center',
					'width' => 100,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'name' => array(
					'title' => $this->l('Name'),
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'unit_price_te' => array(
					'title' => $this->l('Unit price (te)'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
				'quantity_expected' => array(
					'title' => $this->l('Quantity'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'price_te' => array(
					'title' => $this->l('Price (te)'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
				'discount_rate' => array(
					'title' => $this->l('Discount rate'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'suffix' => '%',
				),
				'discount_value_te' => array(
					'title' => $this->l('Discount value (te)'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
				'price_with_discount_te' => array(
					'title' => $this->l('Price with product discount (te)'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
				'tax_rate' => array(
					'title' => $this->l('Tax rate'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'suffix' => '%',
				),
				'tax_value' => array(
					'title' => $this->l('Tax value'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
				'price_ti' => array(
					'title' => $this->l('Price (ti)'),
					'align' => 'right',
					'width' => 80,
					'orderby' => false,
					'filter' => false,
					'search' => false,
					'type' => 'price',
					'currency' => true,
				),
			);

			//some staff before render list
			foreach ($this->_list as &$item)
			{
				$item['discount_rate'] = Tools::ps_round($item['discount_rate'], 4);
				$item['tax_rate'] = Tools::ps_round($item['tax_rate'], 4);
				$item['id_currency'] = $currency->id;
			}

			// renders list
			$helper = new HelperList();
			$this->setHelperDisplay($helper);
			$helper->actions = array();
			$helper->show_toolbar = false;

			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			// display these global order informations
			$this->tpl_view_vars = array(
				'supply_order_detail_content' => $content,
				'supply_order_warehouse' => (Validate::isLoadedObject($warehouse) ? $warehouse->name : ''),
				'supply_order_reference' => $supply_order->reference,
				'supply_order_supplier_name' => $supply_order->supplier_name,
				'supply_order_creation_date' => Tools::displayDate($supply_order->date_add, $lang_id, false),
				'supply_order_last_update' => Tools::displayDate($supply_order->date_upd, $lang_id, false),
				'supply_order_expected' => Tools::displayDate($supply_order->date_delivery_expected, $lang_id, false),
				'supply_order_discount_rate' => Tools::ps_round($supply_order->discount_rate, 2),
				'supply_order_total_te' => Tools::displayPrice($supply_order->total_te, $currency),
				'supply_order_discount_value_te' => Tools::displayPrice($supply_order->discount_value_te, $currency),
				'supply_order_total_with_discount_te' => Tools::displayPrice($supply_order->total_with_discount_te, $currency),
				'supply_order_total_tax' => Tools::displayPrice($supply_order->total_tax, $currency),
				'supply_order_total_ti' => Tools::displayPrice($supply_order->total_ti, $currency),
				'supply_order_currency' => $currency,
				'is_template' => $supply_order->is_template,
			);
		}

		return parent::renderView();
	}

	/**
	 * Callback used to display custom content for a given field
	 * @param int $id_supply_order
	 * @param string $tr
	 * @return string $content
	 */
	public function printExportIcons($id_supply_order, $tr)
	{
		$supply_order = new SupplyOrder((int)$id_supply_order);

		if (!Validate::isLoadedObject($supply_order))
			return;

		$supply_order_state = new SupplyOrderState($supply_order->id_supply_order_state);
		if (!Validate::isLoadedObject($supply_order_state))
			return;

		$content = '<span style="width:20px; margin-right:5px;">';
		if ($supply_order_state->editable == false)
			$content .= '<a href="pdf.php?id_supply_order='.(int)$supply_order->id.'" title="Export as PDF"><img src="../img/admin/pdf.gif" alt="invoice" /></a>';
		else
			$content .= '-';
		$content .= '</span>';

		$content .= '<span style="width:20px">';
		if ($supply_order_state->enclosed == true && $supply_order_state->receipt_state == true)
			$content .= '<a href="'.$this->context->link->getAdminLink('AdminSupplyOrders').'&amp;id_supply_order='.(int)$supply_order->id.'&csv_order_details" title="Export as CSV"><img src="../img/admin/excel_file.png" alt="" /></a>';
		else
			$content .= '-';
		$content .= '</span>';


		return $content;
	}

	/**
	 * Assigns default actions in toolbar_btn smarty var, if they are not set.
	 * uses override to specifically add, modify or remove items
	 * @see AdminSupplier::initToolbar()
	 */
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'update_order_state':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

			case 'update_receipt':
				// Default cancel button - like old back link
				if (!isset($this->no_back) || $this->no_back == false)
				{
					$back = Tools::safeOutput(Tools::getValue('back', ''));
					if (empty($back))
						$back = self::$currentIndex.'&token='.$this->token;

					$this->toolbar_btn['cancel'] = array(
						'href' => $back,
						'desc' => $this->l('Cancel')
					);
				}
			break;

			case 'add':
			case 'edit':
				$this->toolbar_btn['save-and-stay'] = array(
					'href' => '#',
					'desc' => $this->l('Save and stay')
				);
			default:
				parent::initToolbar();
		}
	}

	/**
	 * Overrides AdminController::afterAdd()
	 * @see AdminController::afterAdd()
	 * @param ObjectModel $object
	 * @return bool
	 */
	public function afterAdd($object)
	{
		$this->object = $object;
		return true;
	}

	/**
	 * Loads products which quantity (hysical quantity) is equal or less than $threshold
	 * @param int $threshold
	 */
	protected function loadProducts($threshold)
	{
		// if there is already an order
		if (Tools::getValue('id_supply_order'))
			$supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));
		else // else, we just created a new order
			$supply_order = $this->object;

		// if order is not valid, return;
		if (!Validate::isLoadedObject($supply_order))
			return;

		// resets products if needed
		if (Tools::getValue('id_supply_order'))
			$supply_order->resetProducts();

		// gets products
		$query = new DbQuery();
		$query->select('ps.id_product,
					    ps.id_product_attribute,
					    ps.product_supplier_reference as supplier_reference,
					    ps.product_supplier_price_te as unit_price_te,
					    ps.id_currency,
					    IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
						IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
						IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc');
		$query->from('product_supplier ps');
		$query->leftJoin('stock s ON
						  (
						  	s.id_product = ps.id_product
						  	AND
						  	s.id_product_attribute = ps.id_product_attribute
						  )');
		$query->innerJoin('warehouse_product_location wpl ON
						   (
						   	wpl.id_product = ps.id_product
							AND
							wpl.id_product_attribute = ps.id_product_attribute
							AND
							wpl.id_warehouse = '.(int)$supply_order->id_warehouse.'
						   )');
		$query->leftJoin('product p ON (p.id_product = ps.id_product)');
		$query->leftJoin('product_attribute pa ON
						  (
						  	pa.id_product_attribute = ps.id_product_attribute
						  	AND
						  	p.id_product = ps.id_product
						  )');
		$query->where('ps.id_supplier = '.(int)$supply_order->id_supplier);

		// gets items
		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		// loads order currency
		$order_currency = new Currency($supply_order->id_ref_currency);
		if (!Validate::isLoadedObject($order_currency))
			return;

		$manager = StockManagerFactory::getManager();
		foreach ($items as $item)
		{
			if ($manager->getProductRealQuantities($item['id_product'], $item['id_product_attribute'], $supply_order->id_warehouse, true) <= $threshold)
			{
				// sets supply_order_detail
				$supply_order_detail = new SupplyOrderDetail();
				$supply_order_detail->id_supply_order = $supply_order->id;
				$supply_order_detail->id_currency = $order_currency->id;
				$supply_order_detail->id_product = $item['id_product'];
				$supply_order_detail->id_product_attribute = $item['id_product_attribute'];
				$supply_order_detail->reference = $item['reference'];
				$supply_order_detail->supplier_reference = $item['supplier_reference'];
				$supply_order_detail->name = Product::getProductName($item['id_product'], $item['id_product_attribute'], $supply_order->id_lang);
				$supply_order_detail->ean13 = $item['ean13'];
				$supply_order_detail->upc = $item['upc'];
				$supply_order_detail->quantity_expected = (int)$threshold;
				$supply_order_detail->exchange_rate = $order_currency->conversion_rate;

				$product_currency = new Currency($item['id_currency']);
				if (Validate::isLoadedObject($product_currency))
					$supply_order_detail->unit_price_te = Tools::convertPriceFull($item['unit_price_te'], $order_currency, $product_currency);
				else
					$supply_order_detail->unit_price_te = 0;

				$supply_order_detail->save();
			}
		}
	}

	/**
	 * Overrides AdminController::beforeAdd()
	 * @see AdminController::beforeAdd()
	 * @param ObjectModel $object
	 */
	public function beforeAdd($object)
	{
		if (Tools::isSubmit('is_template'))
			$object->is_template = 1;

		return true;
	}

	/**
	 * Helper function for AdminSupplyOrdersController::postProcess()
	 * @see AdminSupplyOrdersController::postProcess()
	 */
	protected function postProcessCopyFromTemplate()
	{
		// gets SupplyOrder and checks if it is valid
		$id_supply_order = (int)Tools::getValue('id_supply_order');
		$supply_order = new SupplyOrder($id_supply_order);
		if (!Validate::isLoadedObject($supply_order))
			$this->_errors[] = Tools::displayError($this->l('Could not copy this template.'));

		// gets SupplyOrderDetail
		$entries = $supply_order->getEntriesCollection($supply_order->id_lang);

		// updates SupplyOrder so that it is not a template anymore
		$supply_order->is_template = 0;
		$supply_order->id = (int)0;
		$supply_order->save();

		// copies SupplyOrderDetail
		foreach ($entries as $entry)
		{
			$entry->id_supply_order = $supply_order->id;
			$entry->id = (int)0;
			$entry->save();
		}

		// redirect when done
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
		$redirect = self::$currentIndex.'&token='.$token;
		$this->redirect_after = $redirect.'&conf=19';
	}

	/**
	 * Gets the current warehouse used
	 *
	 * @return int id_warehouse
	 */
	private function getCurrentWarehouse()
	{
		static $warehouse = 0;

		if ($warehouse == 0)
		{
			$warehouse = -1; // all warehouses
			if ((int)Tools::getValue('id_warehouse'))
				$warehouse = (int)Tools::getValue('id_warehouse');
		}
		return $warehouse;
	}

	/**
	 * Gets the current filter used
	 *
	 * @return int status
	 */
	private function getFilterStatus()
	{
		static $status = 0;

		$status = 0;
		if (Tools::getValue('filter_status') === 'on')
			$status = 1;

		return $status;
	}
}
