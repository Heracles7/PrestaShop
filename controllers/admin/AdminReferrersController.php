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

if (!defined('_PS_ADMIN_DIR_')) define('_PS_ADMIN_DIR_', getcwd().'/..');

if (Tools::getValue('token') == Tools::getAdminToken('AdminReferrers'.(int)Tab::getIdFromClassName('AdminReferrers').(int)Tools::getValue('id_employee')))
{
	if (Tools::isSubmit('ajaxProductFilter'))
		Referrer::getAjaxProduct(
			(int)Tools::getValue('id_referrer'),
			(int)Tools::getValue('id_product'),
			new Employee((int)Tools::getValue('id_employee'))
		);
	else if (Tools::isSubmit('ajaxFillProducts'))
	{
		$json_array = array();
		$result = Db::getInstance()->executeS('
			SELECT p.id_product, pl.name
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl
				ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)Tools::getValue('id_lang').')
			'.(Tools::getValue('filter') != 'undefined' ? 'WHERE name LIKE "%'.pSQL(Tools::getValue('filter')).'%"' : '')
		);

		foreach ($result as $row)
			$json_array[] = '{id_product:'.(int)$row['id_product'].',name:\''.addslashes($row['name']).'\'}';

		die ('['.implode(',', $json_array).']');
	}
}

class AdminReferrersControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'referrer';
		$this->className = 'Referrer';
		$this->fieldsDisplay = array(
			'id_referrer' => array(
				'title' => $this->l('ID'),
				'width' => 25,
				'align' => 'center'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 80
			),
			'cache_visitors' => array(
				'title' => $this->l('Visitors'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_visits' => array(
				'title' => $this->l('Visits'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_pages' => array(
				'title' => $this->l('Pages'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_registrations' => array(
				'title' => $this->l('Reg.'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_orders' => array(
				'title' => $this->l('Ord.'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_sales' => array(
				'title' => $this->l('Sales'),
				'width' => 80,
				'align' => 'right',
				'prefix' => '<b>',
				'suffix' => '</b>',
				'price' => true
			),
			'cart' => array(
				'title' => $this->l('Avg. cart'),
				'width' => 50,
				'align' => 'right',
				'price' => true
			),
			'cache_reg_rate' => array(
				'title' => $this->l('Reg. rate'),
				'width' => 30,
				'align' => 'center'
			),
			'cache_order_rate' => array(
				'title' => $this->l('Order rate'),
				'width' => 30,
				'align' => 'center'
			),
			'fee0' => array(
				'title' => $this->l('Click'),
				'width' => 30,
				'align' => 'right',
				'price' => true
			),
			'fee1' => array(
				'title' => $this->l('Base'),
				'width' => 30,
				'align' => 'right',
				'price' => true
			),
			'fee2' => array(
				'title' => $this->l('Percent'),
				'width' => 30,
				'align' => 'right',
				'price' => true
			)
		);

		parent::__construct();
	}

	public function initList()
	{
		// Display list Referrers:
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_select = 'SUM(sa.cache_visitors) AS cache_visitors, SUM(sa.cache_visits) AS cache_visits, SUM(sa.cache_pages) AS cache_pages,
							SUM(sa.cache_registrations) AS cache_registrations, SUM(sa.cache_orders) AS cache_orders, SUM(sa.cache_sales) AS cache_sales,
							IF(sa.cache_orders > 0, ROUND(sa.cache_sales/sa.cache_orders, 2), 0) as cart, (sa.cache_visits*click_fee) as fee0,
							(sa.cache_orders*base_fee) as fee1, (sa.cache_sales*percent_fee/100) as fee2';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'referrer_shop` sa
				ON (sa.'.$this->identifier.' = a.'.$this->identifier.' AND sa.id_shop IN ('.implode(', ', $this->context->shop->getListOfID()).'))';

		$this->_group = 'GROUP BY sa.id_referrer';

		$this->tpl_list_vars = array(
			'enable_calendar' => $this->enableCalendar(),
			'calendar_form' => $this->displayCalendar(),
			'settings_form' => $this->displaySettings()
		);

		return parent::initList();
	}

	public function initForm()
	{
		$uri = Tools::getHttpHost(true, true).__PS_BASE_URI__;

		$this->fields_form[0] = array('form' => array(
			'legend' => array(
				'title' => $this->l('Affiliate'),
				'image' => '../img/admin/affiliation.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 20,
					'required' => true
				),
				array(
					'type' => 'password',
					'label' => $this->l('Password:'),
					'name' => 'passwd',
					'size' => 20,
					'desc' => $this->l('Leave blank if no change')
				)
			),
			'desc' => array(
				$this->l('Affiliates can access their own data with this name and password.'),
				$this->l('Front access:').' <a href="'.$uri.'modules/trackingfront/stats.php" style="font-style: italic;">'.$uri.'modules/trackingfront/stats.php</a>'
			)
		));

		$this->fields_form[1] = array('form' => array(
			'legend' => array(
				'title' => $this->l('Commission plan'),
				'image' => '../img/admin/money.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Click fee:'),
					'name' => 'click_fee',
					'size' => 8,
					'desc' => $this->l('Fee given for each visit.')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Base fee:'),
					'name' => 'base_fee',
					'size' => 8,
					'desc' => $this->l('Fee given for each order placed.')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Percent fee:'),
					'name' => 'percent_fee',
					'size' => 8,
					'desc' => $this->l('Percent of the sales.')
				)
			)
		));

		if (Shop::isFeatureActive())
		{
			$this->fields_form[1]['form']['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
				'values' => Shop::getTree()
			);
		}

		$this->fields_form[2] = array('form' => array(
			'legend' => array(
				'title' => $this->l('Technical information - Simple mode'),
				'image' => '../img/admin/affiliation.png'
			),
			'help' => true,
			'input' => array(
				array(
					'type' => 'textarea',
					'label' => $this->l('Include:'),
					'name' => 'http_referer_like',
					'cols' => 40,
					'rows' => 1,
					'h3' => $this->l('HTTP referrer')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Exclude:'),
					'name' => 'http_referer_like_not',
					'cols' => 40,
					'rows' => 1
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Include:'),
					'name' => 'request_uri_like',
					'cols' => 40,
					'rows' => 1,
					'h3' => $this->l('Request Uri')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Exclude:'),
					'name' => 'request_uri_like_not',
					'cols' => 40,
					'rows' => 1
				)
			),
			'desc' => $this->l('If you know how to use MySQL regular expressions, you can use the').' 
					<a style="cursor: pointer; font-weight: bold;" onclick="$(\'#tracking_expert\').slideToggle();">'.$this->l('expert mode').'.</a>',
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		));

		$this->fields_form[3] = array('form' => array(
			'legend' => array(
				'title' => $this->l('Technical information - Expert mode'),
				'image' => '../img/admin/affiliation.png'
			),
			'input' => array(
				array(
					'type' => 'textarea',
					'label' => $this->l('Include:'),
					'name' => 'http_referer_regexp',
					'cols' => 40,
					'rows' => 1,
					'h3' => $this->l('HTTP referrer')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Exclude:'),
					'name' => 'http_referer_regexp_not',
					'cols' => 40,
					'rows' => 1
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Include:'),
					'name' => 'request_uri_regexp',
					'cols' => 40,
					'rows' => 1,
					'h3' => $this->l('Request Uri')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Exclude:'),
					'name' => 'request_uri_regexp_not',
					'cols' => 40,
					'rows' => 1
				)
			)
		));

		$this->multiple_fieldsets = true;

		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_value = array(
			'click_fee' => number_format((float)($this->getFieldValue($obj, 'click_fee')), 2),
			'base_fee' => number_format((float)($this->getFieldValue($obj, 'base_fee')), 2),
			'percent_fee' => number_format((float)($this->getFieldValue($obj, 'percent_fee')), 2),
			'http_referer_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like'), ENT_COMPAT, 'UTF-8')),
			'http_referer_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like_not'), ENT_COMPAT, 'UTF-8')),
			'request_uri_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like'), ENT_COMPAT, 'UTF-8')),
			'request_uri_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like_not'), ENT_COMPAT, 'UTF-8'))
		);

		$this->tpl_form_vars = array('uri' => $uri);

		return parent::initForm();
	}

	public function displayCalendar($action = null, $table = null, $identifier = null, $id = null)
	{
		return AdminStatsTabController::displayCalendarForm(array(
			'Calendar' => $this->l('Calendar'),
			'Day' => $this->l('Today'),
			'Month' => $this->l('Month'),
			'Year' => $this->l('Year')
		), $this->token, $action, $table, $identifier, $id);
	}

	public function displaySettings()
	{
		if (!Tools::isSubmit('viewreferrer'))
		{
			$tpl = $this->context->smarty->createTemplate($this->tpl_folder.'form_settings.tpl');

			$tpl->assign(array(
				'current' => self::$currentIndex,
				'token' => $this->token,
				'tracking_dt' => (int)Tools::getValue('tracking_dt', Configuration::get('TRACKING_DIRECT_TRAFFIC'))
			));

			return $tpl->fetch();
		}
	}

	private function enableCalendar()
	{
		return (!Tools::isSubmit('add'.$this->table) && !Tools::isSubmit('submitAdd'.$this->table) && !Tools::isSubmit('update'.$this->table));
	}

	public function postProcess()
	{
		if ($this->enableCalendar())
		{
			$calendar_tab = new AdminStatsController();
			$calendar_tab->postProcess();
		}

		if (Tools::isSubmit('submitSettings'))
			if ($this->tabAccess['edit'] === '1')
				if (Configuration::updateValue('TRACKING_DIRECT_TRAFFIC', (int)Tools::getValue('tracking_dt')))
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.Tools::getValue('token'));

		if (ModuleGraph::getDateBetween() != Configuration::get('PS_REFERRERS_CACHE_LIKE') || Tools::isSubmit('submitRefreshCache'))
			Referrer::refreshCache();
		if (Tools::isSubmit('submitRefreshIndex'))
			Referrer::refreshIndex();

		return parent::postProcess();
	}

	public function initView()
	{
		$referrer = new Referrer((int)Tools::getValue('id_referrer'));

		$display_tab = array(
			'uniqs' => $this->l('Unique visitors'),
			'visitors' => $this->l('Visitors'),
			'visits' => $this->l('Visits'),
			'pages' => $this->l('Pages viewed'),
			'registrations' => $this->l('Registrations'),
			'orders' => $this->l('Orders'),
			'sales' => $this->l('Sales'),
			'reg_rate' => $this->l('Registration rate'),
			'order_rate' => $this->l('Order rate'),
			'click_fee' => $this->l('Click fee'),
			'base_fee' => $this->l('Base fee'),
			'percent_fee' => $this->l('Percent fee'));

		$this->tpl_view_vars = array(
			'enable_calendar' => $this->enableCalendar(),
			'calendar_form' => $this->displayCalendar($this->action, $this->table, $this->identifier, (int)Tools::getValue($this->identifier)),
			'referrer' => new Referrer((int)Tools::getValue('id_referrer')),
			'display_tab' => $display_tab,
			'id_employee' => (int)$this->context->employee->id,
			'id_lang' => (int)$this->context->language->id
		);

		return parent::initView();
	}
}


