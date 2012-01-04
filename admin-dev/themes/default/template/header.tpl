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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$iso}" lang="{$iso}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="NOFOLLOW, NOINDEX" />
		<title>{$meta_title} - PrestaShop&trade;</title>
		{if $display_header}
			<script type="text/javascript">
				var class_name = '{$class_name}';
				var iso_user = '{$iso_user}';
				var country_iso_code = '{$country_iso_code}';
				var _PS_VERSION_ = '{$smarty.const._PS_VERSION_}';

				var helpboxes = {$help_box};
				var roundMode = {$round_mode};
				{if isset($shop_context)}
					{if $shop_context == 'all'}
						var youEditFieldFor = "{l s='A modification of this field will be applied for all shops'}";
					{elseif $shop_context == 'group'}
						var youEditFieldFor = "{l s='A modification of this field will be applied for all shops of group '}<b>{$shop_name}</b>";
					{else}
						var youEditFieldFor = "{l s='A modification of this field will be applied for the shop '}<b>{$shop_name}</b>";
					{/if}
				{else}
					var youEditFieldFor = '';
				{/if}
				{* Notifications vars *}
				var autorefresh_notifications = '{$autorefresh_notifications}';
				var new_order_msg = '{l s='A new order has been made on your shop.'}';
				var order_number_msg = '{l s='Order number : '}';
				var total_msg = '{l s='Total : '}';
				var from_msg = '{l s='From : '}';
				var see_order_msg = '{l s='Click here to see that order'}';
				var new_customer_msg = '{l s='A new customer registered on your shop.'}';
				var customer_name_msg = '{l s='Customer name : '}';
				var see_customer_msg = '{l s='Click here to see that customer'}';
				var new_msg = '{l s='A new message posted on your shop.'}';
				var excerpt_msg = '{l s='Excerpt : '}';
				var see_msg = '{l s='Click here to see that message'}';
				var token_admin_orders = '{getAdminToken tab='AdminOrders'}';
				var token_admin_customers = '{getAdminToken tab='AdminCustomers'}';
				var token_admin_customer_threads = '{getAdminToken tab='AdminCustomerThreads'}';
			</script>
		{/if}

		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
			<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
			{/foreach}
		{/if}
		{if isset($js_files)}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri}"></script>
			{/foreach}
		{/if}

		<link rel="shortcut icon" href="{$img_dir}favicon.ico" />
		{if $display_header}
			{hook h="displayBackOfficeHeader"}
		{/if}
		<!--[if IE]>
		<link type="text/css" rel="stylesheet" href="{$base_url}css/admin-ie.css" />
		<![endif]-->
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {
				color:{$brightness}
			}
		</style>
	</head>
	<body{if $bo_color} style="background:{$bo_color}"{/if}>
{if $display_header}
	<div id="ajax_running"><img src="../img/admin/ajax-loader-yellow.gif" alt="" /> {l s='Loading...'}</div>

	<div id="top_container">
		<div id="container">
			{* begin  HEADER *}
			<div id="header">
				<div id="header_infos">
				<a id="header_shopname" href="index.php"><span>{$shop_name}</span></a><div id="notifs_icon_wrapper">
				{if {$show_new_orders} == 1}
					<div id="orders_notif" class="notifs">
						<span id="orders_notif_number_wrapper" class="number_wrapper">
							<span id="orders_notif_value">0</span>
						</span>
						<div id="orders_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last orders'}</h3>
							<p class="no_notifs">{l s='No new orders has been made on your shop'}</p>
							<ul id="list_orders_notif"></ul>
							<p><a href="index.php?controller=AdminOrders&token={getAdminToken tab='AdminOrders'}">{l s='Show all orders'}</a></p>
						</div>
					</div>
				{/if}
				{if ($show_new_customers == 1)}
					<div id="customers_notif" class="notifs notifs_alternate">
						<span id="customers_notif_number_wrapper" class="number_wrapper">
							<span id="customers_notif_value">0</span>
						</span>
						<div id="customers_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last customers'}</h3>
							<p class="no_notifs">{l s='No new customers registered on your shop'}</p>
							<ul id="list_customers_notif"></ul>
							<p><a href="index.php?controller=AdminCustomers&token={getAdminToken tab='AdminCustomers'}">{l s='Show all customers'}</a></p>
						</div>
					</div>
				{/if}
				{if {$show_new_messages} == 1}
					<div id="customer_messages_notif" class="notifs">
						<span id="customer_messages_notif_number_wrapper" class="number_wrapper">
							<span id="customer_messages_notif_value">0</span>
						</span>
						<div id="customer_messages_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last messages'}</h3>
							<p class="no_notifs">{l s='No new messages posted on your shop'}</p>
							<ul id="list_customer_messages_notif"></ul>
							<p><a href="index.php?tab=AdminCustomerThreads&token={getAdminToken tab='AdminCustomerThreads'}">{l s='Show all messages'}</a></p>
						</div>
					</div>
				{/if}
				</div>
				<span id="employee_links">
				<span class="employee_name">{$first_name}&nbsp;{$last_name}</span>
						<span class="separator"></span>
						<a class="employee" href="index.php?controller=AdminEmployees&id_employee={$employee->id}&updateemployee&token={getAdminToken tab='AdminEmployees'}"  alt="" /> {l s='My preferences'}</a>
					<span class="separator"></span><a href="index.php?logout" id="header_logout">
						<span>{l s='logout'}</span>
					</a>
				{if {$base_url}}
					<span class="separator"></span> <a href="{$base_url}" id="header_foaccess" target="_blank" title="{l s='View my shop'}"><span>{l s='View my shop'}</span></a>
				{/if}
			</span>
			<div id="header_search">
			<form method="post" action="index.php?controller=AdminSearch&token={getAdminToken tab='AdminSearch'}">
			<select name="bo_search_type" id="bo_search_type">
					<option value="0">{l s='everywhere'}</option>
					<option value="1" {if {$search_type} == 1} selected="selected" {/if}>{l s='catalog'}</option>
					<optgroup label="{l s='customers'}:">
					<option value="2" {if {$search_type} == 2} selected="selected" {/if}>{l s='by name'}</option>
					<option value="6" {if {$search_type} == 6} selected="selected" {/if}>{l s='by ip address'}</option>
					</optgroup>
					<option value="3" {if {$search_type} == 3} selected="selected" {/if}>{l s='orders'}</option>
					<option value="4" {if {$search_type} == 4} selected="selected" {/if}>{l s='invoices'}</option>
					<option value="5" {if {$search_type} == 5} selected="selected" {/if}>{l s='carts'}</option>
				</select>
				<input type="text" name="bo_query" id="bo_query" value="{$bo_query}" />
				<input type="submit" id="bo_search_submit" class="button" value="{l s='Search'}"/>
			</form>
		</div>
		<div id="header_quick">
		<script type="text/javascript">
			function quickSelect(elt)
			{
				var eltVal = $(elt).val();
				if (eltVal == "0") return false;
				else if (eltVal.substr(eltVal.length - 6) == '_blank') window.open(eltVal.substr(0, eltVal.length - 6), '_blank');
				else location.href = eltVal;
			}
		</script>
		<select onchange="quickSelect(this);" id="quick_select">
			<option value="0">{l s='Quick Access'}</option>
			{foreach $quick_access as $quick}
				<option value="{$quick.link}{if $quick.new_window}_blank{/if}">&gt; {$quick.name}</option>
			{/foreach}
		</select>
	</div>

	{if $multi_shop}
		<div id="header_shoplist"><span>{l s='Select your shop:'}</span>{$shop_list}</div>
	{/if}
	{hook h="displayBackOfficeTop"}
	</div>
	<ul id="menu">
		{if !$tab}
			<div class="mainsubtablist" style="display:none">
			</div>
		{/if}
		{foreach $tabs AS $t}
		<li class="submenu_size maintab {if $t.current}active{/if}" id="maintab{$t.id_tab}">
			<span class="title">
				<img src="{$t.img}" alt="" />{$t.name}
			</span>
			<ul class="submenu">
				{foreach from=$t.sub_tabs item=t2}
					<li><a href="{$t2.href}">{$t2.name}</a></li>
				{/foreach}
			</ul>
		</li>
	{/foreach}
	</ul>
</div> {* end header *}
{/if}
<div id="main">
	<div id="content">
		{if $display_header && $install_dir_exists}
			<div style="background-color: #FFEBCC;border: 1px solid #F90;line-height: 20px;margin: 0px 0px 10px;padding: 10px 20px;">
				{l s='For security reasons, you must also:'}  {l s='delete the /install folder'}
			</div>
		{/if}

		{if $display_header && $is_multishop && $shop_context != 'all'}
			<div class="multishop_info">
				{if $shop_context == 'group'}
					{l s='You are configuring your store for group shop '} <b>{$group_shop->name}</b>
				{elseif $shop_context == 'shop'}
					{l s='You are configuring your store for shop '} <b>{$shop->name}</b>
				{/if}
			</div>
		{/if}
