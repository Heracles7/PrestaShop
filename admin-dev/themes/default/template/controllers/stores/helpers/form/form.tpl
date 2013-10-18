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

{block name=script}
	$(document).ready(function() {
		$('#latitude, #longitude').keyup(function() {
			$(this).val($(this).val().replace(/,/g, '.'));
		});
	});
{/block}

{block name="input"}
	{if $input.type == 'latitude'}
	<div class="row">
		<div class="col-lg-3">
			<input type="text"
				{if isset($input.size)}size="{$input.size}"{/if}
				{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
				name="latitude"
				id="latitude"
				value="{$fields_value[$input.name]|escape:'htmlall'}" />
		</div>
		<div class="col-lg-1">
			<div class="form-control-static text-center"> / </div>
		</div>
		<div class="col-lg-3">
			<input type="text"
				{if isset($input.size)}size="{$input.size}"{/if}
				{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
				name="longitude"
				id="longitude"
				value="{$fields_value['longitude']|escape:'htmlall'}" />
		</div>
	</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="other_input"}
	{if $key == 'rightCols'}
		{foreach $field as $input}
			{if $input.type == 'file'}
				<div class="form-group">
					<label class="control-label col-lg-3">{$input.label} </label>
					<div class="col-lg-7">
						<input id="{$input.name}" type="file" name="{$input.name}" class="hide" />
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="{$input.name}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="{$input.name}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file'}
								</button>
							</span>
						</div>
						{if isset($input.desc)}<p>{$input.desc}</p>{/if}
						{if isset($fields_value.image) && $fields_value.image}
							<div class="clearfix">&nbsp;</div>
							<div id="image" class="thumbnail">
								{$fields_value.image}
								<p>{l s='File size'} {$fields_value.size}kb</p>
								<a class="btn btn-default" href="{$current}&{$identifier}={$form_id}&token={$token}&deleteImage=1">
									<i class="icon-trash"></i> {l s='Delete'}
								</a>
							</div>
						{/if}
						<script>
							$(document).ready(function(){
								$('#{$input.name}-selectbutton').click(function(e){
									$('#{$input.name}').trigger('click');
								});
								$('#{$input.name}-name').click(function(e){
									$('#{$input.name}').trigger('click');
								});
								$('#{$input.name}').change(function(e){
									var val = $(this).val();
									var file = val.split(/[\\/]/);
									$('#{$input.name}-name').val(file[file.length-1]);
								});
							});
						</script>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Hours:'}</label>
				<div class="col-lg-9"><p class="form-control-static">{l s='e.g. 10:00AM - 9:30PM'}</p></div>
			</div>
			{foreach $fields_value.days as $k => $value}
			<div class="form-group">
				<label class="control-label col-lg-3">{$value}</label>
				<div class="col-lg-9"><input type="text" size="25" name="hours_{$k}" value="{if isset($fields_value.hours[$k-1])}{$fields_value.hours[$k-1]|escape:'htmlall'}{/if}" /></div>
			</div>
			{/foreach}
		{/foreach}
	{/if}
{/block}