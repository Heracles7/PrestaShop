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
*  @version  Release: $Revision: 7227 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

ob_start();
$timerStart = microtime(true);

//	$_GET['tab'] = $_GET['controller'];
//	$_POST['tab'] = $_POST['controller'];
//	$_REQUEST['tab'] = $_REQUEST['controller'];

$context = Context::getContext();
if (isset($_GET['logout']))
	$context->employee->logout();

if (!isset($context->employee) || !$context->employee->isLoggedBack())
	Tools::redirectAdmin('login.php?redirect='.$_SERVER['REQUEST_URI']);


// Set current index
//$currentIndex = $_SERVER['SCRIPT_NAME'].(($tab = Tools::getValue('tab')) ? '?tab='.$tab : '');
//if (empty($tab))
//{
	$currentIndex = $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller: '');
	$tab = $controller;
//}
if ($back = Tools::getValue('back'))
	$currentIndex .= '&back='.urlencode($back);
AdminTab::$currentIndex = $currentIndex;

$iso = $context->language->iso_code;
include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');

/* Server Params */
$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
$protocol_content = (isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
$link = new Link($protocol_link, $protocol_content);
$context->link = $link;
define('_PS_BASE_URL_', Tools::getShopDomain(true));
define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

$path = dirname(__FILE__).'/themes/';
if (empty($context->employee->bo_theme) OR !file_exists($path.$context->employee->bo_theme.'/admin.css'))
{
	if (file_exists($path.'oldschool/admin.css'))
		$context->employee->bo_theme = 'oldschool';
	elseif (file_exists($path.'origins/admin.css'))
		$context->employee->bo_theme = 'origins';
	else
		foreach (scandir($path) as $theme)
			if ($theme[0] != '.' AND file_exists($path.$theme.'/admin.css'))
			{
				$employee->bo_theme = $theme;
				break;
			}
	$context->employee->update();
}

// Change shop context ?
if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false)
{
	$context->cookie->shopContext = Tools::getValue('setShopContext');
	$url = parse_url($_SERVER['REQUEST_URI']);
	$query = (isset($url['query'])) ? $url['query'] : '';
	parse_str($query, $parseQuery);
	unset($parseQuery['setShopContext']);
	Tools::redirectAdmin($url['path'] . '?' . http_build_query($parseQuery));
}

$context->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

$shopID = '';
if ($context->cookie->shopContext)
{
	$split = explode('-', $context->cookie->shopContext);
	if (count($split) == 2 && $split[0] == 's')
		$shopID = (int)$split[1];
}
$context->shop = new Shop($shopID);
