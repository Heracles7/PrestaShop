{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.name == 'vat_number'}
		{if $vat == 'is_applicable'}
			<div id="vat_area" style="display: visible">
		{else if $vat == 'management'}
			<div id="vat_area" style="display: hidden">
		{else}
			<div style="display: none;">
		{/if}
	{/if}

	{if $input.type == 'text_customer' && !isset($customer)}
		<label>{l s='Customer email'}</label>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
	{if $input.type == 'text_customer'}
		{if isset($customer)}
			<div class="margin-form"><a style="display: block; padding-top: 4px;" href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={$tokenCustomer}">{$customer->lastname} {$customer->firstname} ({$customer->email})</a></div>
			<input type="hidden" name="id_customer" value="{$customer->id}" />
			<input type="hidden" name="email" value="{$customer->email}" />
		{else}
			<script type="text/javascript">
			$('input[name=email]').live('blur', function(e)
			{
				var email = $(this).val();
				if (email.length > 5)
				{
					var data = {};
					data.email = email;
					data.token = "{$token}";
					data.ajax = 1;
					data.controller = "AdminAddresses";
					data.action = "loadNames";
					$.ajax({
						type: "POST",
						url: "ajax-tab.php",
						data: data,
						dataType: 'json',
						async : true,
						success: function(msg)
						{
							if (msg)
							{
								var infos = msg.infos.split('_');
								$('input[name=firstname]').val(infos[0]);
								$('input[name=lastname]').val(infos[1]);
								$('input[name=company]').val(infos[2]);
							}
						},
						error: function(msg)
						{
						}
					});
				}
			});
			</script>
			<div class="margin-form">
				<input type="text" size="33" name="email" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" style="text-transform: lowercase;" /> <sup>*</sup>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
	{if $input.name == 'vat_number'}
		</div>
	{/if}
{/block}
