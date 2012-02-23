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
*  @version  Release: $Revision: 7095 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AddressControllerCore extends FrontController
{
	public $auth = true;
	public $guestAllowed = true;
	public $php_self = 'address';
	public $authRedirection = 'addresses';
	public $ssl = true;

	/**
	 * @var Address Current address
	 */
	protected $_address;

	/**
	 * Set default medias for this controller
	 */
	public function setMedia()
	{
		parent::setMedia();
		$this->addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
	}

	/**
	 * Initialize address controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		// Get address ID
		$id_address = 0;
		if ($this->ajax && Tools::isSubmit('type'))
		{
			if (Tools::getValue('type') == 'delivery' && isset($this->context->cart->id_address_delivery))
				$id_address = (int)$this->context->cart->id_address_delivery;
			else if (Tools::getValue('type') == 'invoice' && isset($this->context->cart->id_address_invoice)
						&& $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery)
				$id_address = (int)$this->context->cart->id_address_invoice;
			else
				exit;
		}
		else
			$id_address = (int)Tools::getValue('id_address', 0);

		// Initialize address
		if ($id_address)
		{
			$this->_address = new Address($id_address);
			if (Validate::isLoadedObject($this->_address) && Customer::customerHasAddress($this->context->customer->id, $id_address))
			{
				if (Tools::isSubmit('delete'))
				{
					if ($this->context->cart->id_address_invoice == $this->_address->id)
						unset($this->context->cart->id_address_invoice);
					if ($this->context->cart->id_address_delivery == $this->_address->id)
						unset($this->context->cart->id_address_delivery);
					if ($this->_address->delete())
						Tools::redirect('index.php?controller=addresses');
					$this->errors[] = Tools::displayError('This address cannot be deleted.');
				}
			}
			else if ($this->ajax)
				exit;
			else
				Tools::redirect('index.php?controller=addresses');
		}
	}

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitAddress'))
			$this->processSubmitAddress();
		else if (!Validate::isLoadedObject($this->_address) && Validate::isLoadedObject($this->context->customer))
		{
			$_POST['firstname'] = $this->context->customer->firstname;
			$_POST['lastname'] = $this->context->customer->lastname;
		}
	}

	/**
	 * Process changes on an address
	 */
	protected function processSubmitAddress()
	{
		/*if ($this->context->customer->is_guest)
			Tools::redirect('index.php?controller=addresses');*/

		$address = new Address();
		$this->errors = $address->validateController();
		$address->id_customer = (int)$this->context->customer->id;

		// Check page token
		if ($this->isTokenValid())
			$this->errors[] = Tools::displayError('Invalid token');

		// Check phone
		if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))
			$this->errors[] = Tools::displayError('You must register at least one phone number');

		// Check country
		if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
			throw new PrestaShopException('Country cannot be loaded with address->id_country');

		if ((int)$country->contains_states && !(int)$address->id_state)
			$this->errors[] = Tools::displayError('This country requires a state selection.');

		// US customer: normalize the address
		if ($address->id_country == Country::getByIso('US'))
		{
			include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
			$normalize = new AddressStandardizationSolution;
			$address->address1 = $normalize->AddressLineStandardization($address->address1);
			$address->address2 = $normalize->AddressLineStandardization($address->address2);
		}

		// Check country zip code
		$zip_code_format = $country->zip_code_format;
		if ($country->need_zip_code)
		{
			if (($postcode = Tools::getValue('postcode')) && $zip_code_format)
			{
				$zip_regexp = '/^'.$zip_code_format.'$/ui';
				$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
				$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
				$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
				$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
				$zip_regexp = str_replace('C', $country->iso_code, $zip_regexp);
				if (!preg_match($zip_regexp, $postcode))
					$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '
											.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:')
											.' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
			}
			else if ($zip_code_format)
				$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is required.');
			else if ($postcode && !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
				$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.')
										.'<br />'.Tools::displayError('Must be typed as follows:').' '
										.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
		}

		// Check country DNI
		if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))))
			$this->errors[] = Tools::displayError('Identification number is incorrect or has already been used.');
		else if (!$country->isNeedDni())
			$address->dni = null;
		
		// Check if the alias exists
		if (!empty($_POST['alias'])
			&& (int)$this->context->customer->id > 0
			&& Db::getInstance()->getValue('
				SELECT count(*)
				FROM '._DB_PREFIX_.'address
				WHERE `alias` = \''.pSql($_POST['alias']).'\'
				AND id_address != '.(int)Tools::getValue('id_address').'
				AND id_customer = '.(int)$this->context->customer->id.'
				AND deleted = 0') > 0)
			$this->errors[] = sprintf(Tools::displayError('The alias "%s" is already used, please chose an other one.'), Tools::safeOutput($_POST['alias']));

		// Don't continue this process if we have errors !
		if ($this->errors && !Tools::isSubmit('ajax'))
			return;

		// If we edit this address, delete old address and create a new one
		if (Validate::isLoadedObject($this->_address))
		{
			if (Validate::isLoadedObject($country) && !$country->contains_states)
				$address->id_state = 0;
			$address_old = $this->_address;
			if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id))
			{
				if ($address_old->isUsed())
				{
					$address_old->delete();
					if (!Tools::isSubmit('ajax'))
					{
						$to_update = false;
						if ($this->context->cart->id_address_invoice == $address_old->id)
						{
							$to_update = true;
							$this->context->cart->id_address_invoice = 0;
						}
						if ($this->context->cart->id_address_delivery == $address_old->id)
						{
							$to_update = true;
							$this->context->cart->id_address_delivery = 0;
						}
						if ($to_update)
							$this->context->cart->update();
					}
				}
				else
				{
					$address->id = (int)($address_old->id);
					$address->date_add = $address_old->date_add;
				}
			}
		}

		// Save address
		if ($result = $address->save())
		{
			// In order to select this new address : order-address.tpl
			if ((bool)Tools::getValue('select_address', false) || ($this->ajax && Tools::getValue('type') == 'invoice'))
			{
				// This new adress is for invoice_adress, select it
				$this->context->cart->id_address_invoice = (int)$address->id;
				$this->context->cart->update();
			}

			if ($this->ajax)
			{
				$return = array(
					'hasError' => (bool)$this->errors,
					'errors' => $this->errors,
					'id_address_delivery' => $this->context->cart->id_address_delivery,
					'id_address_invoice' => $this->context->cart->id_address_invoice
				);
				die(Tools::jsonEncode($return));
			}

			// Redirect to old page or current page
			if ($back = Tools::getValue('back'))
			{
				$mod = Tools::getValue('mod');
				Tools::redirect('index.php?controller='.$back.($mod ? '&back='.$mod : ''));
			}
			else
				Tools::redirect('index.php?controller=addresses');
		}
		$this->errors[] = Tools::displayError('An error occurred while updating your address.');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		/* Secure restriction for guest */
		if ($this->context->customer->is_guest)
			Tools::redirect('index.php?controller=addresses');

		$this->assignCountries();
		$this->assignVatNumber();
		$this->assignAddressFormat();

		// Assign common vars
		$this->context->smarty->assign(array(
			'ajaxurl' => _MODULE_DIR_,
			'errors' => $this->errors,
			'token' => Tools::getToken(false),
			'select_address' => (int)Tools::getValue('select_address'),
			'address' => $this->_address,
			'id_address' => (Validate::isLoadedObject($this->_address)) ? $this->_address->id : 0,
		));

		if ($back = Tools::getValue('back'))
			$this->context->smarty->assign('back', Tools::safeOutput($back));
		if ($mod = Tools::getValue('mod'))
			$this->context->smarty->assign('mod', Tools::safeOutput($mod));
		if (isset($this->context->cookie->account_created))
		{
			$this->context->smarty->assign('account_created', 1);
			unset($this->context->cookie->account_created);
		}

		$this->setTemplate(_PS_THEME_DIR_.'address.tpl');
		parent::initContent();
	}

	/**
	 * Assign template vars related to countries display
	 */
	protected function assignCountries()
	{
		// Get selected country
		if (Tools::isSubmit('id_country') && !is_null(Tools::getValue('id_country')) && is_numeric(Tools::getValue('id_country')))
			$selected_country = (int)Tools::getValue('id_country');
		else if (isset($this->_address) && isset($this->_address->id_country) && !empty($this->_address->id_country) && is_numeric($this->_address->id_country))
			$selected_country = (int)$this->_address->id_country;
		else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$array = preg_split('/,|-/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (!Validate::isLanguageIsoCode($array[0]) || !($selected_country = Country::getByIso($array[0])))
				$selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
		}
		else
			$selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');

		// Generate countries list
		if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
			$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
		else
			$countries = Country::getCountries($this->context->language->id, true);

		// @todo use helper
		$list = '';
		foreach ($countries as $country)
		{
			$selected = ($country['id_country'] == $selected_country) ? 'selected="selected"' : '';
			$list .= '<option value="'.(int)$country['id_country'].'" '.$selected.'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}

		// Assign vars
		$this->context->smarty->assign(array(
			'countries_list' => $list,
			'countries' => $countries,
		));
	}

	/**
	 * Assign template vars related to address format
	 */
	protected function assignAddressFormat()
	{
		$id_country = is_null($this->_address)? 0 : (int)$this->_address->id_country;
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
		$this->context->smarty->assign('ordered_adr_fields', $dlv_adr_fields);
	}

	/**
	 * Assign template vars related to vat number
	 * @todo move this in vatnumber module !
	 */
	protected function assignVatNumber()
	{
		$vat_number_exists = file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
		$vat_number_management = Configuration::get('VATNUMBER_MANAGEMENT');
		if ($vat_number_management && $vat_number_exists)
			include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

		if ($vat_number_management && $vat_number_exists && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
			$vat_display = 2;
		else if ($vat_number_management)
			$vat_display = 1;
		else
			$vat_display = 0;

		$this->context->smarty->assign(array(
			'vatnumber_ajax_call' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
			'vat_display' => $vat_display,
		));
	}

	public function displayAjax()
	{
		if (count($this->errors))
		{
			$return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors
			);
			die(Tools::jsonEncode($return));
		}
	}
}
