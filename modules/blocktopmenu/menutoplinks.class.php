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
*  @version  Release: $Revision: 7095 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MenuTopLinks
{
  public static function gets($id_lang, $id_linksmenutop = null, $id_shop)
  {
    return Db::getInstance()->executeS('
    SELECT l.id_linksmenutop, l.new_window, l.link, ll.label 
    FROM '._DB_PREFIX_.'linksmenutop l 
    LEFT JOIN '._DB_PREFIX_.'linksmenutop_lang ll ON (l.id_linksmenutop = ll.id_linksmenutop AND ll.id_lang = '.(int)$id_lang.' AND ll.id_shop='.(int)$id_shop.')
    WHERE 1
    '.((!is_null($id_linksmenutop)) ? ' AND l.id_linksmenutop = "'.(int)$id_linksmenutop.'"' : '').'
    AND l.id_shop IN (0, '.(int)$id_shop.')
    ');
  }

  public static function get($id_linksmenutop, $id_lang, $id_shop)
  {
    return self::gets($id_lang, $id_linksmenutop, $id_shop);
  }

  public static function add($link, $label, $newWindow = 0, $id_shop)
  {
    if(!is_array($label))
      return false;

    Db::getInstance()->insert(
      'linksmenutop',
      array(
        'new_window'=>(int)$newWindow,
        'link'=>pSQL($link),
        'id_shop' => (int)$id_shop
      )
    );
    $id_linksmenutop = Db::getInstance()->Insert_ID();
    foreach($label as $id_lang=>$label)
    {
      Db::getInstance()->insert(
        'linksmenutop_lang',
        array(
          'id_linksmenutop'=>(int)$id_linksmenutop,
          'id_lang'=>(int)$id_lang,
          'id_shop'=>(int)$id_shop,
          'label'=>pSQL($label)
        )
      );
    }
  }

  public static function remove($id_linksmenutop, $id_shop)
  {
    Db::getInstance()->delete(_DB_PREFIX_.'linksmenutop', 'id_linksmenutop = '.(int)$id_linksmenutop.' AND id_shop = '.(int)$id_shop);
    Db::getInstance()->delete(_DB_PREFIX_.'linksmenutop_lang', 'id_linksmenutop = '.(int)$id_linksmenutop);
  }
}
?>
