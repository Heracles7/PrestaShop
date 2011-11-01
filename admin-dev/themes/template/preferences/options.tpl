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
*  @version  Release: $Revision: 9540 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helper/options/options.tpl"} 
{block name="start_field_block"}
	{if $field['type'] == 'price'}
		<div class="margin-form">
			{$field['currency_left']}
			<input type="text"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else} 5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />
			{if isset($field['next'])} &nbsp; {$field['next']|strval}{/if}
			{$field['currency_right']}
	{elseif $field['type'] == 'disabled'}
		<div class="margin-form">
			{$field['disabled']}
	{elseif $field['type'] == 'maintenance_ip'}
		<div class="margin-form">
			{$field['script_ip']}
			<input type="text"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else} 5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />
			{$field['link_remove_ip']}
	{else}
		<div class="margin-form">
	{/if}
{/block}

{block name="end_field_block"}
	{if $field['type'] == 'checkbox_table'}
		<div class="clear"></div>
		<br />
	{/if}
{/block}