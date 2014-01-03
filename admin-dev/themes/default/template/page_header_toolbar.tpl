{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="page-head">
	{block name=pageTitle}
	<h2 class="page-title">
		{*if isset($toolbar_btn['back'])}
		<a id="page-header-desc-{$table}{if isset($toolbar_btn['back'].imgclass)}-{$toolbar_btn['back'].imgclass}{/if}" class="page-header-toolbar-back" {if isset($toolbar_btn['back'].href)}href="{$toolbar_btn['back'].href}"{/if} title="{$toolbar_btn['back'].desc}" {if isset($toolbar_btn['back'].target) && $toolbar_btn['back'].target}target="_blank"{/if}{if isset($toolbar_btn['back'].js) && $toolbar_btn['back'].js}onclick="{$toolbar_btn['back'].js}"{/if}>
			<i class="process-icon-back"></i>
		</a>
		{/if*}
		{$breadcrumbs2.tab.name}
		{* {if is_array($title)}{$title|end}{else}{$title}{/if} *}
	</h2>
	{/block}

	{block name=pageBreadcrumb}
	<ul class="breadcrumb page-breadcrumb">
		{* Container *}
		{if $breadcrumbs2.container.name != ''}
			<li>
				{if $breadcrumbs2.container.href != ''}<a href="{$breadcrumbs2.container.href}">{/if}
				{if $breadcrumbs2.container.icon != ''}<i class="{$breadcrumbs2.container.icon}"></i>{/if}
				{$breadcrumbs2.container.name}
				{if $breadcrumbs2.container.href != ''}</a>{/if}
			</li>
		{/if}
		
		{* Current Tab *}
		{if $breadcrumbs2.tab.name != ''}
			<li>
				{if $breadcrumbs2.tab.href != ''}<a href="{$breadcrumbs2.tab.href}">{/if}
				{if $breadcrumbs2.tab.icon != ''}<i class="{$breadcrumbs2.tab.icon}"></i>{/if}
				{$breadcrumbs2.tab.name}
				{if $breadcrumbs2.tab.href != ''}</a>{/if}
			</li>
		{/if}
		
		{* Action *}
		{if $breadcrumbs2.action.name != ''}
			<li>
				{if $breadcrumbs2.action.href != ''}<a href="{$breadcrumbs2.action.href}">{/if}
				{if $breadcrumbs2.action.icon != ''}<i class="{$breadcrumbs2.action.icon}"></i>{/if}
				{$breadcrumbs2.action.name}
				{if $breadcrumbs2.action.href != ''}</a>{/if}
			</li>
		{/if}
		</ul>
	{/block}

	<div class="page-bar toolbarBox">
		<div class="btn-toolbar">
			{block name=toolbarBox}
			<ul class="nav nav-pills pull-right">
				{foreach from=$toolbar_btn item=btn key=k}
				{if $k != 'back' && $k != 'modules-list'}
				<li>
					<a id="page-header-desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="toolbar_btn" {if isset($btn.href)}href="{$btn.href}"{/if} title="{$btn.desc}" {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
						<i class="{if isset($btn.icon)}{$btn.icon}{else}process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i>
						<span {if isset($btn.force_desc) && $btn.force_desc == true } class="locked" {/if}>{$btn.desc}</span>
					</a>
				</li>
				{/if}
				{/foreach}
				{if isset($toolbar_btn['modules-list'])}
				<li>
					<a id="page-header-desc-{$table}-{if isset($toolbar_btn['modules-list'].imgclass)}{$toolbar_btn['modules-list'].imgclass}{else}modules-list{/if}" class="toolbar_btn{if isset($toolbar_btn['modules-list'].class)} {$toolbar_btn['modules-list'].class}{/if}" {if isset($toolbar_btn['modules-list'].href)}href="{$toolbar_btn['modules-list'].href}"{/if} title="{$toolbar_btn['modules-list'].desc}" {if isset($toolbar_btn['modules-list'].target) && $toolbar_btn['modules-list'].target}target="_blank"{/if}{if isset($toolbar_btn['modules-list'].js) && $toolbar_btn['modules-list'].js}onclick="{$toolbar_btn['modules-list'].js}"{/if}>
						<i class="{if isset($toolbar_btn['modules-list'].icon)}{$toolbar_btn['modules-list'].icon}{else}process-icon-{if isset($toolbar_btn['modules-list'].imgclass)}{$toolbar_btn['modules-list'].imgclass}{else}modules-list{/if}{/if}" ></i>
						<span {if isset($toolbar_btn['modules-list'].force_desc) && $toolbar_btn['modules-list'].force_desc == true } class="locked" {/if}>{$toolbar_btn['modules-list'].desc}</span>
					</a>
				</li>
				{/if}
			</ul>

			<script language="javascript" type="text/javascript">
			//<![CDATA[
				var modules_list_loaded = false;

				$(function() {

					{if isset($tab_modules_open) && $tab_modules_open}
						$('#modules_list_container').modal('show');
						openModulesList();
					{/if}
				});

				{if isset($tab_modules_list)}
					$('.process-icon-modules-list').parent('a').unbind().bind('click', function (){
						$('#modules_list_container').modal('show');
						openModulesList();
					});
				{/if}
			//]]>
			</script>
			{/block}
		</div>
	</div>
</div>
