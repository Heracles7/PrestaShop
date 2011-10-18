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
<script type="text/javascript">
$(document).ready(function() {
	$('#details_{$id}').click(function() {
		if (typeof(this.dataMaped) == 'undefined') {
			$.ajax({
				url: 'index.php',
				data: {
					id: '{$id}',
					controller: '{$controller}',
					token: '{$token}',
					action: '{$action}',
					ajax: true
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data) {
					if(typeof(data.use_parent_structure) == 'undefined' || (data.use_parent_structure == true))
						$.each(data.data, function(it, row)
						{
							if($('#details_{$id}').parent().parent().hasClass('alt_row'))
								var content = $('<tr class="details_{$id} alt_row"></tr>');
							else
								var content = $('<tr class="details_{$id}"></tr>');
							content.append($('<td></td>'));
							$.each(data.fields_display, function(it, line)
							{
								if (typeof(row[it]) == 'undefined')
									content.append($('<td class="'+this.align+'"></td>'));
								else
									content.append($('<td class="'+this.align+'">'+row[it]+'</td>'));
							});
							content.append($('<td></td>'));
							$('#details_{$id}').parent().parent().after(content);
						});
					else
					{
						if($('#details_{$id}').parent().parent().hasClass('alt_row'))
							var content = $('<tr class="details_{$id} alt_row"></tr>');
						else
							var content = $('<tr class="details_{$id}"></tr>');
						content.append($('<td style="border:none!important;">'+data.data+'</td>').attr('colspan', $('#details_{$id}').parent().parent().find('td').length));
						$('#details_{$id}').parent().parent().after(content);
					}
					this.dataMaped = true;
					this.opened = false;
				}
			});
		}

		if(this.opened)
		{
			$(this).find('img').attr('src', '../img/admin/more.png');
			$(this).parent().parent().parent().find('.details_{$id}').hide();
			this.opened = false
		}
		else
		{
			$(this).find('img').attr('src', '../img/admin/less.png');
			$(this).parent().parent().parent().find('.details_{$id}').show();
			this.opened = true;
		}
		return false;
	});
});
</script>
<a href="#" id="details_{$id}">
	<img src="../img/admin/more.png" alt="{$action}" title="{$action}" />
</a>