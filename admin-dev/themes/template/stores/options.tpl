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
*  @version  Release: $Revision: 9369 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<br />

<form action="{$current}&submitOptions{$table}=1&token={$token}" method="post" enctype="multipart/form-data">
{foreach $option_list AS $category => $categoryData}
	{if isset($categoryData['top'])}{$categoryData['top']}{/if}
	<fieldset {if isset($categoryData['class'])}class="{$categoryData['class']}"{/if}>
	{* Options category title *}
	<legend>
		<img src="{$categoryData['image']}"/>
		{if isset($categoryData['title'])}{$categoryData['title']}{else}{l s='Options'}{/if}
	</legend>

	{* Category description *}
	{if (isset($categoryData['description']) && $categoryData['description'])}
		<div class="optionsDescription">{$categoryData['description']}</div>
	{/if}

	{foreach $categoryData['fields'] AS $key => $field}
		<div style="clear: both; padding-top:15px;" id="conf_id_{$key}" {if $field['is_invisible']} class="isInvisible"{/if}>
		{if $field['title']}
			<label class="conf_title">
			{if (isset($field['required']) && $field['required'])}
				<sup>*</sup>
			{/if}
			{$field['title']}</label>
		{/if}
		<div class="margin-form">
		
		{if $field['type'] == 'select'}
			<select name="{$key}"{if isset($field['js'])} onchange="{$field['js']}"{/if} id="{$key}">
				{foreach $field['list'] AS $k => $option}
					<option value="{$option[$field['identifier']]}"{if $field['value'] == $option[$field['identifier']]} selected="selected"{/if}>{$option['name']}</option>
				{/foreach}
			</select>
		{elseif $field['type'] == 'bool'}
			<label class="t" for="{$key}_on"><img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" /></label>
			<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
			<label class="t" for="{$key}_on"> {l s='Yes'}</label>
			<label class="t" for="{$key}_off"><img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="margin-left: 10px;" /></label>
			<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
			<label class="t" for="{$key}_off"> {l s='No'}</label>
		{elseif $field['type'] == 'radio'}
			{foreach $field['choices'] AS $k => $v}
				<input type="radio" name="{$key}" id="{$key}_{$k}" value="{$k}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
				<label class="t" for="{$key}_{$k}"> {$v}</label><br />
			{/foreach}
			<br />
		{*{elseif $field['type'] == 'checkbox'}
			{foreach $field['choices'] AS $k => $v}
				<input type="checkbox" name="{$key}" id="{$key}{$k}_on" value="{$k|intval}"{if $k == $field['value']} checked="checked"{/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if}/>
				<label class="t" for="{$key}{$k}_on"> {$v}</label><br />
			{/foreach}
			<br />
		*}
		{elseif $field['type'] == 'text'}
			<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{$field['value']|escape:'htmlall':'UTF-8'}" />
			{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
		{elseif $field['type'] == 'password'}
			<input type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="" />
			{if isset($field['suffix'])}&nbsp;{$field['suffix']|strval}{/if}
		{elseif $field['type'] == 'textarea'}
			<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|escape:'htmlall':'UTF-8'}</textarea>
		{elseif $field['type'] == 'file'}
			{if isset($field['thumb']) && $field['thumb'] && $field['thumb']['pos'] == 'before'}
				<img src="{$field['thumb']['file']}" alt="{$field['title']}" title="{$field['title']}" /><br />
			{/if}
			<input type="file" name="{$key}" />
	{*	{elseif $field['type'] == 'image'}	
			<table cellspacing="0" cellpadding="0">
			<tr>
			$i = 0;
			foreach ($field['list'] as $theme)
			{
				<td class="center" style="width: 180px; padding:0px 20px 20px 0px;">
					<input type="radio" name="{$key}" id="{$key}_{$theme['name']}_on" style="vertical-align: text-bottom;" value="{$theme['name']}"'.(_THEME_NAME_ == $theme['name'] ? 'checked="checked"' : '').' />';
					echo '<label class="t" for="{$key}_'.$theme['name'].'_on"> '.Tools::strtolower($theme['name']).'</label>';
					echo '<br />';
					echo '<label class="t" for="{$key}_'.$theme['name'].'_on">';
						echo '<img src="../themes/'.$theme['name'].'/preview.jpg" alt="'.Tools::strtolower($theme['name']).'">';
					echo '</label>';
				echo '</td>';
				if (isset($field['max']) && ($i +1 ) % $field['max'] == 0)
					echo '</tr><tr>';
				$i++;
			}
			echo '</tr>';
			echo '</table>';
	*}
		{elseif $field['type'] == 'textLang'}
			{foreach $field['languages'] AS $id_lang => $value}
				<div id="{$key}_{$id_lang}" style="margin-bottom:8px; display: {if $id_lang == $current_id_lang}'block'{else}'none'{/if}; float: left; vertical-align: top;">
					<input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}_{$id_lang}" value="{$value}" />
				</div>
			{/foreach}
			{$field['flags']}
		{elseif $field['type'] == 'textareaLang'}
			{foreach $field['languages'] AS $id_lang => $value}
				<div id="{$key}_{$id_lang}" style="display: {if $id_lang == $current_id_lang}'block'{else}'none'{/if}; float: left;">';
					<textarea rows="{$field['rows']}" cols="{$field['cols']|intval}"  name="{$key}_{$id_lang}">{$value|replace:'\r\n':"\n"}</textarea>
				</div>
			{/foreach}
			{$field['flags']}
			<br style="clear:both">			
			
			
		{/if}
		{if isset($field['method'])}$field['method']{/if}

		{if ($field['multishop_default'])}
			<div class="preference_default_multishop">
				<label>
					<input type="checkbox" name="configUseDefault['{$key}']" value="1" {if $field['is_disabled']} checked="checked"{/if} onclick="checkMultishopDefaultValue(this, '{$key}')" /> {l s='Use default value'}
				</label>
			</div>
		{/if}
		{if isset($field['desc'])}<p class="preference_description">{$field['desc']}</p>{/if}
		{if $field['is_invisible']}<p class="multishop_warning">{l s='You can\'t change the value of this configuration field in this shop context'}</p>{/if}
		</div></div>
	{/foreach}
	
	<div align="center" style="margin-top: 20px;">
		<input type="submit" value="{l s='   Save   '}" name="submit{$category|ucfirst}{$table}" class="button" />
	</div>
	{if $field['required']}
		<div class="small"><sup>*</sup> {l s='Required field'}</div>
	{/if}
	</fieldset><br />
	{if isset($categoryData['bottom'])}{$categoryData['bottom']}{/if}
{/foreach}
</form><br />
<p>
	<img src="../img/admin/asterisk.gif" class="middle" />
	{l s='You can also replace the icon representing your store in Google Maps. Go to the Preferences tab, and then the Appearance subtab.'}
</p>
