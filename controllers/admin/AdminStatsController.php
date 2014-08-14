<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminStatsControllerCore extends AdminStatsTabController
{
	public static function getVisits($unique = false, $date_from, $date_to, $granularity = false)
	{
		$visits = ($granularity == false) ? 0 : array();
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$metric = $unique ? 'visitors' : 'visits';
			if ($result = $gapi->requestReportData($granularity ? 'ga:date' : '', 'ga:'.$metric, $date_from, $date_to, null, null, 1, 5000))
				foreach ($result as $row)
					if ($granularity == 'day')
						$visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-$3', $row['dimensions']['date']))] = $row['metrics'][$metric];
					elseif ($granularity == 'month')
					{
						if (!isset($visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))]))
							$visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))] = 0;
						$visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-01', $row['dimensions']['date']))] += $row['metrics'][$metric];
					}
					else
						$visits = $row['metrics'][$metric];
		}
		else
		{
			if ($granularity == 'day')
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`date_add`, 10) as date, COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction().'
				GROUP BY LEFT(`date_add`, 10)');
				foreach ($result as $row)
					$visits[strtotime($row['date'])] = $row['visits'];
			}
			elseif ($granularity == 'month')
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`date_add`, 7) as date, COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction().'
				GROUP BY LEFT(`date_add`, 7)');
				foreach ($result as $row)
					$visits[strtotime($row['date'].'-01')] = $row['visits'];
			}
			else
				$visits = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT('.($unique ? 'DISTINCT id_guest' : '*').') as visits
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction());
		}
		return $visits;
	}
	
	public static function getAbandonedCarts($date_from, $date_to)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT id_guest)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
		AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		'.Shop::addSqlRestriction());
	}
	
	public static function getInstalledModules()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT m.`id_module`)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm'));
	}
	
	public static function getDisabledModules()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm', false).'
		WHERE module_shop.id_module IS NULL OR m.active = 0');
	}
	
	public static function getModulesToUpdate()
	{
		$context = Context::getContext();
		$logged_on_addons = false;
		if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
		&& !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons))
			$logged_on_addons = true;
		$modules = Module::getModulesOnDisk(true, $logged_on_addons, $context->employee->id);
		$upgrade_available = 0;
		foreach ($modules as $km => $module)
			if ($module->installed && isset($module->version_addons) && $module->version_addons) // SimpleXMLElement 
				++$upgrade_available;
		return $upgrade_available;
	}
	
	public static function getPercentProductStock()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(IFNULL(stock.quantity, 0) > 0, 1, 0)) as with_stock, COUNT(*) as products
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Product::sqlStock('p', 'pa').'
		WHERE product_shop.active = 1');
		return round($row['products'] ? 100 * $row['with_stock'] / $row['products'] : 0, 2).'%';
	}
	
	public static function getProductAverageGrossMargin()
	{
		$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG((IFNULL(product_attribute_shop.price, product_shop.price) - IFNULL(product_attribute_shop.wholesale_price, product_shop.wholesale_price)) / IFNULL(product_attribute_shop.price, product_shop.price))
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Shop::addSqlAssociation('product_attribute', 'pa', false).'
		WHERE product_shop.active = 1');
		return round(100 * $value, 2).'%';
	}
	
	public static function getDisabledCategories()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE c.active = 0');
	}
	
	public static function getTotalCategories()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c'));
	}
	
	public static function getDisabledProducts()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE product_shop.active = 0');
	}
	
	public static function getTotalProducts()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p'));
	}
	
	public static function getTotalSales($date_from, $date_to, $granularity = false)
	{
		if ($granularity == 'day')
		{
			$sales = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 10) as date, SUM(total_paid_tax_excl / o.conversion_rate) as sales
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 10)');
			foreach ($result as $row)
				$sales[strtotime($row['date'])] = $row['sales'];
			return $sales;
		}
		elseif ($granularity == 'month')
		{
			$sales = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 7) as date, SUM(total_paid_tax_excl / o.conversion_rate) as sales
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 7)');
			foreach ($result as $row)
				$sales[strtotime($row['date'].'-01')] = $row['sales'];
			return $sales;
		}
		else
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(total_paid_tax_excl / o.conversion_rate)
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o'));
	}
	
	public static function get8020SalesCatalog($date_from, $date_to)
	{
		$distinct_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT od.product_id)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(false, 'o'));
		if (!$distinct_products)
			return '0%';
		return round(100 * $distinct_products / AdminStatsController::getTotalProducts()).'%';
	}

	public static function getOrders($date_from, $date_to, $granularity = false)
	{
		if ($granularity == 'day')
		{
			$orders = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 10) as date, COUNT(*) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 10)');
			foreach ($result as $row)
				$orders[strtotime($row['date'])] = $row['orders'];
			return $orders;
		}
		elseif ($granularity == 'month')
		{
			$orders = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 7) as date, COUNT(*) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 7)');
			foreach ($result as $row)
				$orders[strtotime($row['date'].'-01')] = $row['orders'];
			return $orders;
		}
		else
			$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*) as orders
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o'));

		return $orders;
	}
	
	public static function getEmptyCategories()
	{
		$total = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
		$used = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT cp.id_category)
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON c.id_category = cp.id_category
		'.Shop::addSqlAssociation('category', 'c').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
		return intval($total - $used);
	}
	
	public static function getCustomerMainGender()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(g.id_gender IS NOT NULL, 1, 0)) as total, SUM(IF(type = 0, 1, 0)) as male, SUM(IF(type = 1, 1, 0)) as female, SUM(IF(type = 2, 1, 0)) as neutral
		FROM `'._DB_PREFIX_.'customer` c
		'.Shop::addSqlAssociation('customer', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'gender` g ON c.id_gender = g.id_gender
		WHERE c.active = 1');

		if (!$row['total'])
			return false;
		elseif ($row['male'] > $row['female'] && $row['male'] >= $row['neutral'])
			return array('type' => 'male', 'value' => round(100 * $row['male'] / $row['total']));
		elseif ($row['female'] >= $row['male'] && $row['female'] >= $row['neutral'])
			return array('type' => 'female', 'value' => round(100 * $row['female'] / $row['total']));
		return array('type' => 'neutral', 'value' => round(100 * $row['neutral'] / $row['total']));
	}
	
	public static function getBestCategory($date_from, $date_to)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT ca.`id_category`
		FROM `'._DB_PREFIX_.'category` ca
		LEFT JOIN `'._DB_PREFIX_.'category_product` capr ON ca.`id_category` = capr.`id_category`
		LEFT JOIN (
			SELECT pr.`id_product`, t.`totalPriceSold`
			FROM `'._DB_PREFIX_.'product` pr
			LEFT JOIN (
				SELECT pr.`id_product`,
					IFNULL(SUM(cp.`product_quantity`), 0) AS totalQuantitySold,
					IFNULL(SUM(cp.`product_price` * cp.`product_quantity`), 0) / o.conversion_rate AS totalPriceSold
				FROM `'._DB_PREFIX_.'product` pr
				LEFT OUTER JOIN `'._DB_PREFIX_.'order_detail` cp ON pr.`id_product` = cp.`product_id`
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = cp.`id_order`
				WHERE o.invoice_date BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				GROUP BY pr.`id_product`
			) t ON t.`id_product` = pr.`id_product`
		) t	ON t.`id_product` = capr.`id_product`
		WHERE ca.`level_depth` > 1
		GROUP BY ca.`id_category`
		ORDER BY SUM(t.`totalPriceSold`) DESC');
	}
	
	public static function getMainCountry($date_from, $date_to)
	{
		$total_orders = AdminStatsController::getOrders($date_from, $date_to);
		if (!$total_orders)
			return false;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT a.id_country, COUNT(*) as orders
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction());
		$row['orders'] = round(100 * $row['orders'] / $total_orders, 1);
		return $row;
	}
	
	public static function getAverageCustomerAge()
	{
		$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG(DATEDIFF(NOW(), birthday))
		FROM `'._DB_PREFIX_.'customer` c
		'.Shop::addSqlAssociation('customer', 'c').'
		WHERE active = 1
		AND birthday IS NOT NULL AND birthday != "0000-00-00"');
		return round($value / 365);
	}

	public static function getPendingMessages()
	{
		return CustomerThread::getTotalCustomerThreads('status LIKE "%pending%" OR status = "open"');
	}

	public static function getAverageMessageResponseTime($date_from, $date_to)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT MIN(cm1.date_add) as question, MIN(cm2.date_add) as reply
		FROM `'._DB_PREFIX_.'customer_message` cm1
		INNER JOIN `'._DB_PREFIX_.'customer_message` cm2 ON (cm1.id_customer_thread = cm2.id_customer_thread AND cm1.date_add < cm2.date_add)
		JOIN `'._DB_PREFIX_.'customer_thread` ct ON (cm1.id_customer_thread = ct.id_customer_thread)
		WHERE cm1.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		AND cm1.id_employee = 0 AND cm2.id_employee != 0
		'.Shop::addSqlRestriction().'
		GROUP BY cm1.id_customer_thread');
		$total_questions = $total_replies = $threads = 0;
		foreach ($result as $row)
		{
			++$threads;
			$total_questions += strtotime($row['question']);
			$total_replies += strtotime($row['reply']);
		}
		if (!$threads)
			return 0;
		return round(($total_replies - $total_questions) / $threads / 3600, 1);
	}

	public static function getMessagesPerThread($date_from, $date_to)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(*) as messages
		FROM `'._DB_PREFIX_.'customer_thread` ct
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON (ct.id_customer_thread = cm.id_customer_thread)
		WHERE ct.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction().'
		AND status = "closed"
		GROUP BY ct.id_customer_thread');
		$threads = $messages = 0;
		foreach ($result as $row)
		{
			++$threads;
			$messages += $row['messages'];
		}
		if (!$threads)
			return 0;
		return round($messages / $threads, 1);
	}
	
	public static function getPurchases($date_from, $date_to, $granularity = false)
	{
		if ($granularity == 'day')
		{
			$purchases = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT
				LEFT(`invoice_date`, 10) as date,
				SUM(od.`product_quantity` * IF(
					od.`purchase_supplier_price` > 0,
					od.`purchase_supplier_price` / `conversion_rate`,
					od.`original_product_price` * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
				)) as total_purchase_price
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o').'
			GROUP BY LEFT(`invoice_date`, 10)');
			foreach ($result as $row)
				$purchases[strtotime($row['date'])] = $row['total_purchase_price'];
			return $purchases;
		}
		else
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT SUM(od.`product_quantity` * IF(
				od.`purchase_supplier_price` > 0,
				od.`purchase_supplier_price` / `conversion_rate`,
				od.`original_product_price` * '.(int)Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN').' / 100
			)) as total_purchase_price
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
			'.Shop::addSqlRestriction(false, 'o'));
	}
	
	public static function getExpenses($date_from, $date_to, $granularity = false)
	{
		$expenses = ($granularity == 'day' ? array() : 0);

		$orders = Db::getInstance()->ExecuteS('
		SELECT
			LEFT(`invoice_date`, 10) as date,
			total_paid_tax_incl / o.conversion_rate as total_paid_tax_incl,
			total_shipping_tax_excl / o.conversion_rate as total_shipping_tax_excl,
			o.module,
			a.id_country,
			o.id_currency,
			c.id_reference as carrier_reference
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address
		LEFT JOIN `'._DB_PREFIX_.'carrier` c ON o.id_carrier = c.id_carrier
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON o.current_state = os.id_order_state
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59" AND os.logable = 1
		'.Shop::addSqlRestriction(false, 'o'));
		foreach ($orders as $order)
		{
			// Add flat fees for this order
			$flat_fees = Configuration::get('CONF_ORDER_FIXED') + (
				$order['id_currency'] == Configuration::get('PS_CURRENCY_DEFAULT')
					? Configuration::get('CONF_'.strtoupper($order['module']).'_FIXED')
					: Configuration::get('CONF_'.strtoupper($order['module']).'_FIXED_FOREIGN')
				);

			// Add variable fees for this order
			$var_fees = $order['total_paid_tax_incl'] * (
				$order['id_currency'] == Configuration::get('PS_CURRENCY_DEFAULT')
					? Configuration::get('CONF_'.strtoupper($order['module']).'_VAR')
					: Configuration::get('CONF_'.strtoupper($order['module']).'_VAR_FOREIGN')
				) / 100;

			// Add shipping fees for this order
			$shipping_fees = $order['total_shipping_tax_excl'] * (
				$order['id_country'] == Configuration::get('PS_COUNTRY_DEFAULT')
					? Configuration::get('CONF_'.strtoupper($order['carrier_reference']).'_SHIP')
					: Configuration::get('CONF_'.strtoupper($order['carrier_reference']).'_SHIP_OVERSEAS')
				) / 100;
			
			// Tally up these fees
			if ($granularity == 'day')
			{
				if (!isset($expenses[strtotime($order['date'])]))
					$expenses[strtotime($order['date'])] = 0;
				$expenses[strtotime($order['date'])] += $flat_fees + $var_fees + $shipping_fees;
			}
			else
				$expenses += $flat_fees + $var_fees + $shipping_fees;
		}
		return $expenses;
	}

	public function displayAjaxGetKpi()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		switch (Tools::getValue('kpi'))
		{
			case 'conversion_rate':
				$visitors = AdminStatsController::getVisits(true, date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);
				$orders = AdminStatsController::getOrders(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);

				// $data = array();
				// $from = strtotime(date('Y-m-d 00:00:00', strtotime('-31 day')));
				// $to = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
				// for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
					// if (isset($visitors[$date]) && $visitors[$date])
						// $data[$date] = round(100 * ((isset($orders[$date]) && $orders[$date]) ? $orders[$date] : 0) / $visitors[$date], 2);
					// else
						// $data[$date] = 0;

				$visits_sum = $visitors; //array_sum($visitors);
				$orders_sum = $orders; //array_sum($orders);
				if ($visits_sum)
					$value = round(100 * $orders_sum / $visits_sum, 2);
				elseif ($orders_sum)
					$value = '&infin;';
				else
					$value = 0;
				$value .= '%';

				// ConfigurationKPI::updateValue('CONVERSION_RATE_CHART', Tools::jsonEncode($data));
				ConfigurationKPI::updateValue('CONVERSION_RATE', $value);
				ConfigurationKPI::updateValue('CONVERSION_RATE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'abandoned_cart':
				$value = AdminStatsController::getAbandonedCarts(date('Y-m-d H:i:s', strtotime('-2 day')), date('Y-m-d H:i:s', strtotime('-1 day')));
				ConfigurationKPI::updateValue('ABANDONED_CARTS', $value);
				ConfigurationKPI::updateValue('ABANDONED_CARTS_EXPIRE', strtotime('+1 hour'));
				break;

			case 'installed_modules':
				$value = AdminStatsController::getInstalledModules();
				ConfigurationKPI::updateValue('INSTALLED_MODULES', $value);
				ConfigurationKPI::updateValue('INSTALLED_MODULES_EXPIRE', strtotime('+2 min'));
				break;

			case 'disabled_modules':
				$value = AdminStatsController::getDisabledModules();
				ConfigurationKPI::updateValue('DISABLED_MODULES', $value);
				ConfigurationKPI::updateValue('DISABLED_MODULES_EXPIRE', strtotime('+2 min'));
				break;

			case 'update_modules':
				$value = AdminStatsController::getModulesToUpdate();
				ConfigurationKPI::updateValue('UPDATE_MODULES', $value);
				ConfigurationKPI::updateValue('UPDATE_MODULES_EXPIRE', strtotime('+2 min'));
				break;

			case 'percent_product_stock':
				$value = AdminStatsController::getPercentProductStock();
				ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK', $value);
				ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK_EXPIRE', strtotime('+4 hour'));
				break;

			case 'product_avg_gross_margin':
				$value = AdminStatsController::getProductAverageGrossMargin();
				ConfigurationKPI::updateValue('PRODUCT_AVG_GROSS_MARGIN', $value);
				ConfigurationKPI::updateValue('PRODUCT_AVG_GROSS_MARGIN_EXPIRE', strtotime('+6 hour'));
				break;

			case 'disabled_categories':
				$value = AdminStatsController::getDisabledCategories();
				ConfigurationKPI::updateValue('DISABLED_CATEGORIES', $value);
				ConfigurationKPI::updateValue('DISABLED_CATEGORIES_EXPIRE', strtotime('+2 hour'));
				break;

			case 'disabled_products':
				$value = AdminStatsController::getDisabledProducts();
				ConfigurationKPI::updateValue('DISABLED_PRODUCTS', $value);
				ConfigurationKPI::updateValue('DISABLED_PRODUCTS_EXPIRE', strtotime('+2 hour'));
				break;

			case '8020_sales_catalog':
				$value = AdminStatsController::get8020SalesCatalog(date('Y-m-d', strtotime('-30 days')), date('Y-m-d'));
				$value = sprintf($this->l('%d%% of your Catalog'), $value);
				ConfigurationKPI::updateValue('8020_SALES_CATALOG', $value);
				ConfigurationKPI::updateValue('8020_SALES_CATALOG_EXPIRE', strtotime('+12 hour'));
				break;

			case 'empty_categories':
				$value = AdminStatsController::getEmptyCategories();
				ConfigurationKPI::updateValue('EMPTY_CATEGORIES', $value);
				ConfigurationKPI::updateValue('EMPTY_CATEGORIES_EXPIRE', strtotime('+2 hour'));
				break;

			case 'customer_main_gender':
				$value = AdminStatsController::getCustomerMainGender();

				if ($value === false)
					$value = $this->l('No customers', null, null, false);
				elseif ($value['type'] == 'female')
					$value = sprintf($this->l('%d%% Female Customers', null, null, false), $value['value']);
				elseif ($value['type'] == 'male')
					$value = sprintf($this->l('%d%% Male Customers', null, null, false), $value['value']);
				else
					$value = sprintf($this->l('%d%% Neutral Customers', null, null, false), $value['value']);

				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER', array($this->context->language->id => $value));
				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER_EXPIRE', array($this->context->language->id => strtotime('+1 day')));
				break;

			case 'avg_customer_age':
				$value = sprintf($this->l('%d years', null, null, false), AdminStatsController::getAverageCustomerAge(), 1);
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE', array($this->context->language->id => $value));
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE_EXPIRE', array($this->context->language->id => strtotime('+1 day')));
				break;

			case 'pending_messages':
				$value = (int)AdminStatsController::getPendingMessages();
				ConfigurationKPI::updateValue('PENDING_MESSAGES', $value);
				ConfigurationKPI::updateValue('PENDING_MESSAGES_EXPIRE', strtotime('+5 min'));
				break;

			case 'avg_msg_response_time':
				$value = sprintf($this->l('%.1f hours', null, null, false), AdminStatsController::getAverageMessageResponseTime(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day'))));
				ConfigurationKPI::updateValue('AVG_MSG_RESPONSE_TIME', $value);
				ConfigurationKPI::updateValue('AVG_MSG_RESPONSE_TIME_EXPIRE', strtotime('+4 hour'));
				break;

			case 'messages_per_thread':
				$value = round(AdminStatsController::getMessagesPerThread(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day'))), 1);
				ConfigurationKPI::updateValue('MESSAGES_PER_THREAD', $value);
				ConfigurationKPI::updateValue('MESSAGES_PER_THREAD_EXPIRE', strtotime('+12 hour'));
				break;

			case 'newsletter_registrations':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer`
				WHERE newsletter = 1
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				if (Module::isInstalled('blocknewsletter'))
				{
					$value += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'newsletter`
					WHERE active = 1
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				}

				ConfigurationKPI::updateValue('NEWSLETTER_REGISTRATIONS', $value);
				ConfigurationKPI::updateValue('NEWSLETTER_REGISTRATIONS_EXPIRE', strtotime('+6 hour'));
				break;

			case 'enabled_languages':
				$value = Language::countActiveLanguages();
				ConfigurationKPI::updateValue('ENABLED_LANGUAGES', $value);
				ConfigurationKPI::updateValue('ENABLED_LANGUAGES_EXPIRE', strtotime('+1 min'));
				break;

			case 'frontoffice_translations':
				$themes = Theme::getThemes();
				$languages = Language::getLanguages();
				$total = $translated = 0;
				foreach ($themes as $theme)
					foreach ($languages as $language)
					{
						$kpi_key = substr(strtoupper($theme->name.'_'.$language['iso_code']), 0, 16);
						$total += ConfigurationKPI::get('TRANSLATE_TOTAL_'.$kpi_key);
						$translated += ConfigurationKPI::get('TRANSLATE_DONE_'.$kpi_key);
					}
				$value = 0;
				if ($translated)
					$value = round(100 * $translated / $total, 1);
				$value .= '%';
				ConfigurationKPI::updateValue('FRONTOFFICE_TRANSLATIONS', $value);
				ConfigurationKPI::updateValue('FRONTOFFICE_TRANSLATIONS_EXPIRE', strtotime('+2 min'));
				break;

			case 'main_country':
				if (!($row = AdminStatsController::getMainCountry(date('Y-m-d', strtotime('-30 day')), date('Y-m-d'))))
					$value = $this->l('No orders', null, null, false);
				else
				{
					$country = new Country($row['id_country'], $this->context->language->id);
					$value = sprintf($this->l('%d%% %s', null, null, false), $row['orders'], $country->name);
				}

				ConfigurationKPI::updateValue('MAIN_COUNTRY', array($this->context->language->id => $value));				
				ConfigurationKPI::updateValue('MAIN_COUNTRY_EXPIRE', array($this->context->language->id => strtotime('+1 day')));
				break;

			case 'orders_per_customer':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer` c
				WHERE active = 1
				'.Shop::addSqlRestriction());
				if ($value)
				{
					$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'orders` o
					WHERE valid = 1
					'.Shop::addSqlRestriction());
					$value = round($orders / $value, 2);
				}

				ConfigurationKPI::updateValue('ORDERS_PER_CUSTOMER', $value);
				ConfigurationKPI::updateValue('ORDERS_PER_CUSTOMER_EXPIRE', strtotime('+1 day'));
				break;

			case 'average_order_value':
				$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT
					COUNT(`id_order`) as orders,
					SUM(`total_paid_tax_excl` / `conversion_rate`) as total_paid_tax_excl
				FROM `'._DB_PREFIX_.'orders`
				WHERE `invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction());
				$value = Tools::displayPrice($row['orders'] ? $row['total_paid_tax_excl'] / $row['orders'] : 0, $currency);
				ConfigurationKPI::updateValue('AVG_ORDER_VALUE', $value);
				ConfigurationKPI::updateValue('AVG_ORDER_VALUE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'netprofit_visit':
				$date_from = date('Y-m-d', strtotime('-31 day'));
				$date_to = date('Y-m-d', strtotime('-1 day'));

				$total_visitors = AdminStatsController::getVisits(false, $date_from, $date_to);
				$net_profits = AdminStatsController::getTotalSales($date_from, $date_to);
				$net_profits -= AdminStatsController::getExpenses($date_from, $date_to);
				$net_profits -= AdminStatsController::getPurchases($date_from, $date_to);

				if ($total_visitors)
					$value = Tools::displayPrice($net_profits / $total_visitors, $currency);
				elseif ($net_profits)
					$value = '&infin;';
				else
					$value = Tools::displayPrice(0, $currency);
	
				ConfigurationKPI::updateValue('NETPROFIT_VISIT', $value);
				ConfigurationKPI::updateValue('NETPROFIT_VISIT_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;
				
				case 'products_per_category':
					$products = AdminStatsController::getTotalProducts();
					$categories = AdminStatsController::getTotalCategories();
					$value = round($products / $categories);
					ConfigurationKPI::updateValue('PRODUCTS_PER_CATEGORY', $value);
					ConfigurationKPI::updateValue('PRODUCTS_PER_CATEGORY_EXPIRE', strtotime('+1 hour'));
					break;
					
				case 'top_category':
					if (!($id_category = AdminStatsController::getBestCategory(date('Y-m-d', strtotime('-1 month')), date('Y-m-d'))))
						$value = $this->l('No category', null, null, false);
					else
					{
						$category = new Category($id_category, $this->context->language->id);
						$value = $category->name;
					}

					ConfigurationKPI::updateValue('TOP_CATEGORY', array($this->context->language->id => $value));				
					ConfigurationKPI::updateValue('TOP_CATEGORY_EXPIRE', array($this->context->language->id => strtotime('+1 day')));
					break;

			default:
				$value = false;
		}
		if ($value !== false)
		{
			$array = array('value' => $value);
			if (isset($data))
				$array['data'] = $data;
			die(Tools::jsonEncode($array));
		}
		die(Tools::jsonEncode(array('has_errors' => true)));
	}
}
