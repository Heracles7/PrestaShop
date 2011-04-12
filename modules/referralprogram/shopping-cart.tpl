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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE ReferralProgram -->
<p id="referralprogram">
	<img src="{$module_template_dir}referralprogram.gif" alt="{l s='Referral program' mod='referralprogram'}" class="icon" />
	{l s='You have earned a voucher worth' mod='referralprogram'} <span class="bold">{$discount_display}</span> {l s='thanks to your sponsor!' mod='referralprogram'}
	{l s='Enter voucher name' mod='referralprogram'}&nbsp;<span class="bold">{$discount->name}</span> {l s='to receive the reduction on this order.' mod='referralprogram'}
	<a href="{$module_template_dir}referralprogram-program.php" title="{l s='Referral program' mod='referralprogram'}">{l s='View your referral program.' mod='referralprogram'}</a>
</p>
<br />
<!-- END : MODULE ReferralProgram -->