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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*}

<!-- Block layered navigation module -->
<div id="layered_block_left" class="block">
	<h4>{l s='Catalog' mod='blocklayered'}</h4>
	<div class="block_content">
		<form action="#" id="layered_form">
			<div>
				{if isset($selected_filters) && $n_filters > 0}
				<div id="enabled_filters">
					<span class="layered_subtitle" style="float: none;">{l s='Enabled filters:' mod='blocklayered'}</span>
					<ul>
					{foreach from=$selected_filters key=filter_type item=filter_values}
					{foreach from=$filter_values item=filter_value}
					{foreach from=$filters item=filter}
					{if $filter.type == $filter_type && isset($filter.values)}
					{foreach from=$filter.values key=id_value item=value}
					{if $id_value == $filter_value}
						<li><a href="#" rel="layered_{$filter.type_lite}_{$id_value}" title="{l s='Cancel' mod='blocklayered'}">x</a> {$filter.name|escape:html:'UTF-8'}{l s=':'} {$value.name|escape:html:'UTF-8'}</li>
					{/if}
					{/foreach}
					{/if}
					{/foreach}
					{/foreach}
					{/foreach}
					</ul>
				</div>
				{/if}
				{foreach from=$filters item=filter}
					{if isset($filter.values)}
					<div>
						<span class="layered_subtitle">{$filter.name|escape:html:'UTF-8'}</span>
						<span class="layered_close"><a href="#" rel="layered_{$filter.type}_{$filter.id_key}">v</a></span>
						<div class="clear"></div>
						<ul id="layered_{$filter.type}_{$filter.id_key}">
						{foreach from=$filter.values key=id_value item=value}
							<li{if $layered_use_checkboxes} class="nomargin"{/if}>
							{if isset($filter.is_color_group) && $filter.is_color_group}
								<input type="button" name="layered_{$filter.type_lite}_{$id_value}" rel="{$id_value}_{$filter.id_key}" id="layered_attribute_{$id_value}" {if !$value.n} value="X" disabled="disabled"{/if} style="background: {if isset($value.color)}{$value.color}{else}#CCC{/if}; margin-left: 0; width: 16px; height: 16px; padding:0; border: 1px solid {if isset($value.checked) && $value.checked}red{else}#666{/if};" />
								{if isset($value.checked) && $value.checked}<input type="hidden" name="layered_{$filter.type_lite}_{$id_value}" value="{$id_value}" />{/if}
							{else}
								{if $layered_use_checkboxes}<input type="checkbox" class="checkbox" name="layered_{$filter.type_lite}_{$id_value}" id="layered_{$filter.type_lite}{if $id_value || $filter.type == 'quantity'}_{$id_value}{/if}" value="{$id_value}{if $filter.id_key}_{$filter.id_key}{/if}"{if isset($value.checked)} checked="checked"{/if}{if !$value.n} disabled="disabled"{/if} /> {/if}
							{/if}
							<label for="layered_{$filter.type_lite}_{$id_value}"{if !$value.n} class="disabled"{else}{if isset($filter.is_color_group) && $filter.is_color_group} name="layered_{$filter.type_lite}_{$id_value}" class="layered_color" rel="{$id_value}_{$filter.id_key}"{/if}{/if}>{$value.name|escape:html:'UTF-8'}<span> ({$value.n})</span></label>
							</li>
						{/foreach}
						</ul>
					</div>
					{/if}
				{/foreach}
			</div>
			<input type="hidden" name="id_category_layered" value="{$id_category_layered}" />
			{foreach from=$filters item=filter}
				{if $filter.type_lite == 'id_attribute_group' && isset($filter.is_color_group) && $filter.is_color_group}
					{foreach from=$filter.values key=id_value item=value}
						{if isset($value.checked)}
							<input type="hidden" name="layered_id_attribute_group_{$id_value}" value="{$id_value}_{$filter.id_key}" />
						{/if}
					{/foreach}
				{/if}
			{/foreach}
		</form>
	</div>
	<div id="layered_ajax_loader" style="display: none;">
		<p style="margin: 20px 0; text-align: center;"><img src="{$img_ps_dir}loader.gif" alt="" /><br />{l s='Loading...' mod='blocklayered'}</p>
	</div>
</div>
<!-- /Block layered navigation module -->