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
class HTMLTemplateInvoiceCore extends HTMLTemplate
{
	public $order;

   public $available_in_your_account = false;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
        $this->order = new Order($this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($order_invoice->date_add, (int)$this->order->id_lang);
		$this->title = self::l('Invoice ').' #'.Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id).sprintf('%06d', $order_invoice->number);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$country = new Country((int)$this->order->id_address_invoice);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
	    	$delivery_address = new Address((int)$this->order->id_address_delivery);
    		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer($this->order->id_customer);

		$this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $this->order_invoice->getProducts(),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
         	'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer
		));

		return $this->smarty->fetch($this->getTemplate($country->iso_code));
	}

	/**
	 * Returns the tax tab content
	 */
    public function getTaxTabContent()
    {
			$invoice_address = new Address((int)$this->order->id_address_invoice);
			$tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
								AND !empty($invoiceAddress->vat_number)
								AND $invoiceAddress->id_country != Configuration::get('VATNUMBER_COUNTRY');

			$this->smarty->assign(array(
				'tax_exempt' => $tax_exempt,
				'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
				'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
				'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
				'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
				'order' => $this->order,
				'order_invoice' => $this->order_invoice
			));

			return $this->smarty->fetch(_PS_THEME_DIR_.'/pdf/invoice.tax-tab.tpl');
    }

	/**
	 * Returns the invoice template associated to the country iso_code
	 * @param string $iso_country
	 */
	protected function getTemplate($iso_country)
	{
		$file = Configuration::get('PS_INVOICE_MODEL');
		$template = _PS_THEME_DIR_.'/pdf/'.$file.'.tpl';

		$iso_template = _PS_THEME_DIR_.'/pdf/'.$file.'.'.$iso_country.'.tpl';
		if (file_exists($iso_template))
			$template = $iso_template;

		return $template;
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
    public function getBulkFilename()
    {
        return 'invoices.pdf';
    }

	/**
	 * Returns the template filename
	 * @return string filename
	 */
    public function getFilename()
    {
        return Configuration::get('PS_INVOICE_PREFIX').sprintf('%06d', $this->order_invoice->number).'.pdf';
    }
}

