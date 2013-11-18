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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($nbComments) && $nbComments > 0}
    <div class="comments_note">	
        <div class="star_content clearfix">
        {section name="i" start=0 loop=5 step=1}
            {if $averageTotal le $smarty.section.i.index}
                <div class="star"></div>
            {else}
                <div class="star star_on"></div>
            {/if}
        {/section}
        </div>
        <span class="nb-comments">{l s='%s Review(s)'|sprintf:$nbComments mod='productcomments'}&nbsp</span>
    </div>
{/if}