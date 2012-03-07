{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="field_block"}
	{if $input.type == 'address_layout'}
		<div class="margin-form">
			<div style="float:left">
				<textarea id="ordered_fields" name="address_layout" style="width: 300px;height: 140px;">{$input.address_layout}</textarea>
			</div>
			<div style="float:left; margin-left:20px; width:340px;">
				{l s='Liable fields for the address information (click to have more details):'} {$input.display_valid_fields}
			</div>
			<div class="clear"></div>
			<div style="margin:10px 0 10px 0;">
				<a id="useLastDefaultLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout('{$input.encoding_address_layout}', 'lastDefault');" class="button">
					{l s='Use the last registered layout'}</a>
				<a id="useDefaultLayoutSystem" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout('{$input.encoding_default_layout}', 'defaultSystem');" class="button">
					{l s='Use a default layout'}</a>
				<a id="useCurrentLastModifiedLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout(lastLayoutModified, 'currentModified')" class="button">
					{l s='Use my current modified layout'}</a>
				<a id="eraseCurrentLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout('', 'erase');" class="button">
					{l s='Clean layout'}</a>
				<div style="margin-top:10px; padding-top:5px; height:10px;" id="explanationText"></div>
			</div>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name=script}

	$(document).ready(function() {

		$('.availableFieldsList').css("display", "none");

		$('.addPattern').click(function() {
			addFieldsToCursorPosition($(this).attr("id"))
			lastLayoutModified = $("#ordered_fields").val();
		});

		$('#ordered_fields').keyup(function() {
			lastLayoutModified = $(this).val();
		});

		$('#useLastDefaultLayout').mouseover(function() {
			switchExplanationText("{l s='Will display back your last registered layout'}");
		});

		$('#useDefaultLayoutSystem').mouseover(function() {
			switchExplanationText("{l s='Will display a default layout for this country'}");
		});

		$('#useCurrentLastModifiedLayout').mouseover(function() {
			switchExplanationText("{l s='Will display back you\'re current editing layout'}");
		});

		$('#eraseCurrentLayout').mouseover(function() {
			switchExplanationText("{l s='Will delete the current layout'}");
		});

		$('#need_zip_code_on, #need_zip_code_off').change(function() {
			disableZipFormat();
		});

	});

	function switchExplanationText(text) {
		$("#explanationText").fadeOut("fast", function() {
			$(this).html(text);
			$(this).fadeIn("fast");
		});
	}

	function addFieldsToCursorPosition(pattern) {
		$("#ordered_fields").replaceSelection(pattern + " ");
	}

	function displayAvailableFields(containerName) {
		$(".availableFieldsList").each( function () {
			if ($(this).attr('id') != 'availableListFieldsFor_'+containerName)
			$(this).slideUp();
		});
		$("#availableListFieldsFor_" + containerName).slideToggle();
	}

	function resetLayout(defaultLayout, type) {
		if (confirm("{l s='Are you sure to apply this selection ?'}"))
		$("#ordered_fields").val(unescape(defaultLayout.replace(/\+/g, " ")));
	}

{/block}
