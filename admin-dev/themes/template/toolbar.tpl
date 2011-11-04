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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{block name=toolbarBox}
	<ul class="cc_button">
		{foreach from=$toolbar_btn item=btn key=k}
			<li>
				<a id="desc-{$table}-{$btn.imgclass|default:$k}" class="toolbar_btn" href="{$btn.href}" title="{$btn.desc}">
					<span class="process-icon-{$btn.imgclass|default:$k} {$btn.class|default:'' }" ></span>
					<div>{$btn.desc}</div>
				</a>
			</li>
		{/foreach}
	</ul>
	<script language="javascript">
		$(function() {
			//get reference on save link
			btn_save = $('span[class~="process-icon-save"]').parent();

			//get reference on form submit button
			btn_submit = $('#{$table}_form_submit_btn');

			//get reference on current save link label
			lbl_save = $('#desc-{$table}-save div');

			//override save link label with submit button value
			lbl_save.html(btn_submit.attr("value"));

			//add hidden input to emulate submit button click when posting the form -> field name posted
			btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

			//hide standard submit button
			btn_submit.hide();

			//submit the form
			{block name=formSubmit}
				btn_save.click(function() {
					$('#{$table}_form').submit();
				});
			{/block}
		});
	</script>
{/block}
