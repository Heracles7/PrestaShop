<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderInvoiceCore extends ObjectModel
{
	const TAX_EXCL = 0;
	const TAX_INCL = 1;
	const DETAIL = 2;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $number;

	/** @var integer */
	public $delivery_number;

	/** @var integer */
	public $delivery_date = '0000-00-00 00:00:00';

	/** @var float */
	public $total_discount_tax_excl;

	/** @var float */
	public $total_discount_tax_incl;

	/** @var float */
	public $total_paid_tax_excl;

	/** @var float */
	public $total_paid_tax_incl;

	/** @var float */
	public $total_products;

	/** @var float */
	public $total_products_wt;

	/** @var float */
	public $total_shipping_tax_excl;

	/** @var float */
	public $total_shipping_tax_incl;

	/** @var int */
	public $shipping_tax_computation_method;

	/** @var float */
	public $total_wrapping_tax_excl;

	/** @var float */
	public $total_wrapping_tax_incl;

	/** @var string note */
	public $note;

	/** @var intger */
	public $date_add;

	/** @var array Total paid cache */
	protected static $_total_paid_cache = array();

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_invoice',
		'primary' => 'id_order_invoice',
		'fields' => array(
			'id_order' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'number' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'delivery_number' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'delivery_date' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'total_discount_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_discount_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'total_paid_tax_excl' =>	array('type' => self::TYPE_FLOAT),
			'total_paid_tax_incl' =>	array('type' => self::TYPE_FLOAT),
			'total_products' =>			array('type' => self::TYPE_FLOAT),
			'total_products_wt' =>		array('type' => self::TYPE_FLOAT),
			'total_shipping_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_shipping_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'shipping_tax_computation_method' => array('type' => self::TYPE_INT),
			'total_wrapping_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_wrapping_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'note' => 					array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65000),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	public function getProductsDetail()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product` p
		ON p.id_product = od.product_id
		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
		WHERE od.`id_order` = '.(int)$this->id_order.'
		'.($this->id && $this->number ? ' AND od.`id_order_invoice` = '.(int)$this->id : ''));
	}

	public static function getInvoiceByNumber($id_invoice)
	{
		if (is_numeric($id_invoice))
			$id_invoice = (int)($id_invoice);
		elseif (is_string($id_invoice))
		{
			$matches = array();
			if (preg_match('/^(?:'.Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id).')\s*([0-9]+)$/i', $id_invoice, $matches))
				$id_invoice = $matches[1];
		}
		if (!$id_invoice)
			return false;

		$id_order_invoice = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_order_invoice`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE number = '.(int)$id_invoice
		);

		return ($id_order_invoice ? new OrderInvoice($id_order_invoice) : false);
	}

	/**
	 * Get order products
	 *
	 * @return array Products with price, quantity (with taxe and without)
	 */
	public function getProducts($products = false, $selectedProducts = false, $selectedQty = false)
	{
		if (!$products)
			$products = $this->getProductsDetail();

		$order = new Order($this->id_order);
		$customized_datas = Product::getAllCustomizedDatas($order->id_cart);

		$resultArray = array();
		foreach ($products as $row)
		{
			// Change qty if selected
			if ($selectedQty)
			{
				$row['product_quantity'] = 0;
				foreach ($selectedProducts as $key => $id_product)
					if ($row['id_order_detail'] == $id_product)
						$row['product_quantity'] = (int)($selectedQty[$key]);
				if (!$row['product_quantity'])
					continue;
			}

			$this->setProductImageInformations($row);
			$this->setProductCurrentStock($row);
			$this->setProductCustomizedDatas($row, $customized_datas);

			// Add information for virtual product
			if ($row['download_hash'] && !empty($row['download_hash']))
			{
				$row['filename'] = ProductDownload::getFilenameFromIdProduct((int)$row['product_id']);
				// Get the display filename
				$row['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
			}

			$row['id_address_delivery'] = $order->id_address_delivery;

			/* Stock product */
			$resultArray[(int)$row['id_order_detail']] = $row;
		}

		if ($customized_datas)
			Product::addCustomizationPrice($resultArray, $customized_datas);

		return $resultArray;
	}

	protected function setProductCustomizedDatas(&$product, $customized_datas)
	{
		$product['customizedDatas'] = null;
		if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']]))
			$product['customizedDatas'] = $customized_datas[$product['product_id']][$product['product_attribute_id']];
		else
			$product['customizationQuantityTotal'] = 0;
	}

	/**
	 *
	 * This method allow to add stock information on a product detail
	 * @param array &$product
	 */
	protected function setProductCurrentStock(&$product)
	{
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
			&& (int)$product['advanced_stock_management'] == 1
			&& (int)$product['id_warehouse'] > 0)
			$product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities($product['product_id'], $product['product_attribute_id'], null, true);
		else
			$product['current_stock'] = '--';
	}

	/**
	 *
	 * This method allow to add image information on a product detail
	 * @param array &$product
	 */
	protected function setProductImageInformations(&$product)
	{
		if (isset($product['product_attribute_id']) && $product['product_attribute_id'])
			$id_image = Db::getInstance()->getValue('
				SELECT image_shop.id_image
				FROM '._DB_PREFIX_.'product_attribute_image pai'.
				Shop::addSqlAssociation('image', 'pai', true).'
				WHERE id_product_attribute = '.(int)$product['product_attribute_id']);

		if (!isset($id_image) || !$id_image)
			$id_image = Db::getInstance()->getValue('
				SELECT image_shop.id_image
				FROM '._DB_PREFIX_.'image i'.
				Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
				WHERE id_product = '.(int)($product['product_id']));

		$product['image'] = null;
		$product['image_size'] = null;

		if ($id_image)
			$product['image'] = new Image($id_image);
	}

	/**
	 * This method returns true if at least one order details uses the
	 * One After Another tax computation method.
	 *
	 * @since 1.5
	 * @return boolean
	 */
	public function useOneAfterAnotherTaxComputationMethod()
	{
		// if one of the order details use the tax computation method the display will be different
		return Db::getInstance()->getValue('
		SELECT od.`tax_computation_method`
		FROM `'._DB_PREFIX_.'order_detail_tax` odt
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
		WHERE od.`id_order` = '.(int)$this->id_order.'
		AND od.`id_order_invoice` = '.(int)$this->id.'
		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
		) || Configuration::get('PS_INVOICE_TAXES_BREAKDOWN');
	}

	/**
	 * Returns the correct product taxes breakdown.
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getProductTaxesBreakdown($order = null)
	{
		$previous_round_mode = Tools::$round_mode;
		Tools::$round_mode = $order->round_mode;

		/**
		 * Keys of the $breakdown array below are tax rates.
		 * Values are arrays that should contain at least the columns:
		 * - total_amount
		 * - name
		 * - total_price_tax_excl
		 */
		$breakdown = array();

		$order_discount_tax_excl = $this->total_discount_tax_excl;

		$order = new Order($this->id_order);
		$free_shipping_tax_excl = 0;

		$product_specific_discounts = array();

		foreach ($order->getCartRules() as $order_cart_rule)
		{
			if ($order_cart_rule['free_shipping'])
			{
				$free_shipping_tax_excl = $this->total_shipping_tax_excl;
			}

			$cart_rule = new CartRule($order_cart_rule['id_cart_rule']);
			if ($cart_rule->reduction_product > 0)
			{
				if (empty($product_specific_discounts[$cart_rule->reduction_product]))
				{
					$product_specific_discounts[$cart_rule->reduction_product] = 0;
				}

				$product_specific_discounts[$cart_rule->reduction_product] += $order_cart_rule['value_tax_excl'];
				$order_discount_tax_excl -= $order_cart_rule['value_tax_excl'];
			}
		}

		$order_discount_tax_excl -= $free_shipping_tax_excl;

		$product_details = Db::getInstance()->executeS('
			SELECT t.`rate`, t.`id_tax`, od.`total_price_tax_excl` AS total_price_tax_excl, od.product_id as id_product, `total_amount`, od.`ecotax`, od.`ecotax_tax_rate`, od.`product_quantity`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id_order.'
			AND od.`id_order_invoice` = '.(int)$this->id.'
		');

		$order_ecotax = 0;

		foreach ($product_details as $details)
		{
			$order_ecotax += $details['ecotax'] * $details['product_quantity'];
		}

		foreach ($product_details as $details)
		{
			if (!array_key_exists($details['rate'], $breakdown))
			{
				$breakdown[$details['rate']] = array(
					'name' => $details['rate'],
					'total_amount' => 0,
					'total_price_tax_excl' => 0,
					'ecotax_price_tax_excl' => 0,
					'discount_ratio' => 0
				);
			}
			$breakdown[$details['rate']]['ecotax_price_tax_excl'] += $details['ecotax'] * $details['product_quantity'];
			$discount_ratio = ($details['total_price_tax_excl'] + $details['ecotax']) / $this->total_products;
			$breakdown[$details['rate']]['discount_ratio'] += $discount_ratio;
			$breakdown[$details['rate']]['total_amount'] += $details['total_amount'];
			$share_of_order_discount = $discount_ratio * ($order_discount_tax_excl);
			$breakdown[$details['rate']]['total_price_tax_excl'] += $details['total_price_tax_excl'] - $share_of_order_discount;
			if (!empty($product_specific_discounts[$details['id_product']]))
			{
				$breakdown[$details['rate']]['total_price_tax_excl'] -= $product_specific_discounts[$details['id_product']];
			}
		}

		$product_discounts = $this->total_discount_tax_excl - $free_shipping_tax_excl;
		$total_products_after_discounts = $this->total_products - $product_discounts;
		$breakdown_total_products_after_discounts = 0;
		foreach ($breakdown as $rate => $details)
		{
			$total_price_tax_excl = Tools::ps_round($breakdown[$rate]['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
			$breakdown_total_products_after_discounts += $total_price_tax_excl;
			$breakdown[$rate]['total_amount'] = Tools::ps_round($breakdown[$rate]['total_amount'], _PS_PRICE_COMPUTE_PRECISION_);
			$breakdown[$rate]['total_price_tax_excl'] = $total_price_tax_excl;
		}

		$delta = $total_products_after_discounts - $breakdown_total_products_after_discounts - $order_ecotax;
		if ($delta !== 0)
		{
			Tools::spreadAmount($delta, _PS_PRICE_COMPUTE_PRECISION_, $breakdown, 'total_price_tax_excl');
		}

		Tools::$round_mode = $previous_round_mode;
		
		return $breakdown;
	}

	/**
	 * Returns the shipping taxes breakdown
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getShippingTaxesBreakdown($order)
	{
		$taxes_breakdown = array();

		// No shipping breakdown if it's free!
		foreach ($order->getCartRules() as $cart_rule)
			if ($cart_rule['free_shipping'])
				return $taxes_breakdown;

		$shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

		if ($shipping_tax_amount > 0)
			$taxes_breakdown[] = array(
				'rate' => $order->carrier_tax_rate,
				'total_amount' => $shipping_tax_amount,
				'total_tax_excl' => $this->total_shipping_tax_excl
			);

		return $taxes_breakdown;
	}

	/**
	 * Returns the wrapping taxes breakdown
	 *
	 * @todo
	 * @since 1.5
	 * @return array
	 */
	public function getWrappingTaxesBreakdown()
	{
		$taxes_breakdown = array();
		return $taxes_breakdown;
	}

	/**
	 * Returns the ecotax taxes breakdown
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getEcoTaxTaxesBreakdown()
	{
		$result = Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate` as `rate`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_excl`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int)$this->id_order.'
		AND `id_order_invoice` = '.(int)$this->id.'
		GROUP BY `ecotax_tax_rate`');

		$taxes = array();
		foreach ($result as $row)
			if ($row['ecotax_tax_excl'] > 0)
			{
				$row['ecotax_tax_incl'] = Tools::ps_round($row['ecotax_tax_excl'] + ($row['ecotax_tax_excl'] * $row['rate'] / 100), _PS_PRICE_DISPLAY_PRECISION_);
				$row['ecotax_tax_excl'] = Tools::ps_round($row['ecotax_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
				$taxes[] = $row;
			}
		return $taxes;
	}

	/**
	 * Returns all the order invoice that match the date interval
	 *
	 * @since 1.5
	 * @static
	 * @param $date_from
	 * @param $date_to
	 * @return array collection of OrderInvoice
	 */
	public static function getByDateInterval($date_from, $date_to)
	{
		$order_invoice_list = Db::getInstance()->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE DATE_ADD(oi.date_add, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
			AND oi.date_add >= \''.pSQL($date_from).'\'
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			AND oi.number > 0
			ORDER BY oi.date_add ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5.0.3
	 * @static
	 * @param $id_order_state
	 * @return array collection of OrderInvoice
	 */
	public static function getByStatus($id_order_state)
	{
		$order_invoice_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE '.(int)$id_order_state.' = o.current_state
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			AND oi.number > 0
			ORDER BY oi.`date_add` ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5.0.3
	 * @static
	 * @param $date_from
	 * @param $date_to
	 * @return array collection of invoice
	 */
	public static function getByDeliveryDateInterval($date_from, $date_to)
	{
		$order_invoice_list = Db::getInstance()->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE DATE_ADD(oi.delivery_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
			AND oi.delivery_date >= \''.pSQL($date_from).'\'
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			ORDER BY oi.delivery_date ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5
	 * @static
	 * @param $id_order_invoice
	 */
	public static function getCarrier($id_order_invoice)
	{
		$carrier = false;
		if ($id_carrier = OrderInvoice::getCarrierId($id_order_invoice))
			$carrier = new Carrier((int)$id_carrier);

		return $carrier;
	}

	/**
	 * @since 1.5
	 * @static
	 * @param $id_order_invoice
	 */
	public static function getCarrierId($id_order_invoice)
	{
		$sql = 'SELECT `id_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order_invoice` = '.(int)$id_order_invoice;

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * @static
	 * @param $id
	 * @return OrderInvoice
	 */
	public static function retrieveOneById($id)
	{
		$order_invoice = new OrderInvoice($id);
		if (!Validate::isLoadedObject($order_invoice))
			throw new PrestaShopException('Can\'t load Order Invoice object for id: '.$id);
		return $order_invoice;
	}

	/**
	 * Amounts of payments
	 * @since 1.5.0.2
	 * @return float Total paid
	 */
	public function getTotalPaid()
	{
		$cache_id = 'order_invoice_paid_'.(int)$this->id;
		if (!Cache::isStored($cache_id))
		{
			$amount = 0;
			$payments = OrderPayment::getByInvoiceId($this->id);
			foreach ($payments as $payment)
				$amount += $payment->amount;
			Cache::store($cache_id, $amount);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Rest Paid
	 * @since 1.5.0.2
	 * @return float Rest Paid
	 */
	public function getRestPaid()
	{
		return round($this->total_paid_tax_incl + $this->getSiblingTotal() - $this->getTotalPaid(), 2);
	}


	/**
	 * Return collection of order invoice object linked to the payments of the current order invoice object
	 *
	 * @since 1.5.0.14
     * @return PrestaShopCollection|array Collection of OrderInvoice or empty array
	 */
	public function getSibling()
	{
		$query = new DbQuery();
		$query->select('oip2.id_order_invoice');
		$query->from('order_invoice_payment', 'oip1');
		$query->innerJoin('order_invoice_payment', 'oip2',
			'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
		$query->where('oip1.id_order_invoice = '.$this->id);

		$invoices = Db::getInstance()->executeS($query);
		if (!$invoices)
			return array();

		$invoice_list = array();
		foreach ($invoices as $invoice)
			$invoice_list[] = $invoice['id_order_invoice'];

		$payments = new PrestaShopCollection('OrderInvoice');
		$payments->where('id_order_invoice', 'IN', $invoice_list);

		return $payments;
	}


	/**
	 * Return total to paid of sibling invoices
	 *
	 * @param int $mod TAX_EXCL, TAX_INCL, DETAIL
	 *
	 * @since 1.5.0.14
	 */
	public function getSiblingTotal($mod = OrderInvoice::TAX_INCL)
	{
		$query = new DbQuery();
		$query->select('SUM(oi.total_paid_tax_incl) as total_paid_tax_incl, SUM(oi.total_paid_tax_excl) as total_paid_tax_excl');
		$query->from('order_invoice_payment', 'oip1');
		$query->innerJoin('order_invoice_payment', 'oip2',
			'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
		$query->leftJoin('order_invoice', 'oi',
			'oi.id_order_invoice = oip2.id_order_invoice');
		$query->where('oip1.id_order_invoice = '.$this->id);

		$row = Db::getInstance()->getRow($query);

		switch ($mod)
		{
			case OrderInvoice::TAX_EXCL:
				return $row['total_paid_tax_excl'];
			case OrderInvoice::TAX_INCL:
				return $row['total_paid_tax_incl'];
			default:
				return $row;
		}
	}

	/**
	 * Get global rest to paid
	 *    This method will return something different of the method getRestPaid if
	 *    there is an other invoice linked to the payments of the current invoice
	 * @since 1.5.0.13
	 */
	public function getGlobalRestPaid()
	{
		static $cache;

		if (!isset($cache[$this->id]))
		{
			$res = Db::getInstance()->getRow('
			SELECT SUM(sub.paid) paid, SUM(sub.to_paid) to_paid
			FROM (
				SELECT
					op.amount as paid, SUM(oi.total_paid_tax_incl) to_paid
				FROM `'._DB_PREFIX_.'order_invoice_payment` oip1
				INNER JOIN `'._DB_PREFIX_.'order_invoice_payment` oip2
					ON oip2.id_order_payment = oip1.id_order_payment
				INNER JOIN `'._DB_PREFIX_.'order_invoice` oi
					ON oi.id_order_invoice = oip2.id_order_invoice
				INNER JOIN `'._DB_PREFIX_.'order_payment` op
					ON op.id_order_payment = oip2.id_order_payment
				WHERE oip1.id_order_invoice = '.(int)$this->id.'
				GROUP BY op.id_order_payment
			) sub');
			$cache[$this->id] = round($res['to_paid'] - $res['paid'], 2);
		}

		return $cache[$this->id];
	}

	/**
	 * @since 1.5.0.2
	 * @return bool Is paid ?
	 */
	public function isPaid()
	{
		return $this->getTotalPaid() == $this->total_paid_tax_incl;
	}

	/**
	 * @since 1.5.0.2
	 * @return PrestaShopCollection Collection of Order payment
	 */
	public function getOrderPaymentCollection()
	{
		return OrderPayment::getByInvoiceId($this->id);
	}

	/**
	 * Get the formatted number of invoice
	 * @since 1.5.0.2
	 * @param int $id_lang for invoice_prefix
	 * @return string
	 */
	public function getInvoiceNumberFormatted($id_lang, $id_shop = null)
	{
		$invoice_formatted_number = Hook::exec('actionInvoiceNumberFormatted', array(
			get_class($this) => $this,
			'id_lang' => (int)$id_lang,
			'id_shop' => (int)$id_shop,
			'number' => (int)$this->number
		));

		if (!empty($invoice_formatted_number))
			return $invoice_formatted_number;

		return sprintf('%1$s%2$06d', Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, $id_shop), $this->number);
	}

	public function saveCarrierTaxCalculator(array $taxes_amount)
	{
		$is_correct = true;
		foreach ($taxes_amount as $id_tax => $amount)
		{
			$sql = 'INSERT INTO `'._DB_PREFIX_.'order_invoice_tax` (`id_order_invoice`, `type`, `id_tax`, `amount`)
					VALUES ('.(int)$this->id.', \'shipping\', '.(int)$id_tax.', '.(float)$amount.')';

			$is_correct &= Db::getInstance()->execute($sql);
		}

		return $is_correct;
	}
}
