{*
* 2007-2012 PrestaShop
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
*  @version  Release: $Revision: 9596 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$(function() {
	$('body').highlight('{$query}');
});
</script>

{if $show_toolbar}
	<div class="toolbar-placeholder">
		<div class="toolbarBox {if $toolbar_fix}toolbarHead{/if}">
				{include file="toolbar.tpl" toolbar_btn=$toolbar_btn}
				<div class="pageTitle">
				<h3>
					{block name=pageTitle}
						<span id="current_obj" style="font-weight: normal;">{$title|default:'&nbsp;'}</span>
					{/block}
				</h3>
				</div>
		</div>
	</div>
{/if}

{if isset($features)}
	{if !$features}
		<h3>{l s='No features matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Features matching your query'} : {$query}</h3>
		<table class="table" cellpadding="0" cellspacing="0">
			{foreach $features key=key item=feature }
				{foreach $feature key=k item=val name=feature_list}
					<tr>
						<th>{if $smarty.foreach.feature_list.first}{$key}{/if}</th>
						<td>
							<a href="{$val.link}">{$val.value}</a>
						</td>
					</tr>
				{/foreach}
			{/foreach}
		</table>
		<div class="clear">&nbsp;</div>
	{/if}
{/if}
{if isset($categories)}
	{if !$categories}
		<h3>{l s='No categories matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Categories matching your query'} : {$query}</h3>
		<table cellspacing="0" cellpadding="0" class="table">
			{foreach $categories key=key item=category }
				<tr class="alt_row">
					<td>{$category}</td>
				</tr>
			{/foreach}
		</table>
		<div class="clear">&nbsp;</div>
	{/if}
{/if}
{if isset($products)}
	{if !$products}
		<h3>{l s='No products matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Products matching your query'} : {$query}</h3>
		{$products}
	{/if}
{/if}
{if isset($customers)}
	{if !$customers}
		<h3>{l s='No customer matching your query'} : {$query}</h3>
	{else}
		<h3>{l s='Customer matching your query'} : {$query}</h3>
		{$customers}
	{/if}
{/if}