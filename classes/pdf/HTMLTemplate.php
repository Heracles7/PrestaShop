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
abstract class HTMLTemplateCore
{
	public $title;
	public $date;
	public $available_in_your_account = true;
	public $smarty;
    public $shop;

	/**
	 * Returns the template's HTML header
	 * @return string HTML header
	 */
	public function getHeader()
	{
        $shop_name = '';
        if (Validate::isLoadedObject($this->shop))
            $shop_name = $this->shop->name;

		$this->smarty->assign(array(
			'logo_path' => $this->getLogo(),
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->title,
			'date' => $this->date,
			'shop_name' => $shop_name
		));

		return $this->smarty->fetch(_PS_THEME_DIR_.'/pdf/header.tpl');
	}

	/**
	 * Returns the template's HTML footer
	 * @return string HTML footer
	 */
	public function getFooter()
	{
        $shop_address = $this->getShopAddress();

		$this->smarty->assign(array(
			'available_in_your_account' => $this->available_in_your_account,
			'shop_address' => $shop_address,
			'shop_fax' => Configuration::get('PS_SHOP_FAX'),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
			'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
			'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT')
		));
		return $this->smarty->fetch(_PS_THEME_DIR_.'/pdf/footer.tpl');
	}

    /**
     * Returns the shop address
     * @return string
     */
    protected function getShopAddress()
    {
        $shop_address = '';
        if (Validate::isLoadedObject($this->shop))
        {
            $shop_address_obj = $this->shop->getAddress();
            if (isset($shop_address_obj) && $shop_address_obj instanceof Address)
                $shop_address = AddressFormat::generateAddress($shop_address_obj, array(), ' - ', ' ');return $shop_address;
            return $shop_address;
        }

        return $shop_address;
    }

    /**
	 * Returns the invoice logo
	 */
    protected function getLogo()
    {
		$logo = '';

		if (file_exists(_PS_IMG_DIR_.'logo_invoice.jpg'))
			$logo = _PS_IMG_.'logo_invoice.jpg';
		else if (file_exists(_PS_IMG_DIR_.'logo.jpg'))
			$logo = _PS_IMG_.'logo.jpg';

		return $logo;
    }

	/**
	* Assign hook data
	*
	* @param $object generally the object used in the constructor
	*/
	public function assignHookData($object)
	{
		$template = ucfirst(str_replace('HTMLTemplate', '', get_class($this)));
		$hook_name = 'displayPDF'.$template;

		$this->smarty->assign(array(
			'HOOK_DISPLAY_PDF' => Hook::exec($hook_name, array('object' => $object)),
		));
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	abstract public function getContent();


	/**
	 * Returns the template filename
	 * @return string filename
	 */
    abstract public function getFilename();

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
    abstract public function getBulkFilename();




	/**
	 * Translatation method
	 * @param string $string
	 * @return string translated text
	 */
	protected static function l($string)
	{
		$iso = Context::getContext()->language->iso_code;

        if (!Validate::isLangIsoCode($iso))
            die('Invalid iso lang ('.$iso.')');

		if (@!include(_PS_THEME_DIR_.'pdf/'.$iso.'.php'))
			die('Cannot include PDF translation language file : '._PS_THEME_DIR_.'pdf/'.$iso.'.php');

		if (!isset($_LANGPDF) OR !is_array($_LANGPDF))
			return str_replace('"', '&quot;', $string);
		$key = md5(str_replace('\'', '\\\'', $string));
		$str = (key_exists('PDF_invoice'.$key, $_LANGPDF) ? $_LANGPDF['PDF_invoice'.$key] : $string);

		return $str;
	}
}

