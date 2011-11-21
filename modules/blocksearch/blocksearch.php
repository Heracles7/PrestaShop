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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockSearch extends Module
{
	public function __construct()
	{
		$this->name = 'blocksearch';
		$this->tab = 'search_filter';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Quick Search block');
		$this->description = $this->l('Adds a block with a quick search field.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('header'))
			return false;
		return true;
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
		{
			Tools::addCSS(_PS_CSS_DIR_.'jquery.autocomplete.css');
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery.autocomplete.js');
		}
		Tools::addCSS(_THEME_CSS_DIR_.'product_list.css');
		Tools::addCSS(($this->_path).'blocksearch.css', 'all');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		$this->calculHookCommon($params);
		return $this->display(__FILE__, 'blocksearch.tpl');
	}

	public function hookTop($params)
	{
		$this->calculHookCommon($params);
		return $this->display(__FILE__, 'blocksearch-top.tpl');
	}

	/**
	 * _hookAll has to be called in each hookXXX methods. This is made to avoid code duplication.
	 *
	 * @param mixed $params
	 * @return void
	 */
	private function calculHookCommon($params)
	{
		$this->context->smarty->assign('ENT_QUOTES', ENT_QUOTES);
		$this->context->smarty->assign('search_ssl', Tools::usingSecureMode());
		$this->context->smarty->assign('ajaxsearch', Configuration::get('PS_SEARCH_AJAX'));
		$this->context->smarty->assign('instantsearch', Configuration::get('PS_INSTANT_SEARCH'));

		return true;
	}
}
