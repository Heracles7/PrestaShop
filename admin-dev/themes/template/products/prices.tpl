<script type="text/javascript">
var product_prices = new Array();
{foreach from=$combinations item='combination'}
	product_prices['{$combination.id_product_attribute}'] = '{$combination.price}';
{/foreach}
</script>
<a href="#" onclick="$('#add_specific_price').slideToggle();return false;"><img src="../img/admin/add.gif" alt="" />{l s='Add a new specific price'}</a>
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
		{foreach from=$currencies item=currency}
			<option value="{$currency['id_currency']}">{$currency['name']|htmlentitiesUTF8}</option>
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
			<select id="id_product_attribute" name="id_product_attribute">
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
					nextText: ''
				});
		});
	</script>

			<label>{l s='Product price'}
				{if $country_display_tax_label}
				 {l s='(tax excl.):'}
				 {/if}
				 </label>
{$content}
