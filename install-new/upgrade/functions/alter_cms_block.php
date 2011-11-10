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

function alter_cms_block()
{
	// No one will know if the table does not exist :] Thanks Damien for your solution ;)
	DB::getInstance()->Execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_lang` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->Execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->Execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_page` CHANGE  `id_block_cms`  `id_cms_block` INT( 10 ) UNSIGNED NOT NULL');
	
	DB::getInstance()->Execute('ALTER TABLE  `'._DB_PREFIX_.'cms_block_page` CHANGE  `id_block_cms_page`  `id_cms_block_page` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT');
		
}

