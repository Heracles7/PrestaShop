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
*  @version  Release: $Revision: 11069 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($product->id)}

	<h4>{l s='Suppliers of the current product'}</h4>
	<div class="separation"></div>
	<div class="hint" style="display:block; position:'auto';">
		<p>{l s='This interface allows you to specify the suppliers of the current product and eventually its combinations.'}</p>
		<p>{l s='It is also possible to specify for each product/product combinations the supplier reference according to previously associated suppliers.'}</p>
	</div>
	<p>{l s='Please choose the suppliers associated to this product, and the default one.'}</p>
	{assign var=confirm value="Are you sure you want to delete entered product information?"}
	<a class="button bt-icon" href="{$link->getAdminLink('AdminSuppliers')}&addsupplier" onclick="return confirm(' {$confirm} ')">
		<img src="../img/admin/add.gif" alt="{l s='Create new supplier'}" title="{l s='Create new supplier'}" /><span>{l s='Create new supplier'}</span>
	</a>
	<table cellpadding="5" style="width:100%">
		<tbody>
			<tr>
				<td valign="top" style="text-align:left;vertical-align:top;">
					<table class="table" cellpadding="0" cellspacing="0" style="width:50%;">
						<thead>
							<tr>
								<th>{l s='Selected'}</th>
								<th>{l s='Supplier Name'}</th>
								<th>{l s='Default'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$suppliers item=supplier}
							<tr>
								<td><input type="checkbox" class="supplierCheckBox" name="check_supplier_{$supplier['id_supplier']}" {if $supplier['is_selected'] == true}checked="checked"{/if} value="{$supplier['id_supplier']}" /></td>
								<td>{$supplier['name']}</td>
								<td><input type="radio" id="default_supplier_{$supplier['id_supplier']}" name="default_supplier" value="{$supplier['id_supplier']}" {if $supplier['is_selected'] == false}disabled="disabled"{/if} {if $supplier['is_default'] == true}checked="checked"{/if} /></td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<p>&nbsp;</p>
					<h4>{l s='Product reference(s)'}</h4>
	<div class="separation"></div>
	<p>{l s='You can specify product reference(s) for each supplier associated.'}</p>

	<div id="suppliers_accordion" style="margin-top:10px; display:block;">
		{foreach from=$associated_suppliers item=supplier}
		    <h3 style="margin-bottom:0;"><a href="#">{$supplier->name}</a></h3>
		    <div style="display:block;">

				<table cellpadding="10" cellspacing="0" class="table">

					<thead>
						<tr>
							<th>{l s='Product name'}</th>
							<th width="150">{l s='Supplier reference'}</th>
							<th width="150">{l s='Unit price tax excluded'}</th>
							<th width="150">{l s='Unit price currency'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach $attributes AS $index => $attribute}
						{assign var=reference value=''}
						{assign var=price_te value=''}
						{assign var=id_currency value=''}
						{foreach from=$associated_suppliers_collection item=asc}
							{if $asc->id_product == $attribute['id_product'] && $asc->id_product_attribute == $attribute['id_product_attribute'] && $asc->id_supplier == $supplier->id_supplier}
								{assign var=reference value=$asc->product_supplier_reference}
								{assign var=price_te value=Tools::ps_round($asc->product_supplier_price_te, 2)}
								{assign var=id_currency value=$asc->id_currency}
							{/if}
						{/foreach}
						<tr {if $index is odd}class="alt_row"{/if}>
							<td>{$product_designation[$attribute['id_product_attribute']]}</td>
							<td>
								<input type="text" size="10" value="{$reference}" name="supplier_reference_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
							</td>
							<td>
								<input type="text" size="10" value="{$price_te}" name="product_price_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}" />
							</td>
							<td>
								<select name="product_price_currency_{$attribute['id_product']}_{$attribute['id_product_attribute']}_{$supplier->id_supplier}">
									{foreach $currencies AS $currency}
										<option value="{$currency['id_currency']}"
											{if $currency['id_currency'] == $id_currency}selected="selected"{/if}
										>{$currency['name']}</option>
									{/foreach}
								</select>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		{/foreach}
	</div>

	<script type="text/javascript">
		$(function() {
			var default_is_ok = false;

			var manageDefaultSupplier = function() {

				var availables_radio_buttons = [];
				var radio_buttons = $('input[name="default_supplier"]');

				for (i=0; i<radio_buttons.length; i++)
				{
					var item = $(radio_buttons[i]);

					if (item.is(':disabled'))
					{
						if (item.is(':checked'))
						{
							item.attr("checked", "");
							default_is_ok = false;
						}
					}
					else
					{
						availables_radio_buttons.push(item);
					}
				}

				if (default_is_ok == false)
				{
					for (i=0; i<availables_radio_buttons.length; i++)
					{
						var item = $(availables_radio_buttons[i]);

						if (item.is(':disabled') == false)
						{
							item.attr("checked", "checked");
							default_is_ok = true;
						}

						break;
					}
				}
			};

			$('.supplierCheckBox').click(function() {

				var check = $(this);

				var checkbox = $('#default_supplier_'+check.val());

				if (this.checked)
				{
					//enable default radio button associated
					checkbox.attr("disabled","");
				}
				else
				{
					//enable default radio button associated
					checkbox.attr("disabled","disabled");
				}

				//manage default supplier check
				manageDefaultSupplier();

			});

			setTimeout(function() {
				$('#suppliers_accordion').accordion();
			}, 500);
		});
	</script>

{/if}