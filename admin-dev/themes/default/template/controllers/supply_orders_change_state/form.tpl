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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helper/form/form.tpl"}

{block name="other_fieldsets"}

<br />

{if isset($supply_order_state) && $supply_order_state->editable == false && isset($supply_order)}
<fieldset>
<legend>
<img src="../img/admin/pdf.gif" alt="{l s='Supply Order State'}">
{l s='Print the supply order form'}
</legend>
<a href="pdf.php?id_supply_order={$supply_order->id}" target="_blank" title="Export as PDF">{l s='Click here to download the supply order form'}.</a>
</fieldset>
{/if}

{/block}
