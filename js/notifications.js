function getPush()
{
	$.post("ajax.php",{"getNotifications" : "1"}, function(data) {
		if (data)
		{
			json = jQuery.parseJSON(data);
			
			// Add orders notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.order, function(property, value) {
				html += "<li>" + new_order_msg + "<br />" + order_number_msg + "<strong>#" + parseInt(value.id_order) + "</strong><br />" + total_msg + "<strong>" + value.total_paid_real + "</strong><br />" + from_msg + "<strong>" + value.customer_name + "</strong><br /><a href=\"index.php?tab=AdminOrders&token=" + token_admin_orders + "&vieworder&id_order=" + parseInt(value.id_order) + "\">" + see_order_msg + "</a></li>";
			});						
			if (html != "")
			{
				$("#list_orders_notif").prev("p").hide();
				$("#list_orders_notif").empty().append(html);
				nb_notifs = $("#list_orders_notif li").length;
				$("#orders_notif_value").text(nb_notifs);
				$("#orders_notif_number_wrapper").show();
			}
			else
			{
				$("#orders_notif_number_wrapper").hide();
			}	
			
			// Add customers notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.customer, function(property, value) {
				html += "<li>" + new_customer_msg + "<br />" + customer_name_msg + "<strong>" + value.customer_name + "</strong><br /><a href=\"index.php?tab=AdminCustomers&token=" + token_admin_customers + "&viewcustomer&id_customer=" + parseInt(value.id_customer) + "\">" + see_customer_msg + "</a></li>";
			});						
			if (html != "")
			{
				$("#list_customers_notif").prev("p").hide();						
				$("#list_customers_notif").empty().append(html);
				nb_notifs = $("#list_customers_notif li").length;
				$("#customers_notif_value").text(nb_notifs);
				$("#customers_notif_number_wrapper").show();
			}
			else
			{
				$("#customers_notif_number_wrapper").hide();
			}
			
			// Add messages notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.message, function(property, value) {
				html += "<li>" + new_msg + "<br />" + from_msg + "<strong>" + value.customer_name + "</strong><br />" + excerpt_msg + "<strong>" + value.message_customer + "</strong><br /><a href=\"index.php?tab=AdminOrders&token='.Tools::getAdminTokenLite('AdminOrders').'&vieworder&id_order=" + parseInt(value.id_order) + "\">" + see_msg + "</a></li>";
			});
			if (html != "")
			{
				$("#list_messages_notif").prev("p").hide();	
				$("#list_messages_notif").empty().append(html);
				nb_notifs = $("#list_messages_notif li").length;
				$("#messages_notif_value").text(nb_notifs);
				$("#messages_notif_number_wrapper").show();
			}
			else
			{
				$("#messages_notif_number_wrapper").hide();
			}
		}
		setTimeout("getPush()",60000);
	});
}

$().ready(function()
{
	var hints = $('.translatable span.hint');
	if (youEditFieldFor)
	{
		hints.html(hints.html() + '<br /><span class="red">' + youEditFieldFor + '</span>');
	}
	var html = "";		
	var nb_notifs = 0;
	var wrapper_id = "";
	var type = new Array();
	
	$(".notifs").live("click", function(){
		wrapper_id = $(this).attr("id");
		type = wrapper_id.split("s_notif")
		$.post("ajax.php",{"updateElementEmployee" : "1", "updateElementEmployeeType" : type[0]}, function(data) {
			if(data)
			{
				if(!$("#" + wrapper_id + "_wrapper").is(":visible"))
				{
					$(".notifs_wrapper").hide();
					$("#" + wrapper_id + "_number_wrapper").hide();  
					$("#" + wrapper_id + "_wrapper").show();  
				}else
				{
					$("#" + wrapper_id + "_wrapper").hide();							
				}
			}				
		});
	});
	
	$("#main").click(function(){
		$(".notifs_wrapper").hide();
	});

	getPush();
});