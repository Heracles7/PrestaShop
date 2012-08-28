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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6664 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture assign='page_title'}{l s='My addresses'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
	<a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="{$link->getPageLink('my-account', true)}">{l s='My account'}</a>
	<p>{l s='Please configure the desired billing and delivery addresses to be preselected when placing an order. You may also add additional addresses, useful for sending gifts or receiving your order at the office.'}</p>
	<div>
		{if isset($multipleAddresses) && $multipleAddresses}
		<h3>{l s='Your addresses are listed below.'}</h3>
		<p>{l s='Be sure to update them if they have changed.'}</p>
		{assign var="adrs_style" value=$addresses_style}
		<form action="opc.html" method="post">
			<ul data-role="listview" data-theme="g">
				{foreach from=$multipleAddresses item=address name=myLoop}
				<li>
					<a href="{$link->getPageLink('address', true, null, "id_address={$address.object.id|intval}")}" title="{l s='Update'}">
						<h4>{$address.object.alias}</h4>
						{foreach from=$address.ordered name=adr_loop item=pattern}
							{assign var=addressKey value=" "|explode:$pattern}
							{foreach from=$addressKey item=key name="word_loop"}
								{$address.formated[$key|replace:',':'']|escape:'htmlall':'UTF-8'}
							{/foreach}
							<br />
						{/foreach}
					</a>
				</li>
				{/foreach}
			</ul>
		</form>
		{else}
		<p class="warning">{l s='No addresses available.'}</p>
	{/if}
		<a href="{$link->getPageLink('address', true)}" data-role="button" data-theme="a">{l s='Add new address'}</a>
	</div>
	
	{include file='./sitemap.tpl'}
</div><!-- /content -->