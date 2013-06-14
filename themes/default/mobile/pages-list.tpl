{*
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

		<hr/>
		<h2 class="site_map">{l s='Sitemap'}</h2>
		<ul data-role="listview" data-inset="true" id="category">
			{if $controller_name != 'index'}<li><a href="{$link->getPageLink('index', true)|escape:'html'}" data-ajax="false">Accueil</a></li>{/if}
			
			{* need to set a Hook : hookMobilePagesList *}
			{* ===================================== *}
			<li><a href="{$link->getCategoryLink(3, false)|escape:'html'}" data-ajax="false">IPod</a></li>
			<li><a href="{$link->getCategoryLink(4, false)|escape:'html'}" data-ajax="false">Accessoires</a></li>
			{* ===================================== *}
			
			{if $controller_name != 'my-account'}<li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" data-ajax="false">{l s='My account'}</a></li>{/if}
			{if $controller_name != 'contact'}<li><a href="{$link->getPageLink('contact', true)|escape:'html'}" data-ajax="false">{l s='Contact'}</a></li>{/if}
		</ul>
