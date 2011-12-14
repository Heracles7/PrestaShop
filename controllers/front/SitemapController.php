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

class SitemapControllerCore extends FrontController
{
	public $php_self = 'sitemap';

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'sitemap.css');
		$this->addJS(_THEME_JS_DIR_.'tools/treeManagement.js');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->context->smarty->assign('categoriesTree', Category::getRootCategory()->recurseLiteCategTree(0));
		$this->context->smarty->assign('categoriescmsTree', CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1));
		$this->context->smarty->assign('voucherAllowed', (int)Configuration::get('PS_VOUCHERS'));

		$blockmanufacturer = Module::getInstanceByName('blockmanufacturer');
		$blocksupplier = Module::getInstanceByName('blocksupplier');
		$this->context->smarty->assign('display_manufacturer_link', (((int)$blockmanufacturer->id) ? true : false));
		$this->context->smarty->assign('display_supplier_link', (((int)$blocksupplier->id) ? true : false));
		$this->context->smarty->assign('PS_DISPLAY_SUPPLIERS', Configuration::get('PS_DISPLAY_SUPPLIERS'));
		$this->context->smarty->assign('display_store', Configuration::get('PS_STORES_DISPLAY_SITEMAP'));

		$this->setTemplate(_PS_THEME_DIR_.'sitemap.tpl');
		parent::initContent();
	}
}
