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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartControllerCore extends FrontController
{
	public $php_self = 'cart';

	protected $id_product;
	protected $id_product_attribute;
	protected $id_address_delivery;
	protected $customization_id;
	protected $qty;

	/**
	 * This is not a public page, so the canonical redirection is disabled
	 */
	public function canonicalRedirection($canonicalURL = '')
	{
	}

	/**
	 * Initialize cart controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		// Get page main parameters
		$this->id_product = (int)Tools::getValue('id_product', null);
		$this->id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));
		$this->customization_id = (int)Tools::getValue('id_customization');
		$this->qty = abs(Tools::getValue('qty', 1));
		$this->id_address_delivery = (int)Tools::getValue('id_address_delivery');
	}

	public function postProcess()
	{
		if ($this->isTokenValid())
			$this->errors[] = Tools::displayError('Invalid token');

		// Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
		if ($this->context->cookie->exists() && !$this->errors)
		{
			if (Tools::getIsset('add') || Tools::getIsset('update'))
				$this->processChangeProductInCart();
			else if (Tools::getIsset('delete'))
				$this->processDeleteProductInCart();
			else if(Tools::getIsset('changeAddressDelivery'))
				$this->processChangeProductAddressDelivery();
			else if(Tools::getIsset('duplicate'))
				$this->processDuplicateProduct();

			// Make redirection
			if (!$this->errors && !$this->ajax)
			{
				$queryString = Tools::safeOutput(Tools::getValue('query', null));
				if ($queryString && !Configuration::get('PS_CART_REDIRECT'))
					Tools::redirect('index.php?controller=search&search='.$queryString);

				// Redirect to previous page
				if (isset($_SERVER['HTTP_REFERER']))
				{
					preg_match('!http(s?)://(.*)/(.*)!', $_SERVER['HTTP_REFERER'], $regs);
					if (isset($regs[3]) && !Configuration::get('PS_CART_REDIRECT'))
						Tools::redirect($_SERVER['HTTP_REFERER']);
				}

				Tools::redirect('index.php?controller=order&'.(isset($this->id_product) ? 'ipa='.$this->id_product : ''));
			}
		}
	}

	/**
	 * This process delete a product from the cart
	 */
	protected function processDeleteProductInCart()
	{
		if ($this->context->cart->deleteProduct($this->id_product, $this->id_product_attribute, $this->customization_id, $this->id_address_delivery))
		{
			if (!Cart::getNbProducts((int)($this->context->cart->id)))
			{
				$this->context->cart->delivery_option = '';
				$this->context->cart->gift = 0;
				$this->context->cart->gift_message = '';
				$this->context->cart->update();
			}
		}
		CartRule::autoRemoveFromCart();
	}

	protected function processChangeProductAddressDelivery()
	{
		$old_id_address_delivery = (int)Tools::getValue('old_id_address_delivery');
		$new_id_address_delivery = (int)Tools::getValue('new_id_address_delivery');

		$this->context->cart->setProductAddressDelivery(
			$this->id_product,
			$this->id_product_attribute,
			$old_id_address_delivery,
			$new_id_address_delivery);
	}

	protected function processDuplicateProduct()
	{
		if (
			!$this->context->cart->duplicateProduct(
				$this->id_product,
				$this->id_product_attribute,
				$this->id_address_delivery,
				(int)Tools::getValue('new_id_address_delivery'),
				1,
				true
			)
		)
		{
			//$error_message = $this->l('Error durring product duplication');
			// For the moment no translations
			$error_message = 'Error durring product duplication';
		}
	}

	/**
	 * This process add or update a product in the cart
	 */
	protected function processChangeProductInCart()
	{
		$mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';

		if ($this->qty == 0)
			$this->errors[] = Tools::displayError('Null quantity');
		else if (!$this->id_product)
			$this->errors[] = Tools::displayError('Product not found');

		$product = new Product($this->id_product, true, $this->context->language->id);
		if (!$product->id || !$product->active)
		{
			$this->errors[] = Tools::displayError('Product is no longer available.', false);
			return;
		}

		// Check product quantity availability
		if ($this->id_product_attribute)
		{
			if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $this->qty))
				$this->errors[] = Tools::displayError('There is not enough product in stock.');
		}
		else if ($product->hasAttributes())
		{
			$minimumQuantity = ($product->out_of_stock == 2) ? !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
			$this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimumQuantity);
			// @todo do something better than a redirect admin !!
			if (!$this->id_product_attribute)
				Tools::redirectAdmin($this->context->link->getProductLink($product));
			else if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $this->qty))
				$this->errors[] = Tools::displayError('There is not enough product in stock.');
		}
		else if (!$product->checkQty($this->qty))
			$this->errors[] = Tools::displayError('There is not enough product in stock.');

		// If no errors, process product addition
		if (!$this->errors && $mode == 'add')
		{
			// Add cart if no cart found
			if (!$this->context->cart->id)
			{
				$this->context->cart->add();
				if ($this->context->cart->id)
					$this->context->cookie->id_cart = (int)$this->context->cart->id;
			}

			// Check customizable fields
			if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id)
				$this->errors[] = Tools::displayError('Please fill in all required fields, then save the customization.');

			if (!$this->errors)
			{
				$updateQuantity = $this->context->cart->updateQty($this->qty, $this->id_product, $this->id_product_attribute, $this->customization_id, $this->id_address_delivery, Tools::getValue('op', 'up'));
				if ($updateQuantity < 0)
				{
					// If product has attribute, minimal quantity is set with minimal quantity of attribute
					$minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
					$this->errors[] = Tools::displayError('You must add').' '.$minimal_quantity.' '.Tools::displayError('Minimum quantity');
				}
				else if (!$updateQuantity)
					$this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.');
			}
		}
		CartRule::autoRemoveFromCart();
	}

	/**
	 * Remove discounts on cart
	 */
	protected function processRemoveDiscounts()
	{
		Tools::displayAsDeprecated();
		$this->errors = array_merge($this->errors, CartRule::autoRemoveFromCart());
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->setTemplate(_PS_THEME_DIR_.'errors.tpl');
	}

	/**
	 * Display ajax content (this function is called instead of classic display, in ajax mode)
	 */
	public function displayAjax()
	{
		if ($this->errors)
			die(Tools::jsonEncode(array('hasError' => true, $this->errors)));

		if (Tools::getIsset('summary'))
		{
			$result = array();
			if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			{
				$groups = (Validate::isLoadedObject($this->context->customer)) ? $this->context->customer->getGroups() : array(1);
				if ($this->context->cart->id_address_delivery)
					$deliveryAddress = new Address($this->context->cart->id_address_delivery);
				$id_country = (isset($deliveryAddress) && $deliveryAddress->id) ? $deliveryAddress->id_country : Configuration::get('PS_COUNTRY_DEFAULT');
				$result['carriers'] = Carrier::getCarriersForOrder(Country::getIdZone($id_country), $groups);
				//$result['checked'] = Carrier::getDefaultCarrierSelection($result['carriers'], (int)$this->cart->id_carrier);
				$result['HOOK_EXTRACARRIER'] = Module::hookExec('extraCarrier', array('address' => (isset($deliveryAddress) && (int)$deliveryAddress->id) ? $deliveryAddress : null));
			}
			$result['summary'] = $this->context->cart->getSummaryDetails();
			$result['customizedDatas'] = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
			$result['HOOK_SHOPPING_CART'] = Hook::exec('shoppingCart', $result['summary']);
			$result['HOOK_SHOPPING_CART_EXTRA'] = Hook::exec('shoppingCartExtra', $result['summary']);

			// Display reduced price (or not) without quantity discount
			if (Tools::getIsset('getproductprice'))
				foreach ($result['summary']['products'] as $key => &$product)
					$product['price_without_quantity_discount'] = Product::getPriceStatic($product['id_product'], !Product::getTaxCalculationMethod(), $product['id_product_attribute']);
			die(Tools::jsonEncode($result));
		}
		// @todo create a hook
		else if (file_exists(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php'))
			require_once(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php');
	}
}
