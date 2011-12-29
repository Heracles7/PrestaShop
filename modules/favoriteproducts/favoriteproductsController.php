<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ModuleFavoriteproductsController extends ModuleController
{
	/**
	 * @var int
	 */
	public $id_product;

	public function init()
	{
		parent::init();

		require_once(dirname(__FILE__).'/FavoriteProduct.php');
		$this->id_product = (int)Tools::getValue('id_product');
	}

	public function postProcess()
	{
		if ($this->process == 'remove')
			$this->processRemove();
		else if ($this->process == 'add')
			$this->processAdd();
	}

	/**
	 * Remove a favorite product
	 */
	public function processRemove()
	{
		// check if product exists
		$product = new Product($this->id_product);
		if (!Validate::isLoadedObject($product))
			die('0');

		$favoriteProduct = FavoriteProduct::getFavoriteProduct((int)Context::getContext()->cookie->id_customer, (int)$product->id);
		if ($favoriteProduct && $favoriteProduct->delete())
			die('0');
		die(1);
	}

	/**
	 * Add a favorite product
	 */
	public function processAdd()
	{
		$product = new Product($this->id_product);
		// check if product exists
		if (!Validate::isLoadedObject($product) || FavoriteProduct::isCustomerFavoriteProduct((int)Context::getContext()->cookie->id_customer, (int)$product->id))
			die('1');
		$favoriteProduct = new FavoriteProduct();
		$favoriteProduct->id_product = $product->id;
		$favoriteProduct->id_customer = (int)Context::getContext()->cookie->id_customer;
		$favoriteProduct->id_shop = (int)Context::getContext()->shop->getID(true);
		if ($favoriteProduct->add())
			die('0');
		die(1);
	}

	public function initContent()
	{
		parent::initContent();
		if ($this->process == 'account')
			$this->assignAccount();
	}

	/**
	 * Prepare account page
	 */
	public function assignAccount()
	{
		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=auth&redirect=module&module=favoriteproducts&action=account');

		if (Context::getContext()->customer->id)
		{
			$this->context->smarty->assign('favoriteProducts', FavoriteProduct::getFavoriteProducts((int)Context::getContext()->customer->id, (int)Context::getContext()->language->id));
			$this->setTemplate('favoriteproducts-account.tpl');
		}
	}
}