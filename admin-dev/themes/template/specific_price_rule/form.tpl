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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helper/form/form.tpl"}

{block name="other_fieldsets"}

<div class="clear">&nbsp;</div>
<fieldset id="conditions">
	<div id="condition_group_list">
	</div>
	<a class="button bt-icon" href="#" id="add_condition_group">
		<img src="../img/admin/add.gif" />
		{l s='Add a new condition group'}
	</a>
	<div id="condition_list" class="clear">
		<legend>{l s='Conditions'}</legend>
		<label for="id_category">{l s='Category:'}</label>
		<div class="margin-form">
			<select id="id_category" name="id_category">
				{foreach from=$categories item='category'}
					<option value="{$category.id_category}">{$category.name}</option>
				{/foreach}
			</select>
			<a class="button bt-icon" href="#" id="add_condition_category">
				<img src="../img/admin/add.gif" />
				{l s='Add condition'}
			</a>
		</div>
		<label for="id_manufacturer">{l s='Manufacturer:'}</label>
		<div class="margin-form">
			<select id="id_manufacturer" name="id_manufacturer">
				{foreach from=$manufacturers item='manufacturer'}
					<option value="{$manufacturer.id_manufacturer}">{$manufacturer.name}</option>
				{/foreach}
			</select>
			<a class="button bt-icon" href="#" id="add_condition_manufacturer">
				<img src="../img/admin/add.gif" />
				{l s='Add condition'}
			</a>
		</div>
		<label for="id_supplier">{l s='Supplier:'}
		<div class="margin-form">
			<select id="id_supplier" name="id_supplier">
				{foreach from=$suppliers item='supplier'}
					<option value="{$supplier.id_supplier}">{$supplier.name}</option>
				{/foreach}
			</select>
			<a class="button bt-icon" href="#" id="add_condition_supplier">
				<img src="../img/admin/add.gif" />
				{l s='Add condition'}
			</a>
		</div>
		<label for="id_attribute">{l s='Attributes:'}
		<div class="margin-form">
			<select id="id_attribute_group">
				{foreach from=$attributes_group item='attribute_group'}
					<option value="{$attribute_group.id_attribute_group}">{$attribute_group.name}</option>
				{/foreach}
			</select>
			{foreach from=$attributes_group item='attribute_group'}
				<select class="id_attribute" style="display:none;" id="id_attribute_{$attribute_group.id_attribute_group}">
					{foreach from=$attribute_group.attributes item='attribute'}
						<option value="{$attribute.id_attribute}">{$attribute.name}</option>
					{/foreach}
				</select>
			{/foreach}
			</select>
			<a rel="" class="button bt-icon" href="#" id="add_condition_attribute">
				<img src="../img/admin/add.gif" />
				{l s='Add condition'}
			</a>
		</div>
		<label for="id_attribute">{l s='Features:'}
		<div class="margin-form">
			<select id="id_feature">
				{foreach from=$features item='feature'}
					<option value="{$feature.id_feature}">{$feature.name}</option>
				{/foreach}
			</select>
			{foreach from=$features item='feature'}
				<select class="id_feature_value" style="display:none;" id="id_feature_{$feature.id_feature}">
					{foreach from=$feature.values item='value'}
						<option value="{$value.id_feature_value}">{$value.value}</option>
					{/foreach}
				</select>
			{/foreach}
			</select>
			<a class="button bt-icon" href="#" id="add_condition_feature">
				<img src="../img/admin/add.gif" />
				{l s='Add condition'}
			</a>
		</div>
	</div>
</fieldset>
{/block}

{block name="script"}
var current_id_condition_group = 0;
var last_condition_group = 0;
var conditions = new Array();
function toggle_condition_group(id_condition_group)
{
	$('.condition_group > table').fadeOut();
	$('#condition_group_'+id_condition_group+' > table').fadeIn();
	current_id_condition_group = id_condition_group;
}
function add_condition(id_condition_group, type, value)
{
	var condition = new Array();
	condition.type = type;
	condition.value = value;
	condition.id_condition_group = id_condition_group;
	conditions.push(condition);
}
function new_condition_group()
{
	last_condition_group++;
	var html = '<fieldset class="condition_group" id="condition_group_'+last_condition_group+'"><legend>{l s='Condition group'} '+last_condition_group+'</legend>';
		html += '<table class="table"><thead><tr><th>{l s='Type'}</th><th>{l s='Value'}</th></tr></thead><tbody></tbody></table>';
		html += '</fieldset><div style="font-weight:bold;">{l s='OR'}</div>';
	toggle_condition_group(last_condition_group);
	$('#condition_group_list').append(html);
}
$(document).ready(function() {
	$('#specific_price_rule_form').live('submit', function(e) {
		var html = '';
		$.each(conditions, function() {
			html += '<input type="hidden" name="condition_group_'+this.id_condition_group+'[]" value="'+this.type+'_'+this.value+'" />';
		});
		$('#conditions').append(html);
	});
	$('#id_feature').change(function() {
		$('.id_feature_value').hide();
		$('#id_feature_'+$(this).val()).show();
	});
	$('#id_attribute_group').change(function() {
		$('.id_attribute').hide();
		$('#id_attribute_'+$(this).val()).show();
	});
	$('#add_condition_category').click(function() {
		add_condition(current_id_condition_group, 'category', $('#id_category option:selected').val());
		var html = '<tr><td>{l s='Category'}</td><td>'+$('#id_category option:selected').html()+'</td></tr>';
		$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
	});
	$('#add_condition_manufacturer').click(function() {
		add_condition(current_id_condition_group, 'manufacturer', $('#id_manufacturer option:selected').val());
		var html = '<tr><td>{l s='Manufacturer'}</td><td>'+$('#id_manufacturer option:selected').html()+'</td></tr>';
		$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
	});
	$('#add_condition_supplier').click(function() {
		add_condition(current_id_condition_group, 'supplier', $('#id_supplier option:selected').val());
		var html = '<tr><td>{l s='Supplier'}</td><td>'+$('#id_supplier option:selected').html()+'</td></tr>';
		$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
	});
	$('#add_condition_attribute').click(function() {
		add_condition(current_id_condition_group, 'attribute', $('#id_attribute_'+$('#id_attribute_group option:selected').val()+' option:selected').val());
		var html = '<tr><td>{l s='Attribute'}</td><td>'+$('#id_attribute_group option:selected').html()+': '+$('#id_attribute_'+$('#id_attribute_group option:selected').val()+' option:selected').html()+'</td></tr>';
		$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
	});
	$('#add_condition_feature').click(function() {
		add_condition(current_id_condition_group, 'feature', $('#id_feature_'+$('#id_feature option:selected').val()+' option:selected').val());
		var html = '<tr><td>{l s='Feature'}</td><td>'+$('#id_feature option:selected').html()+': '+$('#id_feature_'+$('#id_feature option:selected').val()+' option:selected').html()+'</td></tr>';
		$('#condition_group_'+current_id_condition_group+' table tbody').append(html);
	});
	$('#add_condition_group').click(function() {
		new_condition_group();
	});
	$('.condition_group').live('click', function() {
		var id = this.id.split('_');
		toggle_condition_group(id[2]);
	});
	{if $conditions|@count == 0}
		new_condition_group();
	{/if}
	{foreach from=$conditions key='id_group_condition' item='condition_group'}
		new_condition_group();
		{foreach from=$condition_group item='condition'}
			{if $condition.type == 'attribute'}
				$('#id_attribute_group option[value="{$condition.id_attribute_group}"]').attr('selected', 'selected');
				$('#id_attribute_{$condition.id_attribute_group} option[value="{$condition.value}"]').attr('selected', 'selected');
			{elseif $condition.type == 'feature'}
				$('#id_feature[value="{$condition.id_feature}"]').attr('selected', 'selected');
				$('#id_feature_{$condition.id_feature} option[value="{$condition.value}"]').attr('selected', 'selected');
			{else}
				$('#id_{$condition.type} option[value="{$condition.value}"]').attr('selected', 'selected');
			{/if}
			$('#add_condition_{$condition.type}').click();
		{/foreach}
	{/foreach}
	$('#id_attribute_group').change();
	$('#id_feature').change();
});
{/block}
