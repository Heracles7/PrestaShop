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
*  @version  Release: $Revision: 9565 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminSupplierOrdersControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'supplier_order_state';
	 	$this->className = 'SupplierOrderState';
		$this->lang = true;

		$this->fieldsDisplay = array(
			'name' => array(
				'title' => $this->l('Name'),
				'color' => 'color',
			),
			'editable' => array(
				'title' => $this->l('Editable?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'delivery_note' => array(
				'title' => $this->l('Delivery note available?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'pending_receipt' => array(
				'title' => $this->l('Is a pending receipt state?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'receipt_state' => array(
				'title' => $this->l('Is a delivery state?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'enclosed' => array(
				'title' => $this->l('Is an enclosed order state?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
		);

		parent::__construct();
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		parent::init();

		if (Tools::isSubmit('addsupplier_order') ||
			Tools::isSubmit('submitAddsupplier_order') ||
			(Tools::isSubmit('updatesupplier_order') && Tools::isSubmit('id_supplier_order')))
		{
			// override table, lang, className and identifier for the current controller
		 	$this->table = 'supplier_order';
		 	$this->className = 'SupplierOrder';
		 	$this->identifier = 'id_supplier_order';
		 	$this->lang = false;

			$this->action = 'new';
			$this->display = 'add';

			if (Tools::isSubmit('updatesupplier_order'))
				if ($this->tabAccess['edit'] === '1')
					$this->display = 'edit';
				else
					$this->_errors[] = Tools::displayError($this->l('You do not have permission to edit here.'));
		}

		if (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supplier_order'))
		{
			// change the display type in order to add specific actions to
			$this->display = 'update_receipt';

			// display correct toolBar
			$this->initToolbar();
		}
	}

	/**
	 * AdminController::initForm() override
	 * @see AdminController::initForm()
	 */
	public function initForm()
	{
		if (Tools::isSubmit('addsupplier_order_state') ||
			Tools::isSubmit('updatesupplier_order_state') ||
			Tools::isSubmit('submitAddsupplier_order_state') ||
			Tools::isSubmit('submitUpdatesupplier_order_state'))
		{
			$this->fields_form = array(
				'legend' => array(
					'title' => $this->l('Supplier Order State'),
					'image' => '../img/admin/edit.gif'
				),
				'input' => array(
					array(
						'type' => 'text',
						'lang' => true,
						'attributeLang' => 'name',
						'label' => $this->l('Name:'),
						'name' => 'name',
						'size' => 50,
						'required' => true
					),
					array(
						'type' => 'color',
						'label' => $this->l('Back office color:'),
						'name' => 'color',
						'size' => 20,
						'p' => $this->l('Back office background will be displayed in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")'
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Is the order editable in this state?:'),
						'name' => 'editable',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
						'p' => $this->l('You have to define if it is possible to edit the order in this state.
										An editable order is an order not valid to send to the supplier.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Is the delivery note function is available in this state?:'),
						'name' => 'delivery_note',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
						'p' => $this->l('You have to define if it is possible to generate the delivery note of the order in this state.
										The order has to be valid to use this function.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('This state corresponds to a delivery state ?:'),
						'name' => 'receipt_state',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
						'p' => $this->l('You have to define if this state correspond to a product receipt on this order (partial or complete).
										This permit to know if the concerned products have to be added in stock.')
					),
					array(
						'type' => 'radio',
						'label' => $this->l('This state corresponds to a product pending receipt ?:'),
						'name' => 'pending_receipt',
						'required' => true,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
						'p' => $this->l('You have to define if some products are pending receipt in this state.')
					),
				),
				'submit' => array(
					'title' => $this->l('   Save   '),
					'class' => 'button'
				)
			);

			if (Tools::isSubmit('addsupplier_order_state'))
				$this->toolbar_title = $this->l('Stock : Add Supplier order state');
			else
			{
				$this->toolbar_title = $this->l('Stock : Update Supplier order state');

				$id_supplier_order_state = Tools::getValue('id_supplier_order_state', 0);

				// only some fields are editable for initial states
				if (in_array($id_supplier_order_state, array(1, 2, 3, 4, 5, 6)))
				{
					$this->fields_form = array(
						'legend' => array(
							'title' => $this->l('Supplier Order State'),
							'image' => '../img/admin/edit.gif'
						),
						'input' => array(
							array(
								'type' => 'text',
								'lang' => true,
								'attributeLang' => 'name',
								'label' => $this->l('Name:'),
								'name' => 'name',
								'size' => 50,
								'required' => true
							),
							array(
								'type' => 'color',
								'label' => $this->l('Back office color:'),
								'name' => 'color',
								'size' => 20,
								'p' => $this->l('Back office background will be displayed in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")'
							),
						),
						'submit' => array(
							'title' => $this->l('   Save   '),
							'class' => 'button'
						)
					);
				}
			}

			return parent::initForm();
		}

		if (Tools::isSubmit('addsupplier_order') ||
			Tools::isSubmit('updatesupplier_order') ||
			Tools::isSubmit('submitAddsupplier_order') ||
			Tools::isSubmit('submitUpdatesupplier_order'))
		{

			if (Tools::isSubmit('addsupplier_order') ||	Tools::isSubmit('submitAddsupplier_order'))
				$this->toolbar_title = $this->l('Stock : Create new supplier order');

			if (Tools::isSubmit('updatesupplier_order') || Tools::isSubmit('submitUpdatesupplier_order'))
				$this->toolbar_title = $this->l('Stock : Manage supplier order');

			$this->addJqueryUI('ui.datepicker');

			//get warehouses list
			$warehouses = Warehouse::getWarehouseList(true);

			//get currencies list
			$currencies = Currency::getCurrencies();

			//get suppliers list
			$suppliers = Supplier::getSuppliers();

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
						'p' => $this->l('This is the reference of your order.'),
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
						'p' => $this->l('Select the supplier you are buying product from'),
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
						'p' => $this->l('Select the warehouse where you want the order to be sent to.'),
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
						'p' => $this->l('The currency of the order'),
						'hint' => $this->l('Be careful ! When changing this field, all products already added to the order will be removed.')
					),
					array(
						'type' => 'date',
						'label' => $this->l('Delivery date:'),
						'name' => 'date_delivery_expected',
						'size' => 20,
						'required' => true,
						'p' => $this->l('This is the expected delivery date for this order'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Global discount rate (%):'),
						'name' => 'discount_rate',
						'size' => 7,
						'required' => true,
						'p' => $this->l('This is the global discount rate in percents for the order.'),
					),
				),
				'submit' => array(
					'title' => $this->l('   Save order   '),
				)
			);

			//specific discount display
			$this->object->discount_rate = Tools::ps_round($this->object->discount_rate, 4);

			return parent::initForm();
		}

	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		// actions filters on supplier orders list
		if ($this->table == 'supplier_order')
		{
			$nb_items = count($this->_list);

			for ($i = 0; $i < $nb_items; $i++)
			{
				// if the current state doesn't allow order edit, skip the edit action
				if ($this->_list[$i]['editable'] == 0)
					$this->addRowActionSkipList('edit', $this->_list[$i]['id_supplier_order']);
				if ($this->_list[$i]['enclosed'] == 1)
					$this->addRowActionSkipList('changestate', $this->_list[$i]['id_supplier_order']);
				if (1 != $this->_list[$i]['pending_receipt'])
					$this->addRowActionSkipList('updatereceipt', $this->_list[$i]['id_supplier_order']);
			}
		}
	}

	/**
	 * AdminController::initList() override
	 * @see AdminController::initList()
	 */
	public function initList()
	{
		$this->displayInformation($this->l('This interface allows you to manage supplier orders.').'<br />');
		$this->displayInformation($this->l('Also, it allows you to add and edit your own supplier order states.'));

		// access
		if (!($this->tabAccess['add'] === '1'))
			unset($this->toolbar_btn['new']);

		//no link on list rows
		$this->list_no_link = true;

		/*
		 * Manage default list
		 */
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5, 6));

		$this->toolbar_title = $this->l('Stock : Suppliers Orders States');
		$first_list = parent::initList();

		/*
		 * Manage second list
		 */
		// reset actions, toolbar and query vars
		$this->actions = array();
		$this->list_skip_actions = array();
		$this->toolbar_btn = array();
		$this->toolbar_title = '';
		unset($this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		// override table, land, className and identifier for the current controller
	 	$this->table = 'supplier_order';
	 	$this->className = 'SupplierOrder';
	 	$this->identifier = 'id_supplier_order';
	 	$this->lang = false;

		$this->addRowAction('updatereceipt');
		$this->addRowAction('changestate');
		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('details');

	 	// test if a filter is applied for this list
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		// test if a filter reset request is required for this list
		if (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		else
			$this->action = '';

		// redifine fields display
		$this->fieldsDisplay = array(
			'reference' => array(
				'title' => $this->l('Order Reference'),
				'width' => 120,
				'havingFilter' => true
			),
			'employee' => array(
				'title' => $this->l('Employee'),
				'havingFilter' => true
			),
			'supplier' => array(
				'title' => $this->l('Supplier'),
				'width' => 200,
				'filter_key' => 's!name'
			),
			'warehouse' => array(
				'title' => $this->l('Warehouse'),
				'width' => 200,
				'filter_key' => 'w!name'
			),
			'state' => array(
				'title' => $this->l('State'),
				'width' => 250,
				'filter_key' => 'stl!name',
				'color' => 'color',
			),
			'date_add' => array(
				'title' => $this->l('Creation date'),
				'width' => 150,
				'align' => 'right',
				'type' => 'datetime',
				'havingFilter' => true,
				'filter_key' => 'a!date_add'
			),
			'date_upd' => array(
				'title' => $this->l('Last modification date'),
				'width' => 150,
				'align' => 'right',
				'type' => 'datetime',
				'havingFilter' => true,
				'filter_key' => 'a!date_upd'
			),
			'date_delivery_expected' => array(
				'title' => $this->l('Delivery date'),
				'width' => 150,
				'align' => 'right',
				'type' => 'datetime',
				'havingFilter' => true,
				'filter_key' => 'a!date_delivery_expected'
			),
			'id_pdf' => array(
				'title' => $this->l('PDF'),
				'width' => 50,
				'callback' => 'printPDFIcons',
				'orderby' => false,
				'search' => false
			),
		);

		// make new query
		$this->_select = '
			CONCAT(e.lastname, \' \', e.firstname) AS employee,
			s.name AS supplier,
			w.name AS warehouse,
			stl.name AS state,
			st.delivery_note,
			st.editable,
			st.enclosed,
			st.receipt_state,
			st.pending_receipt,
			st.color AS color,
			a.id_supplier_order as id_pdf';

		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'supplier_order_state_lang` stl ON
						(
							a.id_supplier_order_state = stl.id_supplier_order_state
							AND stl.id_lang = '.(int)$this->context->language->id.'
						)
						LEFT JOIN `'._DB_PREFIX_.'supplier_order_state` st ON a.id_supplier_order_state = st.id_supplier_order_state
						LEFT JOIN `'._DB_PREFIX_.'supplier` s ON a.id_supplier = s.id_supplier
						LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = a.id_warehouse)
						LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)';

		// init the toolbar according to the current list
		$this->initToolbar();

		// generate the second list
		$second_list = parent::initList();

		// reset all query vars
		unset($this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		// return the two lists
		return $second_list.$first_list;
	}

	/**
	 * Init the content of change state action
	 */
	public function initChangeStateContent()
	{
		$id_supplier_order = (int)Tools::getValue('id_supplier_order', 0);

		if ($id_supplier_order <= 0)
		{
			$this->_errors[] = Tools::displayError($this->l('The specified supplier order is not valid'));
			return parent::initContent();
		}

		$supplier_order = new SupplierOrder($id_supplier_order);

		if (!Validate::isLoadedObject($supplier_order))
		{
			$this->_errors[] = Tools::displayError($this->l('The specified supplier order is not valid'));
			return parent::initContent();
		}

		// change the display type in order to add specific actions to
		$this->display = 'update_order_state';
		// overrides parent::initContent();
		$this->initToolbar();

		// given the current state, loads available states
		$states = SupplierOrderState::getSupplierOrderStates($supplier_order->id_supplier_order_state);
		// loads languages
		$this->getlanguages();

		// defines the fields of the form to display
		$this->fields_form[]['form'] = array(
			'legend' => array(
				'title' => $this->l('Supplier Order State'),
				'image' => '../img/admin/cms.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_supplier_order',
				),
				array(
					'type' => 'select',
					'label' => $this->l('New state of the order:'),
					'name' => 'id_supplier_order_state',
					'required' => true,
					'options' => array(
						'query' => $states,
						'id' => 'id_supplier_order_state',
						'name' => 'name'
					),
					'p' => $this->l('Choose the new state of your order')
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
			'id_supplier_order_state' => Tools::getValue('id_supplier', ''),
			'id_supplier_order' => $id_supplier_order,
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
	public function initUpdateSupplierOrderContent()
	{
		$this->addJqueryPlugin('autocomplete');

		// load supplier order
		$id_supplier_order = (int)Tools::getValue('id_supplier_order', null);

		if ($id_supplier_order != null)
		{
			$supplier_order = new SupplierOrder($id_supplier_order);

			$currency = new Currency($supplier_order->id_currency);

			if (Validate::isLoadedObject($supplier_order))
			{
				// load products of this order
				$products = $supplier_order->getEntries();
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
				$this->tpl_form_vars['supplier_id'] = $supplier_order->id_supplier;
				$this->tpl_form_vars['currency'] = $currency;
			}
		}

		$this->tpl_form_vars['content'] = $this->content;
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
		$id_supplier_order = (int)Tools::getValue('id_supplier_order', null);

		// if there is no order to fetch
		if (null == $id_supplier_order)
			return parent::initContent();

		$supplier_order = new SupplierOrder($id_supplier_order);

		// if it's not a valid order
		if (!Validate::isLoadedObject($supplier_order))
			return parent::initContent();

		// re-defines fieldsDisplay
		$this->fieldsDisplay = array(
			'id' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 20,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'p_reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'center',
				'width' => 30,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'p_ean13' => array(
				'title' => $this->l('EAN13'),
				'align' => 'center',
				'width' => 30,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'p_name' => array(
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
		$this->table = 'supplier_order_detail';
		$this->identifier = 'id_supplier_order_detail';
	 	$this->className = 'SupplierOrderDetail';
	 	$this->list_simple_header = true;
	 	$this->list_no_link = true;
	 	$this->bulk_actions = array('Update' => array('text' => $this->l('Update selected'), 'confirm' => $this->l('Update selected items?')));
		$this->addRowAction('details');

		// sets toolbar title with order reference
		$this->toolbar_title = sprintf($this->l('Reception of products for supplier order #%s'), $supplier_order->reference);

	 	// gets lang info
		$this->lang = false;
		$lang_id = (int)$this->context->language->id;

		// gets values corresponding to fieldsDisplay
		$this->_select = '
		a.id_supplier_order_detail as id,
		a.quantity_received as quantity_received,
		a.quantity_expected as quantity_expected,
		IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_left,
		IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_received_today,
		IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as p_name,
		p.reference as p_reference,
		p.ean13 as p_ean13';
		$this->_join = '
		INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = a.id_product AND pl.id_lang = '.$lang_id.')
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = a.id_product)
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = a.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.id_attribute = pac.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')';
		$this->_where = 'AND a.`id_supplier_order` = '.(int)$id_supplier_order;
		$this->_group = 'GROUP BY a.id_supplier_order_detail';

		// gets the list ordered by price desc, without limit
		$this->getList($lang_id, 'quantity_expected', 'DESC', 0, false, false);

		// defines action for POST
		$action = '&id_supplier_order='.$id_supplier_order.'&submitUpdateReceipt';

		// renders list
		$helper = new HelperList();
		$this->setHelperDisplay($helper);
		$helper->override_folder = 'supplier_orders_receipt_history/';
		//$helper->shopLinkType = '';
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
		else if (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supplier_order'))
			$this->initUpdateReceiptContent();
		else if (Tools::isSubmit('viewsupplier_order') && Tools::isSubmit('id_supplier_order'))
		{
			$this->action = 'view';
			$this->display = 'view';
			parent::initContent();
		}
		else if (Tools::isSubmit('updatesupplier_order'))
			$this->initUpdateSupplierOrderContent();
		else
			parent::initContent();
	}

	/**
	 * Ths method manage associated products to the order when updating it
	 */
	public function manageOrderProducts()
	{
		// load supplier order
		$id_supplier_order = (int)Tools::getValue('id_supplier_order', null);

		if ($id_supplier_order != null)
		{
			$supplier_order = new SupplierOrder($id_supplier_order);

			$products_already_in_order = $supplier_order->getEntries();
			$currency = new Currency($supplier_order->id_ref_currency);

			if (Validate::isLoadedObject($supplier_order))
			{
				// tests if the supplier or currency have changed in the supplier order
				$new_supplier_id = (int)Tools::getValue('id_supplier');
				$new_currency_id = (int)Tools::getValue('id_currency');

				if (($new_supplier_id != $supplier_order->id_supplier) ||
					($new_currency_id != $supplier_order->id_currency))
				{
					// resets all products in this order
					$supplier_order->resetProducts();
				}
				else
				{
					// gets all product ids to manage
					$product_ids_str = Tools::getValue('product_ids', null);
					$product_ids = explode('|', $product_ids_str);

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

						// Load / Create supplier order detail
						$entry = new SupplierOrderDetail();
						$id_supplier_order_detail = (int)Tools::getValue('input_id_'.$id, 0);
						if ($id_supplier_order_detail > 0)
						{
							$existing_entry = new SupplierOrderDetail($id_supplier_order_detail);
							if (Validate::isLoadedObject($supplier_order))
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
						$entry->name = Tools::getValue('input_name_'.$id, '');
						$entry->exchange_rate = $currency->conversion_rate;
						$entry->id_currency = $currency->id;
						$entry->id_supplier_order = $supplier_order->id;

						$errors = $entry->validateController();

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
								'reference' => $entry->reference,
								'ean13' => $entry->ean13,
							);

							$error_str = '<ul>';
							foreach ($errors as $e)
								$error_str .= '<li>'.$this->l('field ').$e.'</li>';
							$error_str .= '</ul>';

							$this->_errors[] = Tools::displayError($this->l('Please verify informations of the product: ').$entry->name.' '.$error_str);
						}
						else
							$entry->save();
					}

					//delete products that are not managed anymore
					foreach ($products_already_in_order as $paio)
					{
						$product_ok = false;

						foreach ($product_ids as $id)
						{
							$id_check = $paio['id_product'].'_'.$paio['id_product_attribute'];
							if ($id_check == $id)
								$product_ok = true;
						}

						if ($product_ok === false)
						{
							$entry = new SupplierOrderDetail($paio['id_supplier_order_detail']);
							$entry->delete();
						}
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
		// Checks access
		if (Tools::isSubmit('submitAddsupplier_order') && !($this->tabAccess['add'] === '1'))
			$this->_errors[] = Tools::displayError($this->l('You do not have the required permission to add a supplier order.'));
		if (Tools::isSubmit('submitUpdateReceipt') && !($this->tabAccess['edit'] === '1'))
			$this->_errors[] = Tools::displayError($this->l('You do not have the required permission to edit an order.'));
		// Global checks when add / update a supplier order
		if (Tools::isSubmit('submitAddsupplier_order'))
		{
			$this->action = 'save';

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

			// specify employee
			$_POST['id_employee'] = $this->context->employee->id;

			// specify initial state
			$_POST['id_supplier_order_state'] = 1; //defaut creation state

			// specify global reference currency
			$_POST['id_ref_currency'] = Currency::getDefaultCurrency()->id;

			//specific discount check
			$_POST['discount_rate'] = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('discount_rate', 0));

			// manage each associated product
			$this->manageOrderProducts();
		}

		// Manage state change
		if (Tools::isSubmit('submitChangestate')
			&& Tools::isSubmit('id_supplier_order')
			&& Tools::isSubmit('id_supplier_order_state'))
		{
			if ($this->tabAccess['edit'] != '1')
				$this->_errors[] = Tools::displayError($this->l('You do not have permissions to change order state.'));

			// get state ID
			$id_state = (int)Tools::getValue('id_supplier_order_state', 0);
			if ($id_state <= 0)
				$this->_errors[] = Tools::displayError($this->l('The selected supplier order state is not valid.'));

			// get supplier order ID
			$id_supplier_order = (int)Tools::getValue('id_supplier_order', 0);
			if ($id_supplier_order <= 0)
				$this->_errors[] = Tools::displayError($this->l('The supplier order id is not valid.'));

			if (!count($this->_errors))
			{
				// try to load supplier order
				$supplier_order = new SupplierOrder($id_supplier_order);

				if (Validate::isLoadedObject($supplier_order))
				{
					// get valid available possible states for this order
					$states = SupplierOrderState::getSupplierOrderStates($supplier_order->id_supplier_order_state);

					foreach ($states as $state)
					{
						// if state is valid, change it in the order
						if ($id_state == $state['id_supplier_order_state'])
						{
							// special case of validate state - check if there are products in the order
							if ($supplier_order->isEditable() && !$supplier_order->hasEntries())
								$this->_errors[] = Tools::displayError(
									$this->l('It is not possible to change the state of this order because of no product are present in it')
								);

							if (!count($this->_errors))
							{
								$supplier_order->id_supplier_order_state = $state['id_supplier_order_state'];
								if ($supplier_order->save())
								{
									$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
									$redirect = self::$currentIndex.'&token='.$token;
									Tools::redirectAdmin($redirect.'&conf=5');
								}
							}
						}
					}
				}
				else
					$this->_errors[] = Tools::displayError($this->l('The selected supplier is not valid.'));
			}
		}

		if (Tools::isSubmit('submitUpdateReceipt') && Tools::isSubmit('id_supplier_order'))
			$this->postProcessUpdateReceipt();

		parent::postProcess();
	}

	/**
	 * Helper function for AdminSupplierOrdersController::postProcess()
	 *
	 * @see AdminSupplierOrdersController::postProcess()
	 */
	protected function postProcessUpdateReceipt()
	{
		// gets all box selected
		$rows = Tools::getValue('supplier_order_detailBox');
		if (!$rows)
		{
			$this->_errors[] = Tools::displayError($this->l('You did not select any product to update'));
			return;
		}

		// final array with id_supplier_order_detail and value to update
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

		foreach ($to_update as $id_supplier_order_detail => $quantity)
		{
			$supplier_order_detail = new SupplierOrderDetail($id_supplier_order_detail);
			$supplier_order = new SupplierOrder((int)Tools::getValue('id_supplier_order'));

			if (Validate::isLoadedObject($supplier_order_detail) && Validate::isLoadedObject($supplier_order))
			{
				// checks if quantity is valid
				// It's possible to receive more quantity than expected in case of a shipping error from the supplier
				if (!Validate::isInt($quantity) || $quantity <= 0)
					$this->_errors[] = sprintf(Tools::displayError($this->l('Quantity (%d) for product #%d is not valid')), (int)$quantity, (int)$id_supplier_order_detail);
				else // everything is valid :  updates
				{
					// creates the history
					$supplier_receipt_history = new SupplierOrderReceiptHistory();
					$supplier_receipt_history->id_supplier_order_detail = (int)$id_supplier_order_detail;
					$supplier_receipt_history->id_employee = (int)$this->context->employee->id;
					$supplier_receipt_history->id_supplier_order_state = (int)$supplier_order->id_supplier_order_state;
					$supplier_receipt_history->quantity = (int)$quantity;

					// updates quantity received
					$supplier_order_detail->quantity_received += (int)$quantity;

					// if current state is "Pending receipt", then we sets it to "Order received in part"
					if (3 == $supplier_order->id_supplier_order_state)
						$supplier_order->id_supplier_order_state = 4;

					// Adds to stock
					$warehouse = new Warehouse($supplier_order->id_warehouse);
					if (!Validate::isLoadedObject($warehouse))
					{
						$this->_errors[] = Tools::displayError($this->l('Warehouse could not be loaded'));
						return;
					}
					$manager = StockManagerFactory::getManager();
					$res = $manager->addProduct($supplier_order_detail->id_product,
										 		$supplier_order_detail->id_product_attribute,
										 		$warehouse,
										 		(int)$quantity,
										 		Configuration::get('PS_STOCK_MVT_SUPPLIER_ORDER'),
										 		$supplier_order_detail->unit_price_te,
										 		true,
										 		$supplier_order->id);
					if ($res) // if product has been added
					{
						$supplier_receipt_history->add();
						$supplier_order_detail->save();
						$supplier_order->save();
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
			Tools::redirectAdmin($redirect.'&conf=4');
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

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/helper/list/list_action_supplier_order_receipt.tpl');
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

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/helper/list/list_action_supplier_order_change_state.tpl');
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
			$this->identifier = 'id_supplier_order_history';
			$this->table = 'supplier_order_history';

			$this->display = 'list';
			$this->lang = false;
			// gets current lang id
			$lang_id = (int)$this->context->language->id;
			// gets supplier order id
			$id_supplier_order = (int)Tools::getValue('id');

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
					'title' => $this->l('State'),
					'width' => 100,
					'align' => 'left',
					'havingFilter' => true
				),
			);
			// loads history of the given order
			unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
			$this->_select = '
			a.`date_add` as history_date,
			CONCAT(e.`lastname`, \' \', e.`firstname`) as history_employee,
			sosl.`name` as history_state_name,
			sos.`color` as color';

			$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'supplier_order_state` sos ON (a.`id_state` = sos.`id_supplier_order_state`)
			LEFT JOIN `'._DB_PREFIX_.'supplier_order_state_lang` sosl ON
			(
				a.`id_state` = sosl.`id_supplier_order_state`
				AND sosl.`id_lang` = '.(int)$lang_id.'
			)
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.`id_employee` = a.`id_employee`)';

			$this->_where = 'AND a.`id_supplier_order` = '.(int)$id_supplier_order;
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
			$helper->colorOnBackground = true;
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}
		else if (Tools::isSubmit('id_supplier_order_detail'))
		{
			$this->identifier = 'id_supplier_order_receipt_history';
			$this->table = 'supplier_order_receipt_history';
			$this->display = 'list';
			$this->lang = false;
			$lang_id = (int)$this->context->language->id;
			$id_supplier_order_detail = (int)Tools::getValue('id_supplier_order_detail');

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
			$this->_select = 'CONCAT(e.`lastname`, \' \', e.`firstname`) as employee';
			$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.`id_employee` = a.`id_employee`)';
			$this->_where = 'AND a.`id_supplier_order_detail` = '.(int)$id_supplier_order_detail;

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
	 * @see AdminController::initView()
	 */
	public function initView()
	{
		$this->displayInformation($this->l('This interface allows you to display detailed informations on your order.').'<br />');

		$this->show_toolbar = true;
		$this->toolbar_fix = false;
		$this->table = 'supplier_order_detail';
		$this->identifier = 'id_supplier_order_detail';
	 	$this->className = 'SupplierOrderDetail';
	 	$this->colorOnBackground = false;
		$this->lang = false;
		$this->list_simple_header = true;
		$this->list_no_link = true;
		$lang_id = (int)$this->context->language->id;

		// gets the id supplier to view
		$id_supplier_order = (int)Tools::getValue('id_supplier_order');

		// just in case..
		unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);

		// gets all information on the products ordered
		$this->_select = '
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as p_name,
			p.reference as p_reference,
			p.ean13 as p_ean13';

		$this->_join = '
			INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = a.id_product AND pl.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = a.id_product)
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = a.id_product_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.id_attribute = pac.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')';

		$this->_where = 'AND a.`id_supplier_order` = '.(int)$id_supplier_order;
		$this->_group = 'GROUP BY a.id_product';

		// gets global order information
		$supplier_order = new SupplierOrder((int)$id_supplier_order);

		if (Validate::isLoadedObject($supplier_order))
		{
			// gets the list ordered by price desc, without limit
			$this->getList($lang_id, 'price_te', 'DESC', 0, false, false);

			// gets the currency used in this order
			$currency = new Currency($supplier_order->id_currency);

			// gets the employee in charge of the order
			$employee = new Employee($supplier_order->id_employee);

			// gets the warehouse where products will be received
			$warehouse = new Warehouse($supplier_order->id_warehouse);

			// sets toolbar title with order reference
			$this->toolbar_title = sprintf($this->l('Details on supplier order #%s'), $supplier_order->reference);

			// re-defines fieldsDisplay
			$this->fieldsDisplay = array(
				'p_reference' => array(
					'title' => $this->l('Reference'),
					'align' => 'center',
					'width' => 120,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'p_ean13' => array(
					'title' => $this->l('EAN13'),
					'align' => 'center',
					'width' => 100,
					'orderby' => false,
					'filter' => false,
					'search' => false,
				),
				'p_name' => array(
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
			}

			// renders list
			$helper = new HelperList();
			$this->setHelperDisplay($helper);
			$helper->show_toolbar = false;

			$content = $helper->generateList($this->_list, $this->fieldsDisplay);

			// display these global order informations
			$this->tpl_view_vars = array(
				'supplier_order_detail_content' => $content,
				'supplier_order_employee' => (Validate::isLoadedObject($employee) ? $employee->firstname.' '.$employee->lastname : ''),
				'supplier_order_warehouse' => (Validate::isLoadedObject($warehouse) ? $warehouse->name : ''),
				'supplier_order_reference' => $supplier_order->reference,
				'supplier_order_creation_date' => Tools::displayDate($supplier_order->date_add, $lang_id, true),
				'supplier_order_last_update' => Tools::displayDate($supplier_order->date_upd, $lang_id, true),
				'supplier_order_expected' => Tools::displayDate($supplier_order->date_delivery_expected, $lang_id, true),
				'supplier_order_discount_rate' => Tools::ps_round($supplier_order->discount_rate, 2),
				'supplier_order_total_te' => Tools::displayPrice($supplier_order->total_te, $currency),
				'supplier_order_discount_value_te' => Tools::displayPrice($supplier_order->discount_value_te, $currency),
				'supplier_order_total_with_discount_te' => Tools::displayPrice($supplier_order->total_with_discount_te, $currency),
				'supplier_order_total_tax' => Tools::displayPrice($supplier_order->total_tax, $currency),
				'supplier_order_total_ti' => Tools::displayPrice($supplier_order->total_ti, $currency),
				'supplier_order_currency' => $currency,
			);
		}

		return parent::initView();
	}

	/**
	 * Callback used to display custom content for a given field
	 * @param int $id_supplier_order
	 * @param string $tr
	 * @return string $content
	 */
	public function printPDFIcons($id_supplier_order, $tr)
	{
		$supplier_order = new SupplierOrder((int)$id_supplier_order);

		if (!Validate::isLoadedObject($supplier_order))
			return;

		$supplier_order_state = new SupplierOrderState($supplier_order->id_supplier_order_state);
		if (!Validate::isLoadedObject($supplier_order_state))
			return;

		$content = '<span style="width:20px; margin-right:5px;">';
		if ($supplier_order_state->editable == false) // @TODO Decide what states are allowed
			$content .= '<a href="#"><img src="../img/admin/pdf.gif" alt="invoice" /></a>';
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
}