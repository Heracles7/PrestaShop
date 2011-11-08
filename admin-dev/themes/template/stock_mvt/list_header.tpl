{extends file="helper/list/list_header.tpl"}
{block name=leadin}
	{if count($list_warehouses) > 1}
		<form type="get" onchange="submit();">
			<label for="warehouse">{l s="Select a warehouse:"}</label>
			<input type="hidden" name="controller" value="AdminStockMvt" />
			<input type="hidden" name="token" value="{$token}" />
			<select name="warehouse">
				{foreach $list_warehouses as $warehouse}
					<option {if $warehouse.id_warehouse == $current_warehouse}selected="selected"{/if} value="{$warehouse.id_warehouse}">{$warehouse.name}</option>
				{/foreach}
			</select>
		</form>
	{/if}
{/block}
