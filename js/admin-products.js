/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Combination */

var posC = true;
$(document).ready(function() {
	$('#desc-product-newCombination').click(function() {
		if (posC == true)
			removeButtonCombination('add');
		else
			addButtonCombination('add');
	});
});

function removeButtonCombination(item)
{
	$('#add_new_combination').show();
	$('.process-icon-newCombination').removeClass('toolbar-new');
	$('.process-icon-newCombination').addClass('toolbar-cancel');
	$('#submitProductAttribute').val($('#submitProductAttribute').attr(item));
	$('#desc-product-newCombination div').html($('#ResetBtn').val());
	$('id_product_attribute').val(0);
	init_elems();
	posC = false;
}

function addButtonCombination(item)
{
	$('#add_new_combination').hide();
	$('.process-icon-newCombination').removeClass('toolbar-cancel');
	$('.process-icon-newCombination').addClass('toolbar-new');
	$('#submitProductAttribute').val($('#submitProductAttribute').attr(item));
	$('#desc-product-newCombination div').html($('#submitProductAttribute').val());
	posC = true;
}

function deleteProductAttribute(ids, token, parent)
{
	var id = ids.split('||');
	$.ajax({
		url: 'index.php',
		data: {
			id_product: id[0],
			id_product_attribute: id[1],
			controller: 'AdminProducts',
			token: token,
			action: 'deleteProductAttribute',
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.message);
				parent.hide();
			}
			else
				showErrorMessage(data.message);
		}
	});
}

function defaultProductAttribute(ids, token, parent)
{
	var id = ids.split('||');
	$.ajax({
		url: 'index.php',
		data: {
			id_product: id[0],
			id_product_attribute: id[1],
			controller: 'AdminProducts',
			token: token,
			action: 'defaultProductAttribute',
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.message);
				$('table.table').find('tr').each(function(){
					$(this).attr('style', '');
				});
				parent.find('a.default').hide();
				parent.css('background','#BDE5F8');
			}
			else
				showErrorMessage(data.message);
		}
	});
}

function editProductAttribute(ids, token)
{
	var id = ids.split('||');
	$.ajax({
		url: 'index.php',
		data: {
			id_product: id[0],
			id_product_attribute: id[1],
			controller: 'AdminProducts',
			token: token,
			action: 'editProductAttribute',
			ajax: true
		},
		context: document.body,
		dataType: 'json',
		context: this,
		async: false,
		success: function(data) {
			$('#add_new_combination').show();
			$('#attribute_quantity').show();
			$('#product_att_list').html('');
			removeButtonCombination('update');
			$.scrollTo('#add_new_combination', 1200, { offset: -100 });

			var wholesale_price = Math.abs(data[0]['wholesale_price']);
			var price = Math.abs(data[0]['price']);
			var weight = Math.abs(data[0]['weight']);
			var unit_impact = Math.abs(data[0]['unit_price_impact']);
			var reference = data[0]['reference'];
			var ean = data[0]['ean13'];
			var quantity = data[0]['quantity'];
			var image = false;
			var old_attr = Array(data[0]['list_attributes']);

			var product_att_list = '';
			for(i=0;i<data.length;i++)
			{
				product_att_list += data[i]['group_name']+' : ';
				product_att_list += data[i]['attribute_name']+', ';
				product_att_list += data[i]['id_attribute']+', ';
			}

			var old_attr = Array(product_att_list.substr(0, (product_att_list.length-2)));
			var id_product_attribute = data[0]['id_product_attribute'];
			var default_attribute = data[0]['default_on'];
			var eco_tax = data[0]['ecotax'];
			var upc = data[0]['upc'];
			var minimal_quantity = data[0]['minimal_quantity'];
			var available_date = data[0]['available_date'];
			var virtual_product_name_attribute = null;
			var virtual_product_filename_attribute = null;
			var virtual_product_nb_downloable = null;
			var virtual_product_expiration_date_attribute = null;
			var virtual_product_nb_days = null;
			var is_shareable = null;

			fillCombinaison(
				wholesale_price,
				price,
				weight,
				unit_impact,
				reference,
				ean,
				quantity,
				image,
				old_attr,
				id_product_attribute,
				default_attribute,
				eco_tax,
				upc,
				minimal_quantity,
				available_date,
				virtual_product_name_attribute,
				virtual_product_filename_attribute,
				virtual_product_nb_downloable,
				virtual_product_expiration_date_attribute,
				virtual_product_nb_days,
				is_shareable
			);
			calcImpactPriceTI();
		}
	});
}
/* END Combination */

function displayTabProductById(el, id, selected)
{
	myurl = $(el).attr("href")+"&ajax=1";
	$.ajax({
		url : myurl,
		async : true,
		success :function(data)
		{
			$("#product-tab-content-"+id).html(data);
			$("#product-tab-content-"+id).removeClass('not-loaded');

			if (selected)
			{
				$("#link-"+id).addClass('selected');
				$("#product-tab-content-"+id).show();
			}
		}
	});
}

/**
 * Update the manufacturer select element with the list of existing manufacturers
 */
function getManufacturers()
{
	$.ajax({
			url: 'ajax-tab.php',
			cache: false,
			dataType: 'json',
			data: {
				ajaxProductManufacturers:"1",
				ajax : '1',
				token : token,
				controller : 'AdminProducts',
				action : 'productManufacturers'
			},
			success: function(j) {
				var options = $('select#id_manufacturer').html();
				if (j)
				for (var i = 0; i < j.length; i++)
					options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
				$("select#id_manufacturer").replaceWith("<select id=\"id_manufacturer\">"+options+"</select>");
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
			}
	});
}

/* function autocomplete */
urlToCall = null;

$(document).ready(function() {
	updateCurrentText();
	updateFriendlyURL();
});