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

class SearchControllerCore extends FrontController
{
	public $instantSearch;
	public $ajaxSearch;
	
	public function __construct()
	{
		$this->php_self = 'search.php';
		
		parent::__construct();
		
		$this->instantSearch = Tools::getValue('instantSearch');
		$this->ajaxSearch = Tools::getValue('ajaxSearch');
	}
	
	public function preProcess()
	{
		parent::preProcess();

		$query = urldecode(Tools::getValue('q'));
		if ($this->ajaxSearch)
		{
			$searchResults = Search::find((int)(Tools::getValue('id_lang')), $query, 1, 10, 'position', 'desc', true, true, (int)$this->id_current_shop);
			foreach ($searchResults AS &$product)
				$product['product_link'] = self::$link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);
			die(Tools::jsonEncode($searchResults));
		}
		
		if ($this->instantSearch && !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$search = Search::find((int)(self::$cookie->id_lang), $query, $this->p, $this->n, $this->orderBy, $this->orderWay, false, true, (int)$this->id_current_shop);
			Module::hookExec('search', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
			$this->smarty->assign(array(
			'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
			'search_products' => $search['result'],
			'nbProducts' => $search['total'],
			'search_query' => $query,
			'instantSearch' => $this->instantSearch,
			'homeSize' => Image::getSize('home')));
		}
		elseif ($query = Tools::getValue('search_query', Tools::getValue('ref')) AND !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$search = Search::find((int)(self::$cookie->id_lang), $query, false, $this->p, $this->n, $this->orderBy, $this->orderWay, true, (int)$this->id_current_shop);
			Module::hookExec('search', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
			$this->smarty->assign(array(
			'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
			'search_products' => $search['result'],
			'nbProducts' => $search['total'],
			'search_query' => $query,
			'homeSize' => Image::getSize('home')));
		}
		elseif ($tag = urldecode(Tools::getValue('tag')) AND !is_array($tag))
		{
			$nbProducts = (int)(Search::searchTag((int)(self::$cookie->id_lang), $tag, true));
			$this->pagination($nbProducts);
			$result = Search::searchTag((int)(self::$cookie->id_lang), $tag, false, $this->p, $this->n, $this->orderBy, $this->orderWay, true, (int)$this->id_current_shop);
			Module::hookExec('search', array('expr' => $tag, 'total' => sizeof($result)));
			$this->smarty->assign(array(
			'search_tag' => $tag,
			'products' => $result, // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
			'search_products' => $result,
			'nbProducts' => $nbProducts,
			'homeSize' => Image::getSize('home')));
		}
		else
		{
			$this->smarty->assign(array(
			'products' => array(),
			'search_products' => array(),
			'pages_nb' => 1,
			'nbProducts' => 0));
		}
		$this->smarty->assign('add_prod_display', Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'));
	}
	
	public function displayHeader()
	{
		if (!$this->instantSearch AND !$this->ajaxSearch)
			parent::displayHeader();
		else
			$this->smarty->assign('static_token', Tools::getToken(false));
	}
	
	public function displayContent()
	{
		parent::displayContent();
		$this->smarty->display(_PS_THEME_DIR_.'search.tpl');
	}
	
	public function displayFooter()
	{
		if (!$this->instantSearch AND !$this->ajaxSearch)
			parent::displayFooter();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		
		if (!$this->instantSearch AND !$this->ajaxSearch)
			$this->addCSS(_THEME_CSS_DIR_.'product_list.css');
	}
}

