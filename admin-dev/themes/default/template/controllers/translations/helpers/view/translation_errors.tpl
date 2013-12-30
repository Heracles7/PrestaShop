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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	{if !empty($limit_warning)}
	<div class="alert alert-warning">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning: Your hosting provider is using the suhosin patch for PHP, which limits the maximum number of fields allowed in a form:'}

			<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the suhosin limit to'}
		{else}
			{l s='Warning! Your PHP configuration limits the maximum number of fields allowed in a form:'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the this limit to'}
		{/if}
		{l s='%s at least or edit the translation file manually.' sprintf=$limit_warning['needed_limit']}
	</div>
	{else}

		<div class="alert alert-info">
			{l s='Some sentences to translate use this syntax: %s... These are variables, and PrestaShop take care of replacing them before displaying your translation. You must leave these in your translations, and place them appropriately in your sentence.' sprintf='%d, %s, %1$s, %2$d'}
		</div>
		<div class="panel">
			<p>{l s='Expressions to translate:'} <span class="badge">{l s='%d' sprintf=$count}</span></p>
			<p>{l s='Total missing expresssions:'} <span class="badge">{l s='%d' sprintf=$missing_translations|array_sum}</p>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				$('a.useSpecialSyntax').click(function(){
					var syntax = $(this).find('img').attr('alt');
					$('#BoxUseSpecialSyntax .syntax span').html(syntax+".");
					$('#BoxUseSpecialSyntax').toggle(1000);
				});
				$('#BoxUseSpecialSyntax').click(function(){
					$('#BoxUseSpecialSyntax').toggle(1000);
				});
			});
		</script>

		<div id="BoxUseSpecialSyntax">
			<div class="alert alert-warning">
				<p>
					{l s='This expression uses this special syntax:'} <span>%d.</span><br />
					{l s='You must use this syntax in your translations. Here are several examples:'}
				</p>
				<ul class="nav">
					<li><em>There are <strong>%d</strong> products</em> ("<strong>%d</strong>" {l s='will be replaced by a number'}).</li>
					<li><em>List of pages in <strong>%s</strong>:</em> ("<strong>%s</strong>" {l s='will be replaced by a string'}).</li>
					<li><em>Feature: <strong>%1$s</strong> (<strong>%2$d</strong> values)</em> ("<strong>n$</strong>" {l s='is used for the order of the arguments'}).</li>
				</ul>
			</div>
		</div>

		<form method="post" id="{$table}_form" action="{$url_submit}" class="form-horizontal">
			<div class="panel">
				{*{$auto_translate}$*}
				<input type="hidden" name="lang" value="{$lang}" />
				<input type="hidden" name="type" value="{$type}" />
				<input type="hidden" name="theme" value="{$theme}" />

				<table class="table">
					{foreach $errorsArray as $key => $value}
						<tr {if empty($value.trad)}style="background-color:#FBB"{else}{cycle values='class="alt_row",'}{/if}>
							<td width="40%">{$key|stripslashes}</td>
							<td width="40%">
								<input type="text" name="{$key|md5}" value="{$value.trad|regex_replace:'#"#':'&quot;'|stripslashes}"' style="width: 450px{if empty($value.trad)};background:#FBB{/if}">
							</td>
							<td width="18%">
								{if isset($value.use_sprintf) && $value.use_sprintf}
									<a class="useSpecialSyntax" title="{l s='This expression uses a special syntax:'} {$value.use_sprintf}" style="cursor:pointer">
										<img src="{$smarty.const._PS_IMG_}admin/error.png" alt="{$value.use_sprintf}" />
									</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				</table>
				<div class="panel-footer">
					<a name="submitTranslations{$type|ucfirst}" href="{$cancel_url}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
					<button type="submit" name="submitTranslations{$type|ucfirst}AndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
				</div>
			</div>
		</form>
	{/if}

{/block}
