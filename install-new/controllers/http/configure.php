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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Step 4 : configure the shop, admin access and modules preactivations
 */
class InstallControllerHttpConfigure extends InstallControllerHttp
{
	/**
	 * @see InstallAbstractModel::processNextStep()
	 */
	public function processNextStep()
	{
		// Save shop configuration
		$this->session->shop_name = trim(Tools::getValue('shop_name'));
		$this->session->shop_activity = Tools::getValue('shop_activity');
		$this->session->shop_country = Tools::getValue('shop_country');
		$this->session->shop_timezone = Tools::getValue('shop_timezone');

		// Save admin configuration
		$this->session->admin_firstname = trim(Tools::getValue('admin_firstname'));
		$this->session->admin_lastname = trim(Tools::getValue('admin_lastname'));
		$this->session->admin_email = trim(Tools::getValue('admin_email'));
		$this->session->send_informations = Tools::getValue('send_informations');

		// If password fields are empty, but are already stored in session, do not fill them again
		if (!$this->session->admin_password || trim(Tools::getValue('admin_password')))
			$this->session->admin_password = trim(Tools::getValue('admin_password'));

		if (!$this->session->admin_password_confirm || trim(Tools::getValue('admin_password_confirm')))
			$this->session->admin_password_confirm = trim(Tools::getValue('admin_password_confirm'));

		// Save partners preactivation configuration
		$this->session->partners = array();
		$partners = Tools::getValue('partner');
		if (is_array($partners))
		{
			// Check all selected partners and store their fields
			$session_partners = array();
			foreach ($partners as $partner_id => $state)
				$session_partners[$partner_id] = (isset($_POST['partner_fields'][$partner_id])) ? $_POST['partner_fields'][$partner_id] : array();
			$this->session->partners = $session_partners;
		}
	}

	/**
	 * @see InstallAbstractModel::validate()
	 */
	public function validate()
	{
		// List of required fields
		$required_fields = array('shop_name', 'shop_country', 'shop_timezone', 'admin_firstname', 'admin_lastname', 'admin_email', 'admin_password');
		foreach ($required_fields as $field)
			if (!$this->session->$field)
				$this->errors[$field] = $this->l('Field required');

		// Check shop name
		if ($this->session->shop_name && !Validate::isGenericName($this->session->shop_name))
			$this->errors['shop_name'] = $this->l('Invalid shop name');

		// Check admin name
		if ($this->session->admin_firstname && !Validate::isGenericName($this->session->admin_firstname))
			$this->errors['admin_firstname'] = $this->l('Your firstname contains some invalid characters');

		if ($this->session->admin_lastname && !Validate::isGenericName($this->session->admin_lastname))
			$this->errors['admin_lastname'] = $this->l('Your lastname contains some invalid characters');

		// Check passwords
		if ($this->session->admin_password)
		{
			if (!Validate::isPasswdAdmin($this->session->admin_password))
				$this->errors['admin_password'] = $this->l('The password is incorrect (alphanumeric string at least 8 characters)');
			else if ($this->session->admin_password != $this->session->admin_password_confirm)
				$this->errors['admin_password'] = $this->l('Password and its confirmation are different');
		}

		// Check email
		if ($this->session->admin_email && !Validate::isEmail($this->session->admin_email))
			$this->errors['admin_email'] = $this->l('This e-mail address is invalid');

		return count($this->errors) ? false : true;
	}

	public function process()
	{
		if (Tools::getValue('uploadLogo'))
			$this->processUploadLogo();
		else if (Tools::getValue('timezoneByIso'))
			$this->processTimezoneByIso();
		else if (Tools::getValue('getPartners'))
			$this->processGetPartners();
		else if (Tools::getValue('getPartnersFields'))
			$this->processGetPartnersFields();
	}

	/**
	 * Process the upload of new logo
	 */
	public function processUploadLogo()
	{
		$error = '';
		if (isset($_FILES['fileToUpload']))
		{
			$file = $_FILES['fileToUpload'];

			// If error code is not 0, an error occured during upload
			if ($file['error'] != 0)
			{
				$upload_errors = array(
					1 => $this->l('The uploaded file exceeds the upload_max_filesize directive in php.ini'),
					2 => $this->l('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'),
					3 => $this->l('The uploaded file was only partially uploaded'),
					4 => $this->l('No file was uploaded'),
					6 => $this->l('Missing a temporary folder'),
					7 => $this->l('Failed to write file to disk'),
					8 => $this->l('File upload stopped by extension'),
				);

				if (isset($upload_errors[$file['error']]))
					$error = $upload_errors[$file['error']];
				else
					$error = $this->l('No error code available');
			}
			// Check if no error during creation of tmp file
			else if (!$file['tmp_name'] || $file['tmp_name'] == 'none')
			{
				$error = $this->l('Missing a temporary folder');
			}
			// No error, let's update the file
			else
			{
				list($width, $height, $type) = getimagesize($file['tmp_name']);

				// Check if this is really an image
				if ($height == 0)
					$error = $this->l('This is not a valid image file');
				// Resize image
				else
				{
					$newheight = ($height > 500) ? 500 : $height;
					$percent = $newheight / $height;
					$newwidth = $width * $percent;
					$newheight = $height * $percent;
					$thumb = imagecreatetruecolor($newwidth, $newheight);
					switch ($type)
					{
						case 1:
							$source = imagecreatefromgif($file['tmp_name']);
						break;

						case 2:
							$source = imagecreatefromjpeg($file['tmp_name']);
						break;

						case 3:
							$source = imagecreatefrompng($file['tmp_name']);
						break;

						default:
							$error = $this->l('Image type is not supported');
					}

					if (!$error)
					{
						imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
						if (!is_writable(_PS_ROOT_DIR_.'/img/logo.jpg'))
							$error = $this->l('Image folder is not writable');
						else if (!imagejpeg($thumb, _PS_ROOT_DIR_.'/img/logo.jpg', 90))
							$error = $this->l('Cannot upload the file');
					}
				}
			}
		}

		$this->ajaxJsonAnswer(($error) ? false : true, $error);
	}

	/**
	 * Obtain the timezone associated to an iso
	 */
	public function processTimezoneByIso()
	{
		$timezone = $this->getTimezoneByIso(Tools::getValue('iso'));
		$this->ajaxJsonAnswer(($timezone) ? true : false, $timezone);
	}

	/**
	 * Obtain a translation from presintall XML file
	 *
	 * @param SimplexmlElement $xml
	 * @param string $xpath
	 * @return string
	 */
	public function getPreinstallXmlLang(SimplexmlElement $xml, $xpath)
	{
		$lang = $this->language->getLanguageIso();
		$translation = $xml->xpath($xpath.'[@iso="'.$lang.'"]');
		if (!$translation && $lang != 'en')
			$translation = $xml->xpath($xpath.'[@iso="en"]');
		if (!$translation)
			$translation = $xml->xpath($xpath);
		return ($translation) ? (string)$translation[0] : '';
	}

	/**
	 * Get list of partners from PrestaShop website
	 */
	public function processGetPartners()
	{
		$this->iso = Tools::getValue('iso');
		if (!$this->iso)
			$this->ajaxJsonAnswer(false);

		// Load partners XML file from prestashop.com
		$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 3)));
		$content = @file_get_contents('http://api.prestashop.com/partner/preactivation/partners.php?version=1.1', false, $stream_context);
		if (!$xml = @simplexml_load_string($content))
			$this->ajaxJsonAnswer(false, $this->l('Cannot load partners from PrestaShop website'));

		// Browse all partners
		$partners = array();
		foreach ($xml->partner as $partner)
		{
			// Partner available for current language ?
			if (!$partner->xpath('countries[country="'.$this->iso.'"]'))
				continue;

			$partner_id = (string)$partner->key;
			if (!isset($this->session->shop_name))
				$checked = ($partner->prechecked) ? true : false;
			else
				$checked = (isset($this->session->partners[$partner_id])) ? true : false;

			$partners[$partner_id] = array(
				'name' => (string)$partner->name,
				'label' => $this->getPreinstallXmlLang($partner, 'labels/label'),
				'description' => $this->getPreinstallXmlLang($partner, 'descriptions/description'),
				'logo' => $partner->logo_medium,
				'checked' => $checked,
			);
		}

		// If no partners, don't displayany preactivation HTML
		if (!$partners)
			$this->ajaxJsonAnswer(false);

		// Render partners
		$this->partners = $partners;
		$html = $this->displayTemplate('partners', true);
		$this->ajaxJsonAnswer(true, $html);
	}

	/**
	 * Get fields of a partner for a country as HTML
	 */
	public function processGetPartnersFields()
	{
		$this->partner_id = Tools::getValue('partner_id');
		$this->iso = Tools::getValue('iso');
		if (!$this->partner_id || !$this->iso)
			$this->ajaxJsonAnswer(false);

		$this->fields = $this->getPartnersFields($this->partner_id, $this->iso);

		if (!$this->fields)
			$this->ajaxJsonAnswer(false);

		// Render fields
		$html = $this->displayTemplate('partners_fields', true);
		$this->ajaxJsonAnswer(true, $html);
	}

	/**
	 * Get list of fields of a partner for a country
	 *
	 * @param string $partner_id
	 * @param string $iso
	 * @return array
	 */
	public function getPartnersFields($partner_id, $iso)
	{
		// Load partners fields XML file from prestashop.com
		$stream_context = @stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5)));
		$content = @file_get_contents('http://api.prestashop.com/partner/preactivation/fields.php?version=1.1&partner='.$partner_id.'&country_iso_code='.$iso, false, $stream_context);
		if (!$xml = @simplexml_load_string($content))
			$this->ajaxJsonAnswer(false, $this->l('Cannot load partners fields from PrestaShop website'));

		// Browse all fields
		$fields = array();
		foreach ($xml->field as $field)
		{
			$key = (string)$field->key;
			$data = array(
				'type' => (string)$field->type,
				'label' => $this->getPreinstallXmlLang($field, 'labels/label'),
				'help' => $this->getPreinstallXmlLang($field, 'helps/help'),
				'value' => (isset($this->session->partners[$partner_id][$key])) ? $this->session->partners[$partner_id][$key] : (string)$field->default,
			);

			switch ($data['type'])
			{
				case 'text' :
				case 'password' :
					$data['size'] = (string)$field->size;
				break;

				case 'radio' :
				case 'select' :
					$data['list'] = array();
					foreach ($field->values as $value)
						$data['list'][(string)$value->value] = $this->getPreinstallXmlLang($value, 'labels/label');
				break;

				case 'date' :
					if (!is_array($data['value']))
						$data['value'] = array(
							'year' => 0,
							'month' => 0,
							'day' => 0,
						);
				break;
			}

			$fields[$key] = $data;
		}

		return $fields;
	}

	/**
	 * Get list of timezones
	 *
	 * @return array
	 */
	public function getTimezones()
	{
		if (!is_null($this->cache_timezones))
			return;

		if (!file_exists(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml'))
			return array();

		$xml = simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml');
		$timezones = array();
		foreach ($xml->entities->timezone as $timezone)
			$timezones[] = (string)$timezone['name'];
		return $timezones;
	}

	/**
	 * Get a timezone associated to an iso
	 *
	 * @param string $iso
	 * @return string
	 */
	public function getTimezoneByIso($iso)
	{
		if (!file_exists(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml'))
			return '';

		$xml = simplexml_load_file(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml');
		$timezones = array();
		foreach ($xml->relation as $relation)
			$timezones[(string)$relation['iso']] = (string)$relation['zone'];
		return isset($timezones[$iso]) ? $timezones[$iso] : '';
	}

	/**
	 * @see InstallAbstractModel::display()
	 */
	public function display()
	{
		// List of activities
		$list_activities = array(
			$this->l('Lingerie and Adult'),
			$this->l('Animals and Pets'),
			$this->l('Art and Culture'),
			$this->l('Babies'),
			$this->l('Beauty and Personal Care'),
			$this->l('Cars'),
			$this->l('Computer Hardware and Software'),
			$this->l('Download'),
			$this->l('Fashion and accessories'),
			$this->l('Flowers, Gifts and Crafts'),
			$this->l('Food and beverage'),
			$this->l('HiFi, Photo and Video'),
			$this->l('Home and Garden'),
			$this->l('Home Appliances'),
			$this->l('Jewelry'),
			$this->l('Mobile and Telecom'),
			$this->l('Services'),
			$this->l('Shoes and accessories'),
			$this->l('Sports and Entertainment'),
			$this->l('Travel'),
		);
		sort($list_activities);
		$this->list_activities = $list_activities;

		$this->displayTemplate('configure');
	}

	/**
	 * Helper to display error for a field
	 *
	 * @param unknown_type $field
	 */
	public function displayError($field)
	{
		if (!isset($this->errors[$field]))
			return;

		return '<span class="result aligned errorTxt">'.$this->errors[$field].'</span>';
	}
}
