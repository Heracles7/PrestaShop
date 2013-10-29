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

<script>
	var dashboard_ajax_url = '{$link->getAdminLink('AdminDashboard')}';
	var adminstats_ajax_url = '{$link->getAdminLink('AdminStats')}';
	var no_results_translation = '{l s='No result' js='1'}';
	var dashboard_use_push = '{$dashboard_use_push|intval}';
	var read_more = '{l s='Read more' js='1'}';
</script>

<div id="dashboard">
	<div class="row">
		<div class="col-lg-12">
			{if $warning}
				<div class="alert alert-warning">{$warning}</div>
			{/if}
			<div id="calendar" class="panel">
				<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="form-inline">
					<div class="row">
						<div class="col-lg-6">
							<div class="btn-group">
								<button type="button" name="submitDateDay" class="btn btn-default submitDateDay">
									{l s='Day'}
								</button>
								<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth">
									{l s='Month'}
								</button>
								<button type="button" name="submitDateYear" class="btn btn-default submitDateYear">
									{l s='Year'}
								</button>
								<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev">
									{l s='Day'}-1
								</button>
								<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev">
									{l s='Month'}-1
								</button>
								<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev">
									{l s='Year'}-1
								</button>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="input-group fixed-width-lg hide">
									<label class="input-group-addon">{l s='From'}</label>
									<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<div class="input-group fixed-width-lg hide">
									<label class="input-group-addon">{l s='To'}</label>
									<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<button id="datepickerExpand" class="btn btn-default" type="button">
									<strong class="text-info">{$datepickerFrom|escape}</strong>
									-
									<strong class="text-info">{$datepickerTo|escape}</strong>
									- 13 Days
									<i class="icon-calendar-empty"></i>
									<i class="icon-caret-down"></i>
								</button>
							</div>
						</div>
					</div>
					{$calendar}
				</form>	
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3" id="hookDashboardZoneOne">
			{$hookDashboardZoneOne}
		</div>
		<div class="col-lg-7" id="hookDashboardZoneTwo">
			{$hookDashboardZoneTwo}
			<div id="dashaddons">
				<a href="http://addons.prestashop.com/208-dashboards?utm_source=backoffice_dashboard" target="_blank">
					<i class="icon-plus"></i> {l s='Add more graph and data'}
				</a>
			</div>
		</div>
		<div class="col-lg-2">
			
			<section class="dash_news panel">
				<h3><i class="icon-rss"></i> PrestaShop News</h3>
			</section>

			<section id="dash_version" class="visible-lg">
				<iframe frameborder="no" scrolling="no" allowtransparency="true" src="{$new_version_url}"></iframe>
			</section>

			<section class="dash_links panel">
				<h3><i class="icon-link"></i> {l s="Useful Links"}</h3>
					<dl>
						<dt>{l s="Discover the official documentation"}</dt>
						<dd><a href="http://doc.prestashop.com/display/PS16?utm_source=backoffice_dashboard" target="_blank">{l s="Go to doc.prestashop.com"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Use the forum & discover a great community"}</dt>
						<dd><a href="http://www.prestashop.com/forums?utm_source=backoffice_dashboard" target="_blank">{l s="Go to forums.prestashop.com"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Enhance your Shop with new templates & modules"}</dt>
						<dd><a href="http://addons.prestashop.com?utm_source=backoffice_dashboard" target="_blank">{l s="Go to addons.prestashop.com"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Report issues in the Bug Tracker"}</dt>
						<dd><a href="http://forge.prestashop.com?utm_source=backoffice_dashboard" target="_blank">{l s="Go to forge.prestashop.com"}</a></dd>
					</dl>
					<dl>
						<dt>{l s="Contact Us"}</dt>
						<dd><a href="http://www.prestashop.com/en/contact-us?utm_source=backoffice_dashboard" target="_blank">{l s="Go to prestashop.com"}</a></dd>
					</dl>
			</section>
		</div>
	</div>
</div>