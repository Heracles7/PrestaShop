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

{if $show_toolbar}
	<div class="toolbar-placeholder">
		<div class="toolbarBox{if $toolbar_fix} toolbarHead{/if}">
				{include file="toolbar.tpl" toolbar_btn=$toolbar_btn}
				<div class="pageTitle">
				<h3>
					<span id="current_obj" style="font-weight: normal;">{$title|default:'&nbsp;'}</span>
				</h3>
				</div>
			<div class="leadin">{block name="leadin"}{/block}</div>
		</div>
	</div>
{/if}

{if $module_confirmation}
	<div class="module_confirmation conf confirm"><img src="../img/admin/ok.gif" alt="" title="" style="margin-right:5px; float:left;" />
		{l s='The .CSV file has been imported into your shop.'}
	</div>
{/if}

<script type="text/javascript">

	$(document).ready(function(){
		activeClueTip();
	});

	function activeClueTip()
	{
		$('.info').cluetip({
			splitTitle: '|',
		    showTitle: false
	 	});

		$('#preview_import').submit(function() {
			if ($('#truncate').get(0).checked)
			{
				if (confirm('{l s="Are you sure you want to delete"}' + ' ' + $('#entity > option:selected').text().toLowerCase() + '{l s="?"}'))
				{
					this.submit();
				}
				else
				{
					return false;
				}
			}
		});
	};

</script>

<fieldset style="float: left; margin: 0pt 20px 0pt 0pt; height: 160px;width: 666px;">
	<legend><img src="../img/admin/import.gif" />{l s='Upload'}</legend>
	<form action="{$current}&token={$token}" method="POST" enctype="multipart/form-data">
		<label class="clear">{l s='Select a file:'} </label>
		<div class="margin-form">
			<input name="file" type="file" /><br />{l s='You can also upload your file by FTP and put it in'} {$path_import}.
		</div>
		<div class="margin-form">
			<input type="submit" name="submitFileUpload" value="{l s='Upload'}" class="button" />
		</div>
		<div class="margin-form">
			{l s='Allowed files are only UTF-8 and iso-8859-1 encoded ones'}
		</div>
	</form>
</fieldset>

<fieldset>
	<legend><img src="../img/admin/excel_file.png">{l s='Sample files'}</legend>
		<ul style="">
			<li style="text-decoration: underline"><a href="../docs/csv_import/categories_import.csv">{l s='Categories sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/products_import.csv">{l s='Products sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/combinations_import.csv">{l s='Combinations sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/customers_import.csv">{l s='Customers sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/addresses_import.csv">{l s='Addresses sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/manufacturers_import.csv">{l s='Manufacturers sample file'}</a></li>
			<li style="text-decoration: underline"><a href="../docs/csv_import/suppliers_import.csv">{l s='Suppliers sample file'}</a></li>
		</ul>
</fieldset>

<div class="clear">&nbsp;</div>

<form id="preview_import"
	action="{$current}&token={$token}"
	method="post"
	style="display:inline"
	enctype="multipart/form-data"
	class="clear">
	<fieldset style="float: left; margin: 0pt 20px 0pt 0pt; width: 666px;">
		<legend><img src="../img/admin/import.gif" />{l s='Import'}</legend>
		<label class="clear">{l s='Select which entity to import:'} </label>
		<div class="margin-form">
			<select name="entity" id="entity">';
				{foreach $entities AS $entity => $i}
					<option value="{$i}"
						{if $entity == $i}selected="selected"{/if}>
						{$entity}
					</option>
				{/foreach}
			</select>
		</div>

		{if count($files_to_import)}
			<label class="clear">{l s='Select your .CSV file:'} </label>
			<div class="margin-form">
				<select name="csv">
					{foreach $files_to_import AS $filename}
						<option value="{$filename}">{$filename}</option>
					{/foreach}
				</select>
				({count($files_to_import)} {if count($files_to_import) > 1} {l s='files available'}{else}{l s='file available'}{/if})
			</div>
			<label class="clear">{l s='Select language of the file (the locale must be installed):'} </label>
			<div class="margin-form">
				<select name="iso_lang">';
					{foreach $languages AS $lang}
						<option value="{$lang.iso_code}" {if $lang.id_lang == $id_language} selected="selected"{/if}>{$lang.name}</option>
					{/foreach}
				</select>
			</div>
			<label for="convert" class="clear">{l s='iso-8859-1 encoded file:'} </label>
			<div class="margin-form">
				<input name="convert" id="convert" type="checkbox" style="margin-top: 6px;"/>
			</div>
			<label class="clear">{l s='Field separator:'} </label>
			<div class="margin-form">
				<input type="text" size="2" value=";" name="separator"/>
				{l s='e.g. '}"1<span class="bold" style="color: red">;</span>Ipod<span class="bold" style="color: red">;</span>129.90<span class="bold" style="color: red">;</span>5"
			</div>
			<label class="clear">{l s='Multiple value separator:'} </label>
			<div class="margin-form">
				<input type="text" size="2" value="," name="multiple_value_separator"/>
				{l s='e.g. '}"Ipod;red.jpg<span class="bold" style="color: red">,</span>blue.jpg<span class="bold" style="color: red">,</span>green.jpg;129.90"
			</div>
			<label for="truncate" class="clear">{l s='Delete all'} <span id="entitie">{l s='categories'}</span> {l s='before import ?'} </label>
			<div class="margin-form">
				<input name="truncate" id="truncate" type="checkbox"/>
			</div>
			<label for="match_ref" class="clear" style="display: none">{l s='Use product reference as key ?'}</label>
			<div class="margin-form">
				<input name="match_ref" id="match_ref" type="checkbox" style="margin-top: 6px; display:none"/>
			</div>
			<div class="space margin-form">
				<input type="submit" name="submitImportFile" value="{l s='Next step'}" class="button"/>
			</div>
			<div class="warn">
				<p>{l s='Note that the category import does not support categories of the same name'}.</p>

				<p>{l s='Note that references are not specified as UNIQUE in the database'}.</p>
			</div>
		{else}
			<div class="warn">
				{l s='No CSV file is available, please upload one file above.'}<br /><br />
				{l s='You can get many informations about CSV import at:'} <a href="http://www.prestashop.com/wiki/Troubleshooting_6/" target="_blank">http://www.prestashop.com/wiki/Troubleshooting_6/</a><br /><br />
				{l s='More about CSV format at: '} <a href="http://en.wikipedia.org/wiki/Comma-separated_values" target="_blank">http://en.wikipedia.org/wiki/Comma-separated_values</a>
			</div>
		{/if}
	</fieldset>
</form>

<fieldset>

	<legend>
		<img src="../img/admin/import.gif" />{l s='Fields available'}
	</legend>

	<div id="availableFields" style="min-height: 218px; width: 300px;">
		{$available_fields}
	</div>

	<div class="clear">
		<br /><br />{l s='* Required Fields'}
	</div>

</fieldset>
		
<div class="clear">&nbsp;</div>

<script type="text/javascript">
	$("select#entity").change( function() {

		if ($("#entity > option:selected").val() != 1)
		{
			$("label[for=match_ref],#match_ref").hide();
		}

		if ($("#entity > option:selected").val() == 1)
		{
			$("label[for=match_ref],#match_ref").show();
		}

		$("#entitie").html($("#entity > option:selected").text().toLowerCase());
		$.getJSON("ajax.php",
		{
			getAvailableFields:1,
			entity: $("#entity").val()
		},
		function(j)
		{
			var fields = "";
			$("#availableFields").empty();
			for (var i = 0; i < j.length; i++)
				fields += j[i].field;
			$("#availableFields").html(fields);
			activeClueTip();
		});

	});
</script>
