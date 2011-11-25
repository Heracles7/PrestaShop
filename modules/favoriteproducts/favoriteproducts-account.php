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

/* SSL Management */
$useSSL = true;

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

require_once(dirname(__FILE__).'/FavoriteProduct.php');

if (!Context::getContext()->customer->isLogged())
	Tools::redirect('authentication.php?back=modules/favoriteproducts/favoriteproducts.php');

include(dirname(__FILE__).'/../../header.php');

if ((int)Context::getContext()->customer->id)
{
	$smarty->assign('favoriteProducts', FavoriteProduct::getFavoriteProducts((int)Context::getContext()->customer->id, (int)Context::getContext()->language->id));

	echo Module::displayTemplate(dirname(__FILE__).'/favoriteproducts.php', 'favoriteproducts-account.tpl');
}

include(dirname(__FILE__).'/../../footer.php');
