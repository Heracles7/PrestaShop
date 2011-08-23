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

/* Class FreeOrder to use PaymentModule (abstract class, cannot be instancied) */
class FreeOrder extends PaymentModule {}

class ParentOrderControllerCore extends FrontController
{
	public $ssl = true;
	public $php_self = 'order';
		
	public $nbProducts;
	
	public function __construct()
	{
		parent::__construct();
		
		/* Disable some cache related bugs on the cart/order */
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	}
	
	public function init()
	{
		parent::init();
		$this->nbProducts = $this->context->cart->nbProducts();
	}
	
	public function preProcess()
	{
		global $isVirtualCart;
		parent::preProcess();
		
		// Redirect to the good order process
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 AND Dispatcher::getInstance()->getController() != 'order')
			Tools::redirect('index.php?controller=order');
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 AND Dispatcher::getInstance()->getController() != 'order-opc')
		{
			if (isset($_GET['step']) AND $_GET['step'] == 3)
				Tools::redirect('index.php?controller=order-opc&isPaymentStep=true');
			Tools::redirect('index.php?controller=order-opc');
		}
		
		if (Configuration::get('PS_CATALOG_MODE'))
			$this->errors[] = Tools::displayError('This store has not accepted your new order.');
		
		if (Tools::isSubmit('submitReorder') AND $id_order = (int)Tools::getValue('id_order'))
		{
			$oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
			$duplication = $oldCart->duplicate();
			if (!$duplication OR !Validate::isLoadedObject($duplication['cart']))
				$this->errors[] = Tools::displayError('Sorry, we cannot renew your order.');
			elseif (!$duplication['success'])
				$this->errors[] = Tools::displayError('Missing items - we are unable to renew your order');
			else
			{
				$this->context->cookie->id_cart = $duplication['cart']->id;
				$this->context->cookie->write();
				if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
					Tools::redirect('index.php?controller=order-opc');
				Tools::redirect('index.php?controller=order');
			}
		}

		if ($this->nbProducts)
		{
			if (Tools::isSubmit('submitAddDiscount') AND Tools::getValue('discount_name'))
			{
				$discountName = Tools::getValue('discount_name');
				if (!Validate::isDiscountName($discountName))
					$this->errors[] = Tools::displayError('Voucher name invalid.');
				else
				{
					$discount = new Discount((int)(Discount::getIdByName($discountName)));
					if (Validate::isLoadedObject($discount))
					{
						if ($tmpError = $this->context->cart->checkDiscountValidity($discount, $this->context->cart->getDiscounts(), $this->context->cart->getOrderTotal(), $this->context->cart->getProducts(), true))
							$this->errors[] = $tmpError;
					}
					else
						$this->errors[] = Tools::displayError('Voucher name invalid.');
					if (!sizeof($this->errors))
					{
						$this->context->cart->addDiscount((int)($discount->id));
						Tools::redirect('index.php?controller=order-opc');
					}
				}
				$this->context->smarty->assign(array(
					'errors' => $this->errors,
					'discount_name' => Tools::safeOutput($discountName)
				));
			}
			elseif (isset($_GET['deleteDiscount']) AND Validate::isUnsignedId($_GET['deleteDiscount']))
			{
				$this->context->cart->deleteDiscount((int)($_GET['deleteDiscount']));
				Tools::redirect('index.php?controller=order-opc');
			}

			/* Is there only virtual product in cart */
			if ($isVirtualCart = $this->context->cart->isVirtualCart())
				$this->_setNoCarrier();
		}
		
		$this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
	}
	
	public function setMedia()
	{
		parent::setMedia();
		
		// Adding CSS style sheet
		$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
		$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');

		// Adding JS files
		$this->addJS(_THEME_JS_DIR_.'tools.js');
		if ((Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 AND Tools::getValue('step') == 1) OR Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			$this->addJS(_THEME_JS_DIR_.'order-address.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js');
		if ((int)(Configuration::get('PS_BLOCK_CART_AJAX')) OR Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
		{
			$this->addJS(_THEME_JS_DIR_.'cart-summary.js');
			$this->addJS(_PS_JS_DIR_.'jquery/jquery-typewatch.pack.js');
		}
		
	}
	
	/**
	 * @return boolean
	 */
	protected function _checkFreeOrder()
	{
		if ($this->context->cart->getOrderTotal() <= 0)
		{
			$order = new FreeOrder();
			$order->free_order_class = true;
			$order->validateOrder($this->context->cart->id, Configuration::get('PS_OS_PAYMENT'), 0, Tools::displayError('Free order', false), null, array(), null, false, $this->context->cart->secure_key);
			return (int)Order::getOrderByCartId($this->context->cart->id);
		}
		return false;
	}
	
	protected function _updateMessage($messageContent)
	{
		if ($messageContent)
		{
			if (!Validate::isMessage($messageContent))
    			$this->errors[] = Tools::displayError('Invalid message');
    		elseif ($oldMessage = Message::getMessageByCartId((int)($this->context->cart->id)))
    		{
    			$message = new Message((int)($oldMessage['id_message']));
    			$message->message = htmlentities($messageContent, ENT_COMPAT, 'UTF-8');
    			$message->update();
    		}
    		else
    		{
    			$message = new Message();
    			$message->message = htmlentities($messageContent, ENT_COMPAT, 'UTF-8');
    			$message->id_cart = (int)($this->context->cart->id);
    			$message->id_customer = (int)($this->context->cart->id_customer);
    			$message->add();
    		}
    	}
    	else
    	{
    		if ($oldMessage = Message::getMessageByCartId($this->context->cart->id))
    		{
    			$message = new Message($oldMessage['id_message']);
    			$message->delete();
    		}
    	}
		return true;
	}
	
	protected function _processCarrier()
	{
		$this->context->cart->recyclable = (int)(Tools::getValue('recyclable'));
		$this->context->cart->gift = (int)(Tools::getValue('gift'));
		if ((int)(Tools::getValue('gift')))
		{
			if (!Validate::isMessage($_POST['gift_message']))
				$this->errors[] = Tools::displayError('Invalid gift message');
			else
				$this->context->cart->gift_message = strip_tags($_POST['gift_message']);
		}
		
		if (isset($this->context->customer->id) AND $this->context->customer->id)
		{
			$address = new Address((int)($this->context->cart->id_address_delivery));
			if (!($id_zone = Address::getZoneById($address->id)))
				$this->errors[] = Tools::displayError('No zone match with your address');
		}
		else
			$id_zone = Country::getIdZone((int)Configuration::get('PS_COUNTRY_DEFAULT'));
			
		if (Validate::isInt(Tools::getValue('id_carrier')) AND sizeof(Carrier::checkCarrierZone((int)(Tools::getValue('id_carrier')), (int)($id_zone))))
			$this->context->cart->id_carrier = (int)(Tools::getValue('id_carrier'));
		elseif (!$this->context->cart->isVirtualCart() AND (int)(Tools::getValue('id_carrier')) == 0)
			$this->errors[] = Tools::displayError('Invalid carrier or no carrier selected');
		
		Module::hookExec('processCarrier', array('cart' => $this->context->cart));
		
		return $this->context->cart->update();
	}
	
	protected function _assignSummaryInformations()
	{
		if (file_exists(_PS_SHIP_IMG_DIR_.$this->context->cart->id_carrier.'.jpg'))
			$this->context->smarty->assign('carrierPicture', 1);
		$summary = $this->context->cart->getSummaryDetails();
		$customizedDatas = Product::getAllCustomizedDatas($this->context->cart->id);

		// override customization tax rate with real tax (tax rules)
		foreach($summary['products'] AS &$productUpdate)
		{
			$productId = (int)(isset($productUpdate['id_product']) ? $productUpdate['id_product'] : $productUpdate['product_id']);
			$productAttributeId = (int)(isset($productUpdate['id_product_attribute']) ? $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id']);

			if (isset($customizedDatas[$productId][$productAttributeId]))
				$productUpdate['tax_rate'] = Tax::getProductTaxRate($productId, $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		}

		Product::addCustomizationPrice($summary['products'], $customizedDatas);

		if ($free_ship = Tools::convertPrice((float)(Configuration::get('PS_SHIPPING_FREE_PRICE')), new Currency($this->context->cart->id_currency)))
		{
			$discounts = $this->context->cart->getDiscounts();
			$total_free_ship =  $free_ship - ($summary['total_products_wt'] + $summary['total_discounts']);
			foreach ($discounts as $discount)
				if ($discount['id_discount_type'] == 3)
				{
					$total_free_ship = 0;
					break;
				}
			$this->context->smarty->assign('free_ship', $total_free_ship);
		}
		// for compatibility with 1.2 themes
		foreach($summary['products'] AS $key => $product)
			$summary['products'][$key]['quantity'] = $product['cart_quantity'];

		$this->context->smarty->assign($summary);
		$this->context->smarty->assign(array(
			'token_cart' => Tools::getToken(false),
			'isVirtualCart' => $this->context->cart->isVirtualCart(),
			'productNumber' => $this->context->cart->nbProducts(),
			'voucherAllowed' => Configuration::get('PS_VOUCHERS'),
			'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
			'customizedDatas' => $customizedDatas,
			'CUSTOMIZE_FILE' => _CUSTOMIZE_FILE_,
			'CUSTOMIZE_TEXTFIELD' => _CUSTOMIZE_TEXTFIELD_,
			'lastProductAdded' => $this->context->cart->getLastProduct(),
			'displayVouchers' => Discount::getVouchersToCartDisplay($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0)),
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank));
		$this->context->smarty->assign(array(
			'HOOK_SHOPPING_CART' => Module::hookExec('shoppingCart', $summary),
			'HOOK_SHOPPING_CART_EXTRA' => Module::hookExec('shoppingCartExtra', $summary)
		));
	}
	
	protected function _assignAddress()
	{
		//if guest checkout disabled and flag is_guest  in cookies is actived
		if(Configuration::get('PS_GUEST_CHECKOUT_ENABLED') == 0 AND ((int)$this->context->customer->is_guest != Configuration::get('PS_GUEST_CHECKOUT_ENABLED')))
		{
			$this->context->customer->logout();
			Tools::redirect('');
		}
		elseif (!Customer::getAddressesTotalById($this->context->customer->id))
			Tools::redirect('index.php?controller=address&back=order.php&step=1');
		$customer = $this->context->customer;
		if (Validate::isLoadedObject($customer))
		{
			/* Getting customer addresses */
			$customerAddresses = $customer->getAddresses($this->context->language->id);
			
			// Getting a list of formated address fields with associated values
			$formatedAddressFieldsValuesList = array();
			foreach($customerAddresses as $address)
			{
				$tmpAddress = new Address($address['id_address']);
				
				$formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields'] = AddressFormat::getOrderedAddressFields($address['id_country']);
				$formatedAddressFieldsValuesList[$address['id_address']]['formated_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
					$tmpAddress,
					$formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields']);
				
				unset($tmpAddress);
			}
			$this->context->smarty->assign(array(
				'addresses' => $customerAddresses,
				'formatedAddressFieldsValuesList' => $formatedAddressFieldsValuesList));

			/* Setting default addresses for cart */
			if ((!isset($this->context->cart->id_address_delivery) OR empty($this->context->cart->id_address_delivery)) AND sizeof($customerAddresses))
			{
				$this->context->cart->id_address_delivery = (int)($customerAddresses[0]['id_address']);
				$update = 1;
			}
			if ((!isset($this->context->cart->id_address_invoice) OR empty($this->context->cart->id_address_invoice)) AND sizeof($customerAddresses))
			{
				$this->context->cart->id_address_invoice = (int)($customerAddresses[0]['id_address']);
				$update = 1;
			}
			/* Update cart addresses only if needed */
			if (isset($update) AND $update)
				$this->context->cart->update();

			/* If delivery address is valid in cart, assign it to Smarty */
			if (isset($this->context->cart->id_address_delivery))
			{
				$deliveryAddress = new Address((int)($this->context->cart->id_address_delivery));
				if (Validate::isLoadedObject($deliveryAddress) AND ($deliveryAddress->id_customer == $customer->id))
					$this->context->smarty->assign('delivery', $deliveryAddress);
			}

			/* If invoice address is valid in cart, assign it to Smarty */
			if (isset($this->context->cart->id_address_invoice))
			{
				$invoiceAddress = new Address((int)($this->context->cart->id_address_invoice));
				if (Validate::isLoadedObject($invoiceAddress) AND ($invoiceAddress->id_customer == $customer->id))
					$this->context->smarty->assign('invoice', $invoiceAddress);
			}
		}
		if ($oldMessage = Message::getMessageByCartId((int)($this->context->cart->id)))
			$this->context->smarty->assign('oldMessage', $oldMessage['message']);
	}
	
	protected function _assignCarrier()
	{
		$address = new Address($this->context->cart->id_address_delivery);
		$id_zone = Address::getZoneById($address->id);
		$carriers = Carrier::getCarriersForOrder($id_zone, $this->context->customer->getGroups());

		$this->context->smarty->assign(array(
			'checked' => $this->_setDefaultCarrierSelection($carriers),
			'carriers' => $carriers,
			'default_carrier' => (int)(Configuration::get('PS_CARRIER_DEFAULT'))
		));
		$this->context->smarty->assign(array(
			'HOOK_EXTRACARRIER' => Module::hookExec('extraCarrier', array('address' => $address)),
			'HOOK_BEFORECARRIER' => Module::hookExec('beforeCarrier', array('carriers' => $carriers))
		));
	}
	
	protected function _assignWrappingAndTOS()
	{
		// Wrapping fees
		$wrapping_fees = (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE'));
		$wrapping_fees_tax = new Tax(Configuration::get('PS_GIFT_WRAPPING_TAX'));
		$wrapping_fees_tax_inc = $wrapping_fees * (1 + (((float)($wrapping_fees_tax->rate) / 100)));
		
		// TOS
		$cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
		$this->link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, true);
		if (!strpos($this->link_conditions, '?'))
			$this->link_conditions .= '?content_only=1';
		else
			$this->link_conditions .= '&content_only=1';
		
		$this->context->smarty->assign(array(
			'checkedTOS' => (int)($this->context->cookie->checkedTOS),
			'recyclablePackAllowed' => (int)(Configuration::get('PS_RECYCLABLE_PACK')),
			'giftAllowed' => (int)(Configuration::get('PS_GIFT_WRAPPING')),
			'cms_id' => (int)(Configuration::get('PS_CONDITIONS_CMS_ID')),
			'conditions' => (int)(Configuration::get('PS_CONDITIONS')),
			'link_conditions' => $this->link_conditions,
			'recyclable' => (int)($this->context->cart->recyclable),
			'gift_wrapping_price' => (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE')),
			'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
			'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency)));
	}
	
	protected function _assignPayment()
	{
		$this->context->smarty->assign(array(
		    'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
			'HOOK_PAYMENT' => Module::hookExecPayment()
		));
	}
	
	/**
	 * Set id_carrier to 0 (no shipping price)
	 *
	 */
	protected function _setNoCarrier()
	{
		$this->context->cart->id_carrier = 0;
		$this->context->cart->update();
	}
	
	/**
	 * Decides what the default carrier is and update the cart with it
	 *
	 * @param array $carriers
	 * @return number the id of the default carrier
	 */
	protected function _setDefaultCarrierSelection($carriers)
	{
		if (sizeof($carriers))
		{
			$defaultCarrierIsPresent = false;
			if ((int)$this->context->cart->id_carrier != 0)
				foreach ($carriers AS $carrier)
					if ($carrier['id_carrier'] == (int)$this->context->cart->id_carrier)
						$defaultCarrierIsPresent = true;
			if (!$defaultCarrierIsPresent)
				foreach ($carriers AS $carrier)
					if ($carrier['id_carrier'] == (int)Configuration::get('PS_CARRIER_DEFAULT'))
					{
						$defaultCarrierIsPresent = true;
						$this->context->cart->id_carrier = (int)$carrier['id_carrier'];
					}
			if (!$defaultCarrierIsPresent)
				$this->context->cart->id_carrier = (int)$carriers[0]['id_carrier'];
		}
		else
			$this->context->cart->id_carrier = 0;
		if ($this->context->cart->update())
			return $this->context->cart->id_carrier;
		return 0;
	}
	
}

