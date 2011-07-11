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
*  @version  Release: $Revision: 7048 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class BlockCurrencies extends Module
{
	public function __construct()
	{
		$this->name = 'blockcurrencies';
		$this->tab = 'front_office_features';
		$this->version = 0.1;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();
		
		$this->displayName = $this->l('Currency block');
		$this->description = $this->l('Adds a block for selecting a currency.');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('top') AND $this->registerHook('header'));
	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookTop($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
	
		global $smarty;
		
		$id_current_shop = $this->shopID;
		$currencies = Currency::getCurrencies(false, 1, $id_current_shop);
		if (!sizeof($currencies))
			return '';
		$smarty->assign('currencies', $currencies);
		return $this->display(__FILE__, 'blockcurrencies.tpl');
	}
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$context = Context::getContext();
		$context->controller->addCSS(($this->_path).'blockcurrencies.css', 'all');
	}
}


