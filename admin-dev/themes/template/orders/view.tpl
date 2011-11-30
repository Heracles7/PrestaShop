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

{extends file="helper/view/view.tpl"}

{block name="override_tpl"}
	<script type="text/javascript">
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')}";
	var id_order = {$order->id};
	var id_lang = {$current_id_lang};
	var id_currency = {$order->id_currency};
	{assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
	var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
	var currency_sign = "{$currency->sign}";
	var currency_format = "{$currency->format}";
	var currency_blank = "{$currency->blank}";
	var priceDisplayPrecision = 2;
	var use_taxes = {$order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
	var token = "{$smarty.get.token|escape:'htmlall':'UTF-8'}";

	var txt_add_product_stock_issue = "{l s='You want to add more product than available in stock, are you sure you want to add this quantity?'}";
	var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?'}";
	var txt_add_product_no_product = "{l s='Error: No product has been selected'}";
	var txt_add_product_no_product_quantity = "{l s='Error: Quantity of product must be set'}";
	var txt_add_product_no_product_price = "{l s='Error: Price of product must be set'}";
	var txt_confirm = "{l s='Are you sure?'}";

	{literal}
		function showWarehouseList()
		{
			{/literal}{if (count($warehouse_list) > 1)}{literal}
				$('#warehouse').show();
			{/literal}{/if}{literal}
		}

		function hideWarehouseList()
		{
			$('#warehouse').hide();
		}

		$(document).ready(function() {
			hideWarehouseList();
			$("input.datepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
		});
	{/literal}
	</script>

	{if ($HOOK_INVOICE)}
	<div style="float: right; margin: -40px 40px 10px 0;">{$HOOK_INVOICE}</div><br class="clear" />';
	{/if}

	<fieldset style="width:98%; margin-bottom: 10px;">
		<div style="width:50%; float: left;">
			{if (count($invoices_collection))}
			<a href="pdf.php?id_order={$order->id}&pdf"><img src="../img/admin/charged_ok.gif" alt="{l s='View invoice'}" /> {l s='View invoice'}</a>
			{else}
			<img src="../img/admin/charged_ko.gif" alt="{l s='No invoice'}" /> {l s='No invoice'}
			{/if}
			 -
			{if ($currentState->delivery || $order->delivery_number)}
			<a href="pdf.php?id_delivery={$order->delivery_number}"><img src="../img/admin/delivery.gif" alt="{l s='View delivery slip'}" /> {l s='View delivery slip'}</a>
			{else}
			<img src="../img/admin/delivery_ko.gif" alt="{l s='No delivery slip'}" /> {l s='No delivery slip'}
			{/if}
			 -
			<a href="javascript:window.print()"><img src="../img/admin/printer.gif" alt="{l s='Print order'}" title="{l s='Print order'}" /> {l s='Print order'}</a>
		</div>
		<div style="width:50%; float: left;text-align:right;">
			<ul style="margin:0;padding:0;list-style-type:none;">
				<li style="display: inline;">{l s='Date'}: <b>{dateFormat date=$order->date_add full=true}</b> |</li>
				<li style="display: inline;"><b>{sizeof($messages)}</b> {if (sizeof($messages) > 1)}{l s='messages'}{else}{l s='message'}{/if} |</li>
				<li style="display: inline;"><span id="product_number" style="font-weight: bold;">{sizeof($products)}</b> {if (sizeof($products) > 1)}{l s='products'}{else}{l s='product'}{/if} |</li>
				<li style="display: inline;">{l s='Total'}: <span class="total_paid" style="font-weight: bold;">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span></li>
			</ul>
		</div>
		<div class="clear"></div>
	</fieldset>

	<div style="width: 98%">
		<!-- Left column -->
		<div style="width: 48%; float:left;">
			<!-- Change status form -->
			<form action="{$currentIndex}&viewOrder&token={$smarty.get.token}" method="post" style="text-align:center;">
				<select name="id_order_state">
				{foreach from=$states item=state}
					<option onclick="{if (!$currentState->shipped && $state['shipped'])}showWarehouseList(){else}hideWarehouseList(){/if}" value="{$state['id_order_state']}" {if $state['id_order_state'] == $currentState->id}selected="selected"{/if}>{$state['name']|stripslashes}</option>
				{/foreach}
				</select>
				<select name="id_warehouse" id="warehouse">
				{foreach from=$warehouse_list item=warehouse}
					<option value="{$warehouse['id_warehouse']}">{$warehouse['name']}</option>
				{/foreach}
				</select>
				<input type="hidden" name="id_order" value="{$order->id}" />
				<input type="submit" name="submitState" value="{l s='Add'}" class="button" />
			</form>
			<br />

			<!-- History of status -->
			<table cellspacing="0" cellpadding="0" class="table" style="width: 100%;">
			{foreach from=$history item=row key=key}
				{if ($key == 0)}
				<tr>
					<th>{dateFormat date=$row['date_add'] full=true}</th>
					<th><img src="../img/os/{$row['id_order_state']}.gif" /></th>
					<th>{$row['ostate_name']|stripslashes}</th>
					<th>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</th>
				</tr>
				{else}
				<tr class="{if ($key % 2)}alt_row{/if}">
					<td>{dateFormat date=$row['date_add'] full=true}</td>
					<td><img src="../img/os/{$row['id_order_state']}.gif" /></td>
					<td>{$row['ostate_name']|stripslashes}</td>
					<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
				</tr>
				{/if}
			{/foreach}
			</table>

			{if $customer->id}
			<!-- Customer informations -->
			<br />
			<fieldset style="width: 100%;">
				<legend><img src="../img/admin/tab-customers.gif" /> {l s='Customer information'}</legend>
				<span style="font-weight: bold; font-size: 14px;"><a href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={getAdminToken tab='AdminCustomers'}"> {$customer->firstname} {$customer->lastname}</a></span> ({l s='#'}{$customer->id})<br />
				(<a href="mailto:{$customer->email}">{$customer->email}</a>)<br /><br />
				{if ($customer->isGuest())}
					{l s='This order has been placed by a'} <b>{l s='guest'}</b>
					{if (!Customer::customerExists($customer->email))}
					<form method="POST" action="index.php?tab=AdminCustomers&id_customer={$customer->id}&token={getAdminToken tab='AdminCustomers'}">
						<input type="hidden" name="id_lang" value="{$order->id_lang}" />
						<p class="center"><input class="button" type="submit" name="submitGuestToCustomer" value="{l s='Transform to customer'}" /></p>
						{l s='This feature will generate a random password and send an e-mail to the customer'}
					</form>
					{else}
						<div><b style="color:red;">{l s='A registered customer account exists with the same email address'}</b></div>
					{/if}
				{else}
					{l s='Account registered:'} <b>{dateFormat date=$customer->date_add full=true}</b><br />
					{l s='Valid orders placed:'} <b>{$customerStats['nb_orders']}</b><br />
					{l s='Total paid since registration:'} <b>{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</b><br />
			</fieldset>
				{/if}
			{/if}

			<!-- Sources block -->
			{if (sizeof($sources))}
			<br />
			<fieldset style="width: 100%;">
				<legend><img src="../img/admin/tab-stats.gif" /> {l s='Sources'}</legend>
				<ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll; width: 360px;"{/if}>
				{foreach from=$sources item=source}
					<li>
						{dateFormat date=$source['date_add'] full=true}<br />
						<b>{l s='From:'}</b> <a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a><br />
						<b>{l s='To:'}</b> {$source['request_uri']}<br />
						{if $source['keywords']}<b>{l s='Keywords:'}</b> {$source['keywords']}<br />{/if}<br />
					</li>
				{/foreach}
				</ul>
			</fieldset>
			{/if}

			<!-- Admin order hook -->
			{if $HOOK_ADMIN_ORDER}
				{$HOOK_ADMIN_ORDER}
			{/if}
		</div>
		<!-- END Left column -->

		<!-- Right column -->
		<div style="width: 48%; float:right;">
			<!-- Documents block -->
			<fieldset style="width: 100%">
				<legend><img src="../img/admin/details.gif" /> {l s='Documents'}</legend>

				<table class="table" width="100%;" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th style="width:20%">Date</th>
							<th>Document</th>
							<th style="width:20%">Number</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$order->getDocuments() item=document}
						<tr>
							<td>{dateFormat date=$document->date_add}</td>
							<td>Invoice</td>
							<td><a href="pdf.php?pdf&id_order_invoice={$document->id}">#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang)}{'%06d'|sprintf:$document->number}</a></td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="3" class="center">
								<h3>{l s='No document is available'}</h3>
								<p><a class="button" href="{$currentIndex}&viewOrder&submitGenerateInvoice&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">{l s='Generate invoice'}</a></p>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</fieldset>
			<br />

			<!-- Payments block -->
			<fieldset style="width: 100%;">
				<legend><img src="../img/admin/details.gif" /> {l s='Payment'}</legend>

				{if !$order->valid}
				<form method="post" action="{$currentIndex}&viewOrder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
					<p class="warn">{l s='Don\'t forget to update your conversion rate before make this change.'}</p>
					<label>{l s='Change order\'s currency to:'}</label>
					<select name="new_currency">
						{foreach from=$currencies item=currency_change}
							{if $currency_change['id_currency'] != $order->id_currency}
							<option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
							{/if}
						{/foreach}
					</select>
					<input type="submit" class="button" name="submitChangeCurrency" value="{l s='Change'}" />
				</form>
				<hr />
				{/if}

				<p class="error" style="{if $order->total_paid_tax_incl == $total_paid}display: none;{/if}">
					{l s='Warning:'} {displayPrice price=$total_paid currency=$currency->id}
					{l s='paid instead of'} <span class="total_paid">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
				</p>

				<form method="post" action="{$currentIndex}&viewOrder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&token={$smarty.get.token|escape:'htmlall':'UTF-8'}">
					<table class="table" width="100%" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th style="width:28%">{l s='Date'}</th>
								<th>{l s='Payment method'}</th>
								<th style="width:15%">{l s='Transaction ID'}</th>
								<th style="width:25%">{l s='Amount'}</th>
								<th style="width:10%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$order->getOrderPaymentCollection() item=payment}
							<tr>
								<td>{dateFormat date=$payment->date_add full=true}</td>
								<td>{$payment->payment_method}</td>
								<td>{$payment->transaction_id}</td>
								<td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
								<td></td>
							</tr>
							{/foreach}
							<tr>
								<td><input type="text" name="payment_date" class="datepicker" size="17" value="{dateFormat date=date('Y-m-d H:i:s') full=true}" /></td>
								<td>
									<select name="payment_method">
									{foreach from=PaymentModule::getInstalledPaymentModules() item=payment_method}
										{assign var=payment_name value=Module::getInstanceByName($payment_method.name)->displayName}
										<option value="{$payment_name}">{$payment_name}</option>
									{/foreach}
									</select>
								</td>
								<td>
									<input type="text" name="payment_transaction_id" value="" />
								</td>
								<td>
									<input type="text" name="payment_amount" size="5" value="" />
									<select name="payment_currency">
									{foreach from=$currencies item=current_currency}
										<option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
									{/foreach}
									</select>
								</td>
								<td><input class="button" type="submit" name="submitAddPayment" value="Add" /></td>
							</tr>
						</tbody>
					</table>
				</form>
			</fieldset>
			<br />

			<!-- Shipping block -->
			<fieldset style="width: 100%">
				<legend><img src="../img/admin/delivery.gif" /> {l s='Shipping'}</legend>

				<div class="clear" style="float: left; margin-right: 10px;">
					<span class="bold">{l s='Recycled package:'}</span>
					{if $order->recyclable}
					<img src="../img/admin/enabled.gif" />
					{else}
					<img src="../img/admin/disabled.gif" />
					{/if}
				</div>
				<div style="float: left;">
					<span class="bold">{l s='Gift wrapping:'}</span>
					{if $order->gift}
					<img src="../img/admin/enabled.gif" />
					</div>
					<div style="clear: left; margin: 0px 42px 0px 42px; padding-top: 2px;">
						{if $order->gift_message}
						<div style="border: 1px dashed #999; padding: 5px; margin-top: 8px;"><b>{l s='Message:'}</b><br />{$order->gift_message|nl2br}</div>
						{/if}
					{else}
					<img src="../img/admin/disabled.gif" />
					{/if}
				</div>
				<div class="clear" style="margin-bottom: 10px;"></div>

				<table class="table" width="100%" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th style="width:30%">{l s='Date:'}</th>
							<th>{l s='Type'}</th>
							<th style="width:20%">{l s='Carrier'}</th>
							<th>{l s='Weight'}</th>
							<th style="width:15%">{l s='Shipping cost'}</th>
							<th style="width:30%">{l s='Tracking number'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$order->getShipping() item=line}
						<tr>
							<td>{$line.date_add}</td>
							<td>{$line.type}</td>
							<td>{$line.state_name}</td>
							<td>{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</td>
							<td>{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}{else}{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}{/if}</td>
							<td>
								<span id="shipping_number_show">{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}</span>
								{if $line.can_edit}
								<form style="display: inline;" method="POST" action="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'htmlall':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'htmlall':'UTF-8'}{else}0{/if}">
									<span class="shipping_number_edit" style="display:none;">
										<input type="text" name="tracking_number" value="{$line.tracking_number}" />
										<input type="submit" class="button" name="submitShippingNumber" value="{l s='Update'}" />
									</span>
									<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="{l s='Edit'}" /></a>
									<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel'}" /></a>
								</form>
								{/if}
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>

				{if $carrierModuleCall}
					{$carrierModuleCall}
				{/if}
			</fieldset>
			<br />

			<!-- Return block -->
			<fieldset style="width: 100%">
				<legend><img src="../img/admin/delivery.gif" /> {l s='Merchandise returns'}</legend>

				{if $order->getReturn()|count > 0}
				<table class="table" width="100%" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th style="width:30%">Date</th>
							<th>Type</th>
							<th style="width:20%">Carrier</th>
							<th style="width:30%">Tracking number</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$order->getReturn() item=line}
						<tr>
							<td>{$line.date_add}</td>
							<td>{$line.type}</td>
							<td>{$line.state_name}</td>
							<td>
								<span id="shipping_number_show">{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}</span>
								{if $line.can_edit}
								<form style="display: inline;" method="POST" action="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}&id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|escape:'htmlall':'UTF-8'}{else}0{/if}&id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'htmlall':'UTF-8'}{else}0{/if}">
									<span class="shipping_number_edit" style="display:none;">
										<input type="text" name="tracking_number" value="{$line.tracking_number}" />
										<input type="submit" class="button" name="submitShippingNumber" value="{l s='Update'}" />
									</span>
									<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="{l s='Edit'}" /></a>
									<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel'}" /></a>
								</form>
								{/if}
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
				{else}
				{l s='No merchandise returns yet.'}
				{/if}

				{if $carrierModuleCall}
					{$carrierModuleCall}
				{/if}
			</fieldset>
		</div>
		<!-- END Right column -->
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<div style="width: 98%">
		<!-- Addresses -->
		<div style="width: 48%; float:left;"></contact>
			<!-- Shipping address -->
			<fieldset style="width: 100%;">
				<legend><img src="../img/admin/delivery.gif" alt="{l s='Shipping address'}" />{l s='Shipping address'}</legend>

				{if $can_edit}
				<form method="POST" action="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}">
					<div style="margin-bottom:5px;border-bottom:1px solid black;">
						<p style="text-align:center;">
							<select name="id_address">
								{foreach from=$customer_addresses item=address}
								<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_delivery} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
								{/foreach}
							</select>
							<input class="button" type="submit" name="submitAddressShipping" value="{l s='Change'}" />
						</p>
					</div>
				</form>
				{/if}

				<div style="float: right">
					<a href="?tab=AdminAddresses&id_address={$addresses.delivery->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=1{/if}&token={getAdminToken tab='AdminAddresses'}&back={$smarty.server.REQUEST_URI}"><img src="../img/admin/edit.gif" /></a>
					<a href="http://maps.google.com/maps?f=q&hl={$iso_code_lang}&geocode=&q={$addresses.delivery->address1} {$addresses.delivery->postcode} {$addresses.delivery->city} {if ($addresses.delivery->id_state)} {$addresses.deliveryState->name}{/if}" target="_blank"><img src="../img/admin/google.gif" alt="" class="middle" /></a>
				</div>

				{displayAddressDetail address=$addresses.delivery newLine='<br />'}
				{if $addresses.delivery->other}<hr />{$addresses.delivery->other}<br />{/if}
			</fieldset>
		</div>

		<div style="width: 48%; float:right;"></contact>
			<!-- Invoice address -->
			<fieldset style="width: 100%;">
				<legend><img src="../img/admin/invoice.gif" alt="{l s='Invoice address'}" />{l s='Invoice address'}</legend>

				{if $can_edit}
				<form method="POST" action="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$smarty.get.id_order|escape:'htmlall':'UTF-8'}">
					<div style="margin-bottom:5px;border-bottom:1px solid black;">
						<p style="text-align:center;">
							<select name="id_address">
								{foreach from=$customer_addresses item=address}
								<option value="{$address['id_address']}"{if $address['id_address'] == $order->id_address_invoice} selected="selected"{/if}>{$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']}{if !empty($address['state'])} {$address['state']}{/if}, {$address['country']}</option>
								{/foreach}
							</select>
							<input class="button" type="submit" name="submitAddressInvoice" value="{l s='Change'}" />
						</p>
					</div>
				</form>
				{/if}

				<div style="float: right">
					<a href="?tab=AdminAddresses&id_address={$addresses.invoice->id}&addaddress&realedit=1&id_order={$order->id}{if ($addresses.delivery->id == $addresses.invoice->id)}&address_type=2{/if}&back={$smarty.server.REQUEST_URI}&token={getAdminToken tab='AdminAddresses'}"><img src="../img/admin/edit.gif" /></a>
				</div>

				{displayAddressDetail address=$addresses.invoice newLine='<br />'}
				{if $addresses.invoice->other}<hr />{$addresses.invoice->other}<br />{/if}
			</fieldset>
		</div>
		<div class="clear" style="margin-bottom: 10px;"></div>
	</div>

	<form style="width: 98%" action="{$currentIndex}&vieworder&token={$smarty.get.token}" method="post" onsubmit="return orderDeleteProduct('{l s='Cannot return this product'}', '{l s='Quantity to cancel is greater than quantity available'}');">
		<input type="hidden" name="id_order" value="{$order->id}" />
		<fieldset style="width: 100%; ">
			<legend><img src="../img/admin/cart.gif" alt="{l s='Products'}" />{l s='Products'}</legend>
			<div style="float:left;width: 100%;">
				{if $can_edit}
				<div style="float: left;"><a href="#" class="add_product"><img src="../img/admin/add.gif" alt="{l s='Add a product'}" /> {l s='Add a product'}</a></div>
				<div style="float: right; margin-right: 10px" id="refundForm">
				<!--
					<a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Proceed a standard refund'}" /> {l s='Proceed a standard refund'}</a>
					<a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Proceed a partial refund'}" /> {l s='Proceed a partial refund'}</a>
				-->
				</div>
				<br clear="left" /><br />
				{/if}
				<table style="width: 100%;" cellspacing="0" cellpadding="0" class="table" id="orderProducts">
					<tr>
						<th align="center" style="width: 7%">&nbsp;</th>
						<th>{l s='Product'}</th>
						<th style="width: 15%; text-align: center">{l s='UP'} <sup>*</sup></th>
						<th style="width: 4%; text-align: center">{l s='Qty'}</th>
						{if ($order->hasBeenPaid())}<th style="width: 3%; text-align: center">{l s='Refunded'}</th>{/if}
						{if ($order->hasBeenDelivered())}<th style="width: 3%; text-align: center">{l s='Returned'}</th>{/if}
						<th style="width: 3%; text-align: center">{l s='Stock'}</th>
						<th style="width: 10%; text-align: center">{l s='Total'} <sup>*</sup></th>
						<th colspan="2" style="display: none;" class="add_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="edit_product_fields">&nbsp;</th>
						<th colspan="2" style="display: none;" class="standard_refund_fields"><img src="../img/admin/delete.gif" alt="{l s='Products'}" />
							{if ($order->hasBeenDelivered())}
								{l s='Return'}
							{elseif ($order->hasBeenPaid())}
								{l s='Refund'}
							{else}
								{l s='Cancel'}
							{/if}
						</th>
						<th style="width: 8%;text-align:right;display:none" class="partial_refund_fields">
							{l s='Partial refund'}
						</th>
						<th style="width: 8%;text-align:center;">
							{l s='Action'}
						</th>
					</tr>

					{foreach from=$products item=product key=k}
						{* Include customized datas partial *}
						{include file='orders/_customized_data.tpl'}

						{* Include product line partial *}
						{include file='orders/_product_line.tpl'}
					{/foreach}
					{if $can_edit}
						{include file='orders/_new_product.tpl'}
					{/if}
				</table>

				<div style="float:left; width:280px; margin-top:15px;">
					<sup>*</sup> {l s='According to the group of this customer, prices are printed:'}
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						{l s='tax excluded.'}
					{else}
						{l s='tax included.'}
					{/if}

					{if Configuration::get('PS_ORDER_RETURN')}
						<br /><br />{l s='Merchandise returns are disabled'}
					{/if}
				</div>

				<div style="float:right;">
					<table class="table" width="300px;" cellspacing="0" cellpadding="0">
						<tr id="total_products">
							<td width="150px;"><b>{l s='Products'}</b></td>
							<td align="right">{displayPrice price=$order->total_products_wt currency=$currency->id}</td>
							<td class="partial_refund_fields" style="display:none;background-color:rgb(232, 237, 194);">&nbsp;</td>
						</tr>
						<tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
							<td><b>{l s='Discounts'}</b></td>
							<td align="right">-{displayPrice price=$order->total_discounts_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields" style="display:none;background-color:rgb(232, 237, 194);">&nbsp;</td>
						</tr>
						<tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
							<td><b>{l s='Wrapping'}</b></td>
							<td align="right">{displayPrice price=$order->total_wrapping_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields" style="display:none;background-color:rgb(232, 237, 194);">&nbsp;</td>
						</tr>
						<tr id="total_shipping">
							<td><b>{l s='Shipping'}</b></td>
							<td align="right">{displayPrice price=$order->total_shipping_tax_incl currency=$currency->id}</td>
							<td class="partial_refund_fields" style="display:none;background-color:rgb(232, 237, 194);"><input type="text" size="3" name="partialRefundShippingCost" /> &euro;</td>
						</tr>
						<tr style="font-size: 20px" id="total_order">
							<td style="font-size: 20px">{l s='Total'}</td>
							<td style="font-size: 20px" align="right">
								{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}
							</td>
							<td class="partial_refund_fields" style="display:none;background-color:rgb(232, 237, 194);">&nbsp;</td>
						</tr>
					</table>
				</div>
				<div class="clear"></div>

				{if (sizeof($discounts))}
				<div style="float:right; width:280px; margin-top:15px;">
					<table cellspacing="0" cellpadding="0" class="table" style="width:100%;">
						<tr>
							<th><img src="../img/admin/coupon.gif" alt="{l s='Discounts'}" />{l s='Discount name'}</th>
							<th align="center" style="width: 100px">{l s='Value'}</th>
						</tr>
						{foreach from=$discounts item=discount}
						<tr>
							<td>{$discount['name']}</td>
							<td align="center">
							{if $discount['value'] != 0.00}
								-
							{/if}
							{displayPrice price=$discount['value'] currency=$currency->id}
							</td>
						</tr>
						{/foreach}
					</table>
				</div>
				{/if}
			</div>

			<div style="clear:both; height:15px;">&nbsp;</div>
			<div style="float: right; width: 160px; display: none;" class="standard_refund_fields">
			{if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
				<input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" class="button" />&nbsp;<label for="reinjectQuantities" style="float:none; font-weight:normal;">{l s='Re-stock products'}</label><br />
			{/if}
			{if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
				<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" class="button" onclick="toogleShippingCost(this)" />&nbsp;<label for="generateCreditSlip" style="float:none; font-weight:normal;">{l s='Generate a credit slip'}</label><br />
				<input type="checkbox" id="generateDiscount" name="generateDiscount" class="button" onclick="toogleShippingCost(this)" />&nbsp;<label for="generateDiscount" style="float:none; font-weight:normal;">{l s='Generate a voucher'}</label><br />
				<span id="spanShippingBack" style="display:none;"><input type="checkbox" id="shippingBack" name="shippingBack" class="button" />&nbsp;<label for="shippingBack" style="float:none; font-weight:normal;">{l s='Repay shipping costs'}</label><br /></span>
			{/if}
			{if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
				<div style="text-align:center; margin-top:5px;">
					<input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="button" style="margin-top:8px;" />
				</div>
			{/if}
			</div>
			<div style="float: right; width: 160px; display: none;" class="partial_refund_fields">
				<div style="text-align:center; margin-top:5px;">
					<input type="submit" name="partialRefund" value="{l s='Partial refund'}" class="button" style="margin-top:8px;" />
				</div>
			</div>
		</fieldset>
	</form>
	<div class="clear" style="height:20px;">&nbsp;</div>

	<div style="float: left">
		<form action="{$smarty.server.REQUEST_URI}&token={$smarty.get.token}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?'}');">
		<fieldset style="width: 400px;">
			<legend style="cursor: pointer;" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><img src="../img/admin/email_edit.gif" /> {l s='New message'}</legend>
			<div id="message_m" style="display: {if Tools::getValue('message')}none{else}block{/if}; overflow: auto; width: 400px;">
				<a href="#" onclick="$('#message').slideToggle();$('#message_m').slideToggle();return false"><b>{l s='Click here'}</b> {l s='to add a comment or send a message to the customer'}</a>
			</div>
			<div id="message" style="display: {if Tools::getValue('message')}block{else}none{/if}">
						<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
							<option value="0" selected="selected">-- {l s='Choose a standard message'} --</option>
			{foreach from=$orderMessages item=orderMessage}
				<option value="{$orderMessage['message']|escape:'htmlall':'UTF-8'}">{$orderMessage['name']}</option>
			{/foreach}
						</select><br /><br />
						<b>{l s='Display to consumer?'}</b>
						<input type="radio" name="visibility" id="visibility" value="0" /> {l s='Yes'}
						<input type="radio" name="visibility" value="1" checked="checked" /> {l s='No'}
						<p id="nbchars" style="display:inline;font-size:10px;color:#666;"></p><br /><br />
				<textarea id="txt_msg" name="message" cols="50" rows="8" onKeyUp="var length = document.getElementById('txt_msg').value.length; if (length > 600) length = '600+'; document.getElementById('nbchars').innerHTML = '{l s='600 chars max'} (' + length + ')';">{Tools::getValue('message')|escape:'htmlall':'UTF-8'}</textarea><br /><br />
				<input type="hidden" name="id_order" value="{$order->id}" />
				<input type="hidden" name="id_customer" value="{$order->id_customer}" />
				<input type="submit" class="button" name="submitMessage" value="{l s='Send'}" />
			</div>
		</fieldset>
		</form>

	{if (sizeof($messages))}
		<br />
		<fieldset style="width: 400px;">
		<legend><img src="../img/admin/email.gif" /> {l s='Messages'}</legend>
		{foreach from=$messages item=message}
			<div style="overflow:auto; width:400px;" {if $message['is_new_for_me']}class="new_message"{/if}>
			{if ($message['is_new_for_me'])}
				<a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.get.REQUEST_URI}&token={$smarty.get.token}&messageReaded={$message['id_message']}"><img src="../img/admin/enabled.gif" alt="" /></a>
			{/if}
			{l s='At'} <i>{dateFormat date=$message['date_add']}
			</i> {l s='from'} <b>{if ($message['elastname'])}{$message['efirstname']} {$message['elastname']}{else}{$message['cfirstname']} {$message['clastname']}{/if}</b>
			{if ($message['private'] == 1)}<span style="color:red; font-weight:bold;">{l s='Private:'}</span>{/if}
			<p>{$message['message']|nl2br}</p>
			</div>
			<br />
		{/foreach}
		<p class="info">{l s='When you read a message, please click on the green check.'}</p>
		</fieldset>
	{/if}
	</div>

	<div style="float: left; margin-left: 40px">
		<fieldset style="width: 400px;">
			<legend><img src="../img/admin/slip.gif" alt="{l s='Credit slip'}" />{l s='Credit slip'}</legend>
	{if (!sizeof($slips))}
		{l s='No slip for this order.'}
	{else}
		{foreach from=$slips item=slip}
			({dateFormat date=$slip['date_upd']}) : <b><a href="pdf.php?id_order_slip={$slip['id_order_slip']}">{l s='#'}{'%06d'|sprintf:$slip['id_order_slip']}</a></b><br />
		{/foreach}
	{/if}
		</fieldset>
	</div>
	<div class="clear">&nbsp;</div>
	<br /><br /><a href="{$currentIndex}&token={$smarty.get.token}"><img src="../img/admin/arrow2.gif" /> {l s='Back to list'}</a><br />




	<!--  <br />
		<fieldset>
			<legend>
				<img widtdh="20" height="16" src="../img/admin/order-detail-icone.png" />
				{l s='Payment detail'}
			</legend>
			<ul style="list-style:none; display:block; line-height: 1.5em; padding 4px 0;">
				<li style="margin-bottom:10px;">
					<form method="post" action="{$smarty.server.REQUEST_URI}">
						<font style="font-weight:bolder;">{l s='Set the transaction id:'}</font>
						<input type="text" name="transaction_id" value="{if $paymentCCDetails}{$paymentCCDetails['transaction_id']}{/if}" />
						<input type="hidden" name="id_payment_cc" value="{if $paymentCCDetails}{$paymentCCDetails['id_payment_cc']}{/if}" />
						<input class="button" type="submit" name="setTransactionId" value="{l s='Update'}"/>
					</form>
				</li>
				{if $paymentCCDetails}
					{if $paymentCCDetails['card_holder'] != ''}
						<li>
							<font style="font-weight:bolder;">{l s='Card Holder:'} </font>
							{$paymentCCDetails['card_holder']}
						</li>
					{/if}
					{if $paymentCCDetails['card_number'] != ''}
						<li>
							<font style="font-weight:bolder;">{l s='Card Number:'} </font>
							****{$paymentCCDetails['card_number']|substr:-4}
						</li>
					{/if}
					{if $paymentCCDetails['card_brand'] != ''}
						<li>
							<font style="font-weight:bolder;">{l s='Card Brand:'} </font>
								{$paymentCCDetails['card_brand']}
						</li>
					{/if}
					{if $paymentCCDetails['card_expiration'] != ''}
						<li>
							<font style="font-weight:bolder;">{l s='Card expiration:'} </font>
							{$paymentCCDetails['card_expiration']}
						</li>
					{/if}
				{/if}
			</ul>
		</fieldset> -->

		<!-- <br />
		<fieldset style="width: 400px">
			<legend><img src="../img/admin/details.gif" /> {l s='Order details'}</legend>
			{if (Shop::isFeatureActive())}
			<label>{l s='Shop:'}</label>
			<div style="margin: 2px 0 1em 190px;">{Shop::getInstance($order->id_shop)->name}</div>
			{/if}

			<label>{l s='Original cart:'}</label>
			<div style="margin: 2px 0 1em 190px;"><a href="?tab=AdminCarts&id_cart={$cart->id}&viewcart&token={getAdminToken tab='AdminCarts'}">{l s='Cart #'}{"%06d"|sprintf:$cart->id}</a></div>
			<label>{l s='Payment mode:'}</label>
			<div style="margin: 2px 0 1em 190px; padding: 2px 0px;">{substr($order->payment, 0, 32)}{if $order->module} ({$order->module}){/if}</div>
			<div style="margin: 2px 0 1em 50px;">
				<table class="table" width="300px;" cellspacing="0" cellpadding="0">
					<tr>
						<td width="150px;">{l s='Products'}</td>
						<td align="right">{displayPrice price=$order->getTotalProductsWithTaxes() currency=$currency->id}</td>
					</tr>
					{if $order->total_discounts > 0}
					<tr>
						<td>{l s='Discounts'}</td>
						<td align="right">-{displayPrice price=$order->total_discounts currency=$currency->id}</td>
					</tr>
					{/if}
					{if $order->total_wrapping > 0}
					<tr>
						<td>{l s='Wrapping'}</td>
						<td align="right">{displayPrice price=$order->total_wrapping currency=$currency->id}</td>
					</tr>
					{/if}
					<tr>
						<td>{l s='Shipping'}</td>
						<td align="right">{displayPrice price=$order->total_shipping currency=$currency->id}</td>
					</tr>
					<tr style="font-size: 20px">
						<td>{l s='Total'}</td>
						<td align="right">
							{displayPrice price=$order->total_paid currency=$currency->id}
							{if $order->total_paid != $order->total_paid_real}
								<br />
								<font color="red">{l s='Paid:'} {displayPrice price=$order->total_paid_real currency=$currency->id}</font>
							{/if}
						</td>
					</tr>
				</table>
			</div>
			<div style="float: left; margin-right: 10px; margin-left: 42px;">
				<span class="bold">{l s='Recycled package:'}</span>
				{if $order->recyclable}
				<img src="../img/admin/enabled.gif" />
				{else}
				<img src="../img/admin/disabled.gif" />
				{/if}
			</div>
			<div style="float: left; margin-right: 10px;">
				<span class="bold">{l s='Gift wrapping:'}</span>
				{if $order->gift}
				<img src="../img/admin/enabled.gif" />
				</div>
				<div style="clear: left; margin: 0px 42px 0px 42px; padding-top: 2px;">
					{if $order->gift_message}
					<div style="border: 1px dashed #999; padding: 5px; margin-top: 8px;"><b>{l s='Message:'}</b><br />{$order->gift_message|nl2br}</div>
					{/if}
				{else}
				<img src="../img/admin/disabled.gif" />
				{/if}
			</div>
			</fieldset> -->
{/block}
