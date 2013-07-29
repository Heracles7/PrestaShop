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

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}

	<div id="CustomerThreadContacts" class="row">

		{assign var=nb_categories value=count($categories)}
		
		{foreach $categories as $key => $val}

			{assign var=total_thread value=0}
			{assign var=id_customer_thread value=0}

			{foreach $contacts as $tmp => $tmp2}
				{if $val.id_contact == $tmp2.id_contact}
					{assign var=total_thread value=$tmp2.total}
					{assign var=id_customer_thread value=$tmp2.id_customer_thread}
				{/if}
			{/foreach}

			<div class="col-lg-3">
				<h3>{$val.name}</h3>
				{if $nb_categories < 6}
					<p>{$val.description}</p>
				{/if}
				{if $total_thread == 0}
					<span class="message-mail">{l s='No new messages'}</span>
				{else}
					<a href="{$currentIndex}&token={$token}&id_customer_thread={$id_customer_thread}&viewcustomer_thread" class="button">
						{$total_thread} 
						{if $total_thread > 1}{l s='New messages'}{else}{l s='New message'}{/if}
					</a>
				{/if}
			</div>
		{/foreach}

		<div id="MeaningStatus" class="col-lg-3">
			<h3>{l s='Meaning of status'}</h3>
			<ul>
				<li class="label label-success">{l s='Open'}</li>
				<li class="label label-danger">{l s='Closed'}</li>
				<li class="label label-warning">{l s='Pending 1'}</li>
				<li class="label label-warning">{l s='Pending 2'}</li>
			</ul>
		</div>
	
		<div id="CustomerService" class="col-lg-3">
			<h3>{l s='Statistics'}</h3>
			<ul>
				{assign var=count value=0}
				{foreach $params as $key => $val}
					{assign var=count value=$count+1}
					<li>{$key} <span class="badge">{$val}</span></li>
				{/foreach}
			</ul>
		</div>
	</div>
	<hr/>

{/block}