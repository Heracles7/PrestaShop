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

<script type="text/javascript">
	i18n_tax_exc = '{l s='Tax Excluded'} ';
	i18n_tax_inc = '{l s='Tax Included'} ';

	var product_tax = '{$tax_rates}';
	function calcPrice(element, element_has_tax)
	{
			var element_price = element.val().replace(/,/g, '.');
			var other_element_price = 0;

			if (!isNaN(element_price) && element_price > 0)
			{
				if (element_has_tax)
					other_element_price = parseFloat(element_price / ((product_tax / 100) + 1)).toFixed(6);
				else
					other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2).toFixed(2);
			}

			$('#related_to_'+element.attr('name')).val(other_element_price);
	}

	$(document).ready(function() { $('.price_impact').each(function() { calcPrice($(this), false); }); });
</script>

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>

{if $generate}<div class="module_confirmation conf confirm">{l s='%d product(s) successfully created.' sprintf=$combinations_size}</div>{/if}
<script type="text/javascript" src="../js/attributesBack.js"></script>
<form enctype="multipart/form-data" method="post" id="generator" action="{$url_generator}">
	<fieldset>
		<h3><i class="icon-asterisk"></i> {l s='Attributes generator'}</h3>
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<select multiple name="attributes[]" id="attribute_group" style="height: 400px">
						{foreach $attribute_groups as $k => $attribute_group}
							{if isset($attribute_js[$attribute_group['id_attribute_group']])}
								<optgroup name="{$attribute_group['id_attribute_group']}" id="{$attribute_group['id_attribute_group']}" label="{$attribute_group['name']|escape:'htmlall':'UTF-8'}">
									{foreach $attribute_js[$attribute_group['id_attribute_group']] as $k => $v}
										<option name="{$k}" id="attr_{$k}" value="{$v|escape:'htmlall':'UTF-8'}" title="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</optgroup>
							{/if}
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-default" onclick="del_attr_multiple();"><i class="icon-minus-sign"></i> {l s='Delete'}</button>
					<button type="button" class="btn btn-default pull-right" onclick="add_attr_multiple();"><i class="icon-plus-sign"></i> {l s='Add'}</button>
				</div>
			</div>
			<div class="col-lg-9">
				<div class="alert alert-info">{l s='The Combinations Generator is a tool that allows you to easily create a series of combinations by selecting the related attributes. For example, if you\'re selling t-shirts in three different sizes and two different colors, the generator will create six combinations for you.'}</div>

				<p>{l s='You\'re currently generating combinations for the following product:'} <b>{$product_name|escape:'htmlall':'UTF-8'}</b></p>

				<h4>{l s='Step 1: On the left side, select the attributes you want to use (Hold down the "Ctrl" key on your keyboard and validate by clicking on "Add")'}</h4>

				<div>
				{foreach $attribute_groups as $k => $attribute_group}
					{if isset($attribute_js[$attribute_group['id_attribute_group']])}
						<table class="table" style="display: none;">
							<thead>
								<tr>
									<th id="tab_h1">{$attribute_group['name']|escape:'htmlall':'UTF-8'}</th>
									<th id="tab_h2" colspan="2">{l s='Impact on the product price'} ({$currency_sign})</th>
									<th>{l s='Impact on the product weight'} ({$weight_unit})</th>
								</tr>
							</thead>
							<tbody id="table_{$attribute_group['id_attribute_group']}" name="result_table">
							</tbody>
						</table>
						{if isset($attributes[$attribute_group['id_attribute_group']])}
							{foreach $attributes[$attribute_group['id_attribute_group']] AS $k => $attribute}
								<script type="text/javascript">
									$('#table_{$attribute_group['id_attribute_group']}').append(create_attribute_row({$k}, {$attribute_group['id_attribute_group']}, '{$attribute['attribute_name']|addslashes}', {$attribute['price']}, {$attribute['weight']}));
									toggle(getE('table_' + {$attribute_group['id_attribute_group']}).parentNode, true);
								</script>
							{/foreach}
						{/if}
					{/if}
				{/foreach}
	            </div>
				<h4>{l s='Select a default quantity, and reference, for each combination the generator will create for this product.'}</h4>
				<table class="table">
					<tbody>
						<tr>
							<td>{l s='Default Quantity:'}</td>
							<td><input type="text" name="quantity" value="0" /></td>
						</tr>
						<tr>
							<td>{l s='Default Reference:'}</td>
							<td><input type="text" name="reference" value="{$product_reference|escape:'htmlall':'UTF-8'}" /></td>
						</tr>
					</tbody>
				</table>
				<h4>{l s='Please click on "Generate these Combinations"'}</h4>
				<p><input type="submit" class="btn btn-default" name="generate" value="{l s='Generate these Combinations'}" /></p>
			</div>
		</div>
	</fieldset>
</form>