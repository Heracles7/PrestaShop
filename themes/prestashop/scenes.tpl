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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if scenes}
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/jquery.cluetip.js"></script>
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/jquery.scrollTo-1.4.2-min.js"></script>
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/jquery.serialScroll-1.2.2-min.js"></script>
<script type="text/javascript">// <![CDATA[
i18n_scene_close = '{l s='Close'}';
$(function () {ldelim}
	li_width = parseInt({$thumbSceneImageType.width} + 10);
{rdelim});
//]]></script>
<script type="text/javascript" src="{$js_dir}scenes.js"></script>
<div id="scenes">
	<div>
		{foreach from=$scenes key='scene_key' item='scene' name='scenes'}
		<div class="screen_scene" id="screen_scene_{$scene->id}" style="background:transparent url({$base_dir}img/scenes/{$scene->id}-large_scene.jpg); height:{$largeSceneImageType.height}px; width:{$largeSceneImageType.width}px; {if !$smarty.foreach.scenes.first} display:none;{/if}">
			{foreach from=$scene->products key='product_key' item='product'}
			{assign var=imageIds value="`$product.id_product`-`$product.id_image`"}
				<a href="{$product.link|escape:'htmlall':'UTF-8'}" accesskey="#scene_products_cluetip_{$scene_key}_{$product_key}_{$product.id_product}" class="cluetip" style="width:{$product.zone_width}px; height:{$product.zone_height}px; margin-left:{$product.x_axis}px ;margin-top:{$product.y_axis}px;">
					<span style="margin-top:{math equation='a/2 -10' a=$product.zone_height}px; margin-left:{math equation='a/2 -10' a=$product.zone_width}px;">&nbsp;</span>
				</a>
				<div id="scene_products_cluetip_{$scene_key}_{$product_key}_{$product.id_product}" style="display:none;">
					<h4><span class="product_name">{$product.details->name}</span>{if isset($product.details->new) AND $product.details->new}<span class="new">{l s='New'}</span>{/if}</h4>
					{if !$PS_CATALOG_MODE AND $product.details->show_price}
					<div class="prices">
						<p class="price">{if $priceDisplay}{convertPrice price=$product.details->getPrice(false, $product.details->getDefaultAttribute($product.id_product))}{else}{convertPrice price=$product.details->getPrice(true, $product.details->getDefaultAttribute($product.id_product))}{/if}</p>
							{if $product.details->on_sale}
							<span class="on_sale">{l s='On sale!'}</span>
						{elseif isset($product.reduction) && $product.reduction}
							<span class="discount">{l s='Reduced price!'}</span>
						{/if}
					</div>
					{/if}
					<div class="clear">
						<a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.details->name|escape:'htmlall':'UTF-8'}">
							<img src="{$link->getImageLink($product.id_product, $imageIds, 'medium')}" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" />
						</a>
						<p class="description">{$product.details->description_short|strip_tags|truncate:170:'...'}</p>
					</div>
				</div>
			{/foreach}
		</div>
		{/foreach}
	</div>
	{if isset($scenes.1)}
	<div class="thumbs_banner" style="height:{$thumbSceneImageType.height}px;">
		<span class="space-keeper">
			<a class="prev" href="#" style="height:{math equation='a+2' a=$thumbSceneImageType.height}px;" onclick="{ldelim}next_scene_is_at_right = false; $(this).parent().next().trigger('stop').trigger('prev'); return false;{rdelim}">&nbsp;</a>
		</span>
		<div id="scenes_list">
			<ul style="width:{math equation='(a*b + (a-1)*10)' a=$scenes|@count b=$thumbSceneImageType.width}px; height:{$thumbSceneImageType.height}px;">
			{foreach from=$scenes item='scene' name='scenes_list'}
				<li id="scene_thumb_{$scene->id}" style="{if !$smarty.foreach.scenes_list.last} padding-right:10px;{/if}">
					<a style="width:{$thumbSceneImageType.width}px; height:{$thumbSceneImageType.height}px" title="{$scene->name|escape:'htmlall':'UTF-8'}" href="#" rel="{$scene->id}" onclick="{ldelim}loadScene({$scene->id});return false;{rdelim}">
						<img alt="{$scene->name|escape:'htmlall':'UTF-8'}" src="{$content_dir}img/scenes/thumbs/{$scene->id}-thumb_scene.jpg" width="{$thumbSceneSize.width}" height="{$thumbSceneSize.height}" />
					</a>
				</li>
		 	{/foreach}
		 	</ul>
		</div>
		<span class="space-keeper">
			<a class="next" href="#" style="height:{math equation='a+2' a=$thumbSceneImageType.height}px;" onclick="{ldelim}next_scene_is_at_right = true; $(this).parent().prev().trigger('stop').trigger('next'); return false;{rdelim}">&nbsp;</a>
		</span>
	</div>
	{/if}
</div>
{/if}
