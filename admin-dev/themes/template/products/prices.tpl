<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}price.js"></script>
<div class="block_specific_prices_modifications">
			<h4>{l s='Product price'}</h4>
			<div class="separation"></div>
			<script type="text/javascript">
var product_prices = new Array();
{foreach from=$combinations item='combination'}
	product_prices['{$combination.id_product_attribute}'] = '{$combination.price}';
{/foreach}
</script>
<table>
	<tr>
		<td class="col-left"><label>{l s='Pre-tax wholesale price:'}</label></td>
		<td style="padding-bottom:5px;">
			{$currency->prefix}<input size="11" maxlength="14" name="wholesale_price" type="text" value="{$product->wholesale_price}" onchange="this.value = this.value.replace(/,/g, '.');" />{$currency->suffix}
			<p class="preference_description">{l s='The wholesale price at which you bought this product'}</p>
		</td>
	</tr>

	<tr>
		<td class="col-left"><label>{l s='Pre-tax retail price:'}</label></td>
		<td style="padding-bottom:5px;">
			{$currency->prefix}<input size="11" maxlength="14" id="priceTE" name="price" type="text" value="{$product->price}" onchange="this.value = this.value.replace(/,/g, '.');" onkeyup="if (isArrowKey(event)) return; calcPriceTI();" />{$currency->suffix}<sup> *</sup>
			<p class="preference_description">{l s='The pre-tax retail price to sell this product'}</p>
		</td>
	</tr>
	<tr>
		<td class="col-left"><label>{l s='Tax rule:' }</label></td>
		<td style="padding-bottom:5px;">
			<script type="text/javascript">
			noTax = {if $tax_exclude_taxe_option}true{else}false{/if};
			taxesArray = new Array ();
			taxesArray[0] = 0;
			{foreach $tax_rules_groups as $tax_rules_group}
				{if isset($taxesRatesByGroup[$tax_rules_group['id_tax_rules_group']])}
				taxesArray[{$tax_rules_group.id_tax_rules_group}] = {$taxesRatesByGroup[$tax_rules_group['id_tax_rules_group']]};
					{else}
				taxesArray[{$tax_rules_group.id_tax_rules_group}] = 0;
				{/if}
			{/foreach}
			ecotaxTaxRate = {$ecotaxTaxRate / 100};
			</script>

					<span {if $tax_exclude_taxe_option}style="display:none;"{/if} >
					 <select onChange="javascript:calcPriceTI(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
					     <option value="0">{l s='No Tax'}</option>
						{foreach from=$tax_rules_groups item=tax_rules_group}
							<option value="{$tax_rules_group.id_tax_rules_group}" {if $product->id_tax_rules_group == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
								{$tax_rules_group['name']|htmlentitiesUTF8}
							</option>
						{/foreach}
						</select>
						<a href="{$link->getAdminLink('AdminTaxRulesGroup')}&addtax_rules_group&id_product={$product->id}" onclick="return confirm('{l s='Are you sure you want to delete entered product information?'}'" >
						<img src="../img/admin/add.gif" alt="{l s='Create'}" title="{l s='Create'}" /> <b>{l s='Create'}</b>
						</a>
					</span>
					{if $tax_exclude_taxe_option}
						<span style="margin-left:10px; color:red;">{l s='Taxes are currently disabled'}</span> (<b><a href="{$link->getAdminLink('AdminTaxes')}">{l s='Tax options'}</a></b>)
						<input type="hidden" value="{$product->id_tax_rules_group}" name="id_tax_rules_group" />
					{/if}
				</td>
			</tr>
			{if $ps_use_ecotax}
			<tr>
				<td class="col-left"><label>{l s='Eco-tax (tax incl.):' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix}<input size="11" maxlength="14" id="ecotax" name="ecotax" type="text" value="{$product->ecotax}" onkeyup="if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" />{$currency->suffix}
					<span style="margin-left:10px">({l s='already included in price'})</span>
				</td>
			</tr>
			{/if}
			<tr {if !$country_display_tax_label || $tax_exclude_taxe_option}style="display:none"{/if} >
				<td class="col-left"><label>{l s='Retail price with tax:' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix}<input size="11" maxlength="14" id="priceTI" type="text" value="" onchange="noComma('priceTI');" onkeyup="if (isArrowKey(event)) return;  calcPriceTE();" />{$currency->suffix}
				</td>
			</tr>
			<tr id="tr_unit_price">
				<td class="col-left"><label>{l s='Unit price without tax:' }</label></td>
				<td style="padding-bottom:5px;">
					{$currency->prefix} <input size="11" maxlength="14" id="unit_price" name="unit_price" type="text" value="{$product->unit_price}"
						onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/>{$currency->suffix}
						{l s='per'} <input size="6" maxlength="10" id="unity" name="unity" type="text" value="{$product->unity|htmlentitiesUTF8}" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
							{if $ps_tax && $country_display_tax_label}
								<span style="margin-left:15px">{l s='or'}
									{$currency->prefix}<span id="unit_price_with_tax">0.00</span>{$currency->suffix}
									{l s='per'} <span id="unity_second">{$product->unity}</span> {l s='with tax'}
								</span>
							{/if}
							<p>{l s='Eg. $15 per Lb'}</p>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<input type="checkbox" name="on_sale" id="on_sale" style="padding-top: 5px;" {if $product->on_sale}checked="checked"{/if} value="1" />&nbsp;<label for="on_sale" class="t">{l s='Display "on sale" icon on product page and text on product listing'}</label>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label><b>{l s='Final retail price:'}</b></label></td>
						<td style="padding-bottom:5px;">
							<span {if !$country_display_tax_label}style="display:none"{/if} >
							{$currency->prefix}<span id="finalPrice" style="font-weight: bold;"></span>{$currency->suffix}<span {if $ps_tax}style="display:none;"{/if}> ({l s='tax incl.'})</span>
							</span>
							<span {if $ps_tax}style="display:none;"{/if} >

							{if $country_display_tax_label}
								 /
							{/if}
							{$currency->prefix}<span id="finalPriceWithoutTax" style="font-weight: bold;"></span>{$currency->suffix} {if $country_display_tax_label}({l s='tax excl.'}){/if}</span>
						</td>
					</tr>
					<tr>
						<td class="col-left"><label>&nbsp;</label></td>
						<td>
							<div class="hint clear" style="display: block;width: 70%;">{l s='You can define many discounts and specific price rules in the Prices tab'}</div>
						</td>
					</tr>
					{* [end] prices *}
</table>
<div class="separation"></div>
<h4>{l s='Current specific prices'}</h4>
<a class="button bt-icon" href="#" onclick="$('#add_specific_price').slideToggle();return false;"><img src="../img/admin/add.gif" alt="" /><span>{l s='Add a new specific price'}</span></a>
<br/>
<div id="add_specific_price" style="display: none;">
	<input type="hidden" name="sp_id_shop" value="0" />
	<label>{l s='For:'}</label>
	<div class="margin-form">
		<select name="sp_id_shop">
			<option value="0">{l s='All shops'}</option>
		{foreach from=$shops item=shop}
			<option value="{$shop['id_shop']}">{$shop['name']|htmlentitiesUTF8}</option>
		{/foreach}
		</select>
					&gt;
		<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);">
			<option value="0">{l s='All currencies'}</option>
		{foreach from=$currencies item=curr}
			<option value="{$curr['id_currency']}">{$curr['name']|htmlentitiesUTF8}</option>
		{/foreach}
		</select>
					&gt;
		<select name="sp_id_country">
			<option value="0">{l s='All countries'}</option>
		{foreach from=$countries item=country}
			<option value="{$country['id_country']}">{$country['name']|htmlentitiesUTF8}</option>
		{/foreach}
		</select>
					&gt;
		<select name="sp_id_group">
			<option value="0">{l s='All groups'}</option>
		{foreach from=$groups item=group}
			<option value="'.(int)($group['id_group']).'">'.{$group['name']}</option>
		{/foreach}
		</select>
	</div>
	{if $combinations|@count != 0}
		<label>{l s='Combination:'}</label>
		<div class="margin-form">
			<select id="sp_id_product_attribute" name="sp_id_product_attribute">
				<option value="0">{l s='Apply to all combinations'}</option>
				{foreach from=$combinations item='combination'}
					<option value="{$combination.id_product_attribute}">{$combination.attributes}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	<label>{l s='Available from:'}</label>
	<div class="margin-form">
		<input class="datepicker" type="text" name="sp_from" value="" style="text-align: center" id="sp_from" /><span style="font-weight:bold; color:#000000; font-size:12px"> {l s='to'}</span>
		<input class="datepicker" type="text" name="sp_to" value="" style="text-align: center" id="sp_to" />
	</div>

	<label>{l s='Starting at'}</label>
	<div class="margin-form">
		<input type="text" name="sp_from_quantity" value="1" size="3" /> <span style="font-weight:bold; color:#000000; font-size:12px">{l s='unit'}</span>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
				product_prices['0'] = $('#sp_current_ht_price').html();
				$('#id_product_attribute').change(function() {
					$('#sp_current_ht_price').html(product_prices[$('#id_product_attribute option:selected').val()]);
				});
				$('.datepicker').datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});
		});
	</script>

			<label>{l s='Product price'}
				{if $country_display_tax_label}
				 {l s='(tax excl.):'}
				 {/if}
				 </label>
					<div class="margin-form">
						<span id="spm_currency_sign_pre_0" style="font-weight:bold; color:#000000; font-size:12px">{$currency->prefix}</span>
						<input type="text" name="sp_price" value="0" size="11" />
						<span id="spm_currency_sign_post_0" style="font-weight:bold; color:#000000; font-size:12px">{$currency->suffix}</span>
						<span>({l s='Current:'} </span><span id="sp_current_ht_price">{displayWtPrice p=$product->price}</span> )</span>
						<div class="hint" style="display:block;">
							{l s='You can set this value at 0 in order to apply the default price'}
						</div>
					</div>

					<label>{l s='Apply a discount of:'}</label>
					<div class="margin-form">
							<input type="text" name="sp_reduction" value="0.00" size="11" />
						<select name="sp_reduction_type">
							<option selected="selected">---</option>
							<option value="amount">{l s='Amount'}</option>
							<option value="percentage">{l s='Percentage'}</option>
						</select>
						{l s='(if set to "amount", the tax is included)'}
					</div>

					<div class="margin-form">
						<input type="submit" name="submitPriceAddition" value="{l s='Add'}" class="button" />
					</div>
				</div>
		<table style="text-align: center;width:100%" class="table" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th class="cell border" style="width: 12%;">{l s='Rule'}</th>
					<th class="cell border" style="width: 12%;">{l s='Combination'}</th>
					<th class="cell border" style="width: 12%;">{l s='Shop'}</th>
					<th class="cell border" style="width: 12%;">{l s='Currency'}</th>
					<th class="cell border" style="width: 11%;">{l s='Country'}</th>
					<th class="cell border" style="width: 13%;">{l s='Group'}</th>
					<th class="cell border" style="width: 12%;">{l s='Price'} {if $country_display_tax_label}{l s='(tax excl)'}{/if}</th>
					<th class="cell border" style="width: 10%;">{l s='Reduction'}</th>
					<th class="cell border" style="width: 15%;">{l s='Period'}</th>
					<th class="cell border" style="width: 10%;">{l s='From (quantity)'}</th>
					<th class="cell border" style="width: 15%;">{l s='Final price'} {if $country_display_tax_label}{l s='(tax excl.)'}{/if}</th>
					<th class="cell border" style="width: 2%;">{l s='Action'}</th>
				</tr>
			</thead>
			<tbody>
				{$specificPriceModificationForm}
					<script type="text/javascript">
						calcPriceTI();
						unitPriceWithTax('unit');
					</script>