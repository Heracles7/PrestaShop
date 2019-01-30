{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<script>
	var current_id_tab = {$current_id_tab|intval};
	var current_level_percent = {$current_level_percent|intval};
	var current_level = {$current_level|intval};
	var gamification_level = '{l s='Level' mod='gamification' js=1}';
</script>
<style>
	{literal}.gamification_progress-label {top:-1px!important;-moz-border-radius: 15px;-o-border-radius: 15px;-webkit-border-radius: 15px;border-radius: 15px;height:19px;background-image: url(../modules/gamification/views/img/gamification-bar-bg.png) ;width: {/literal}{$current_level_percent}{literal}%}{/literal}
</style>
<div id="gamification_notif" class="notifs">
		{if $notification}
		<span id="gamification_notif_number_wrapper" class="number_wrapper" style="display: inline;">
			<span id="gamification_notif_value">{$notification|intval}</span>
		</span>
		{/if}
	<div id="gamification_notif_wrapper" class="notifs_wrapper" style="width:340px">
		<div id="gamification_top">
			<h3>{l s='Your Merchant Expertise' mod='gamification'}</h3>
		</div>
		<span style="font-size: 15px;color: #585A69;text-shadow: 0 1px 0 #fff;">{l s='Level' mod='gamification'} {$current_level|intval} : {$current_level_percent|intval} %</span>
		<div id="gamification_progressbar">
			<span class="gamification_progress-label"></span>
		</div>
		<div id="gamification_badges_container">
			<ul id="gamification_badges_list" style="{if $badges_to_display|count <= 2} height:140px;{/if}">
				{foreach from=$badges_to_display name=badge_list item=badge}
				{if $badge->id}
					<li class="{if $badge->validated} unlocked {else} locked {/if}" style="float:left;">
						<span class="{if $badge->validated} unlocked_img {else} locked_img {/if}"></span>
						<div class="gamification_badges_title"><span>{if $badge->validated} {l s='Last badge :' mod='gamification'} {else} {l s='Next badge :' mod='gamification'} {/if}</span></div>
						<div class="gamification_badges_img"><img src="{$badge->getBadgeImgUrl()}"></div>
						<div class="gamification_badges_name">{$badge->name|escape:html:'UTF-8'}</div>
					</li>
				{else}
					<li style="height:130px"></li>
				{/if}
				{if $smarty.foreach.badge_list.iteration is not odd && $badges_to_display|count > 2}
						<div class="clear">&nbsp;</div>
					{/if}
				{/foreach}
			</ul>
		</div>
		<a id="gamification_see_more" href="{$link->getAdminLink('AdminGamification')}">{l s='View my complete profile' mod='gamification'}</a>
	</div>
</div>
