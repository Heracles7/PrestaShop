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

{if $firstCall}
	<script type="text/javascript">
		var vat_number = {$vat_number};
		var module_dir = '{$module_dir}';
		var id_language = {$defaultFormLanguage};
		var languages = new Array();

		$(document).ready(function() {ldelim}
			{foreach $languages as $k => $language}
				languages[{$k}] = {ldelim}
					id_lang: {$language.id_lang},
					iso_code: '{$language.iso_code}',
					name: '{$language.name}'
				{rdelim};
			{/foreach}
			displayFlags(languages, id_language, {$allowEmployeeFormLang});

			{if isset($fields_value.id_state)}
				if ($('#id_country') && $('#id_state'))
				{ldelim}
					ajaxStates({$fields_value.id_state});
					$('#id_country').change(function() {ldelim}
						ajaxStates();
					{rdelim});
				{rdelim}
			{/if}
		{rdelim});
	</script>
	<script type="text/javascript" src="../js/form.js"></script>
{/if}

<form action="{$current}&submitAdd{$table}=1&token={$token}" method="post">
	{if $form_id}
		<input type="hidden" name="id_{$table}" value="{$form_id}" />
	{/if}
	<fieldset>
		{foreach $fields as $key => $field}
			{if $key == 'legend'}
				<legend>
					{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
					{$field.title}
				</legend>
			{elseif $key == 'input'}
				{foreach $field as $input}
					{if $input.name == 'id_state'}
						<div id="contains_states" {if $contains_states}style="display:none;"{/if}>
					{/if}
					<label>{$input.label} </label>
					<div class="margin-form">
						{if $input.type == 'text'}
							{if isset($input.lang) && isset($input.attributeLang)}
								{foreach $languages as $language}
									<div id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
										<input type="text" 
												name="{$input.name}_{$language.id_lang}"
												value="{$fields_value[$input.name][$language.id_lang]}"
												{if isset($input.size)}size="{$input.size}"{/if} 
												{if isset($input.class)}class="{$input.class}"{/if} 
												{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if} />
									</div>
								{/foreach}
								{if count($languages) > 1}
									<div class="displayed_flag">
										<img src="../img/l/{$defaultFormLanguage}.jpg" 
											class="pointer" 
											id="language_current_{$input.name}" 
											onclick="toggleLanguageFlags(this);" />
									</div>
									<div id="languages_{$input.name}" class="language_flags">
										{l s='Choose language:'}<br /><br />
										{foreach $languages as $language}
												<img src="../img/l/{$language.id_lang}.jpg" 
													class="pointer" 
													alt="{$language.name}" 
													title="{$language.name}" 
													onclick="changeLanguage('{$input.name}', '{$input.attributeLang}', {$language.id_lang}, '{$language.iso_code}');" />
										{/foreach}
									</div>
								{/if}
							{else}
								<input type="text" 
										name="{$input.name}" 
										id="{$input.name}" 
										value="{$fields_value[$input.name]}" 
										{if isset($input.size)}size="{$input.size}"{/if} 
										{if isset($input.class)}class="{$input.class}"{/if} 
										{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if} />
							{/if}
						{elseif $input.type == 'select'}
							<select name="{$input.name}" id="{$input.name}" {if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
								{if isset($input.options.optiongroup)}
									{foreach $input.options.optiongroup.query AS $optiongroup}
										<optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
											{foreach $optiongroup[$input.options.options.query] as $option}
												<option value="{$option[$input.options.options.id]}" 
														{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}>{$option[$input.options.options.name]}</option>
											{/foreach}
										</optgroup>
									{/foreach}
								{else}
									{foreach $input.options.query AS $option}
										{$fields_value[$input.name]|@p}
										<option value="{$option[$input.options.id]}" 
												{if $fields_value[$input.name] == $option[$input.options.id]}selected="selected"{/if}>{$option[$input.options.name]}</option>
									{/foreach}
								{/if}
							</select>
						{elseif $input.type == 'radio'}
							{foreach $input.values as $value}
								<input type="radio" 
										name="{$input.name}" 
										id="{$value.id}" 
										value="{$value.value}" 
										{if $fields_value[$input.name] == $value.value}checked="checked"{/if} />
								<label {if isset($input.class)}class="{$input.class}"{/if} for="{$value.id}"> {$value.label}</label>
							{/foreach}
						{elseif $input.type == 'textarea'}
							{if isset($input.lang) && isset($input.attributeLang)}
								{foreach $languages as $language}
									<div id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
										<textarea cols="{$input.cols}" rows="{$input.rows}" name="{$input.name}_{$language.id_lang}">{$fields_value[$input.name][$language.id_lang]}</textarea>
									</div>
								{/foreach}
								{if count($languages) > 1}
									<div class="displayed_flag">
										<img src="../img/l/{$defaultFormLanguage}.jpg" 
											class="pointer" 
											id="language_current_{$input.name}" 
											onclick="toggleLanguageFlags(this);" />
									</div>
									<div id="languages_{$input.name}" class="language_flags">
										{l s='Choose language:'}<br /><br />
										{foreach $languages as $language}
												<img src="../img/l/{$language.id_lang}.jpg" 
													class="pointer" 
													alt="{$language.name}" 
													title="{$language.name}" 
													onclick="changeLanguage('{$input.name}', '{$input.attributeLang}', {$language.id_lang}, '{$language.iso_code}');" />
										{/foreach}
									</div>
								{/if}
							{else}
								<textarea name="{$input.name}" id="{$input.name}" cols="{$input.cols}" rows="{$input.rows}">{$fields_value[$input.name]}</textarea>
							{/if}
						{elseif $input.type == 'checkbox'}
							
						{/if}
						{if isset($input.required) && $input.required} <sup>*</sup>{/if}
						{if isset($input.p)}
							<p class="clear">
								{if is_array($input.p)}
									{foreach $input.p as $p}
										{if is_array($p)}
											<span id="{$p.id}">{$p.text}</span><br />
										{else}
											{$p}<br />
										{/if}
									{/foreach}
								{else}
									{$input.p}
								{/if}
							</p>
						{/if}
					</div>
					{if $input.name == 'id_state'}
						</div>
					{/if}
					<div class="clear"></div>
				{/foreach}
			{elseif $key == 'submit'}
				<div class="margin-form">
					<input type="submit" value="{$field.title}" name="submitAdd{$table}" {if isset($field.class)}class="{$field.class}"{/if} />
				</div>
			{/if}
		{/foreach}
		{if $requiredFields}
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		{/if}
	</fieldset>
</form>

<br /><br />
{if $firstCall}
	{if $back}
		<a href="{$back}"><img src="../img/admin/arrow2.gif" />{l s='Back'}</a>
	{else}
		<a href="{$current}&token={$token}"><img src="../img/admin/arrow2.gif" />{l s='Back to list'}</a>
	{/if}
	<br />
{/if}