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
 *  @version  Release: $Revision: 8797 $
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5
 */
class HTMLTemplateOrderSlipCore extends HTMLTemplateInvoice
{
	public $order;
	public $order_slip;

	public function __construct(OrderSlip $order_slip, $smarty)
	{
		$this->order_slip = $order_slip;
		$this->order = new Order((int)$order_slip->id_order);

		$products = OrderSlip::getOrdersSlipProducts($this->order_slip->id, $this->order);
		$customized_datas = Product::getAllCustomizedDatas((int)$this->order->id_cart);
		Product::addCustomizationPrice($products, $customized_datas);

		$this->order->products = $products;
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date, (int)$this->order->id_lang);
		$this->title = HTMLTemplateOrderSlip::l('Slip #').sprintf('%06d', (int)$this->order_slip->id);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer);

		$this->order->total_products = $this->order->total_products_wt = 0;
		foreach ($this->order->products as &$product)
		{
			$product['total_price_tax_excl'] = $product['unit_price_tax_excl'] * $product['product_quantity'];
			$product['total_price_tax_incl'] = $product['unit_price_tax_incl'] * $product['product_quantity'];
			if ($this->order_slip->partial == 1)
			{
				$order_slip_detail = Db::getInstance()->getRow('
					SELECT * FROM `'._DB_PREFIX_.'order_slip_detail`
					WHERE `id_order_slip` = '.(int)$this->order_slip->id.'
					AND `id_order_detail` = '.(int)$product['id_order_detail']);
				$product['total_price_tax_excl'] = $order_slip_detail['amount_tax_excl'];
				$product['total_price_tax_incl'] = $order_slip_detail['amount_tax_incl'];
			}
			$this->order->total_products += $product['total_price_tax_excl'];
			$this->order->total_products_wt += $product['total_price_tax_incl'];
			$this->order->total_paid_tax_excl = $this->order->total_products;
			$this->order->total_paid_tax_incl = $this->order->total_products_wt;
		}
		unset($product); // remove reference
		if ($this->order_slip->shipping_cost == 0)
			$this->order->total_shipping_tax_incl = $this->order->total_shipping_tax_excl = 0;

		if ($this->order_slip->partial == 1 && $this->order_slip->shipping_cost_amount > 0)
			$this->order->total_shipping_tax_incl = $this->order->total_shipping_tax_excl = $this->order_slip->shipping_cost_amount;

		$this->order->total_paid_tax_incl += $this->order->total_shipping_tax_incl;
		$this->order->total_paid_tax_excl += $this->order->total_shipping_tax_excl;

		$this->smarty->assign(array(
			'order' => $this->order,
			'order_slip' => $this->order_slip,
			'order_details' => $this->order->products,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod((int)$customer->id_default_group),
			'tax_tab' => '',
		));

		return $this->smarty->fetch(_PS_THEME_DIR_.'/pdf/order-slip.tpl');
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'order-slips.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return 'order-slip-'.sprintf('%06d', $this->order_slip->id).'.pdf';
	}
}