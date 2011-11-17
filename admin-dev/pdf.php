<?php
/*
* 2007-2011 PrestaShop
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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('_PS_ADMIN_DIR_', getcwd());

include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!Context::getContext()->employee->id)
	Tools::redirectAdmin('login.php');

$functionArray = array(
	'pdf' => 'generateInvoicePDF',
	'id_order_slip' => 'generateOrderSlipPDF',
	'id_delivery' => 'generateDeliverySlipPDF',
	'invoices' => 'generateInvoicesPDF',
	'invoices2' => 'generateInvoicesPDF2',
	'slips' => 'generateOrderSlipsPDF',
	'deliveryslips' => 'generateDeliverySlipsPDF'
);

foreach ($functionArray as $var => $function)
	if (isset($_GET[$var]))
	{
		call_user_func($function);
		die;
	}

function generateInvoicePDF()
{
    if (!isset($_GET['id_order']))
		die (Tools::displayError('Missing order ID'));
	$order = new Order((int)($_GET['id_order']));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));

    generatePDF($order, PDF::TEMPLATE_INVOICE);
}

function generateOrderSlipPDF()
{
	$orderSlip = new OrderSlip((int)($_GET['id_order_slip']));
	$order = new Order((int)($orderSlip->id_order));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));
	$order->products = OrderSlip::getOrdersSlipProducts($orderSlip->id, $order);
	$tmp = NULL;

   generatePDF($order, PDF::TEMPLATE_ORDER_SLIP);
}

function generateDeliverySlipPDF()
{
	$order = Order::getByDelivery((int)($_GET['id_delivery']));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));

    generatePDF($order, PDF::TEMPLATE_DELIVERY_SLIP);
}

function generateInvoicesPDF()
{
	$id_orders_list = Order::getOrdersIdInvoiceByDate($_GET['date_from'], $_GET['date_to'], NULL, 'invoice');
	if (!is_array($id_orders_list))
		die (Tools::displayError('No invoices found'));

    $orders = array();
    foreach ($id_orders_list as $id_order)
        $orders[] = new Order((int)$id_order);

    generatePDF($orders, PDF::TEMPLATE_INVOICE);
}

function generateInvoicesPDF2()
{
	$id_orders_list = array();
	foreach (explode('-', Tools::getValue('id_order_state')) as $id_order_state)
		if (is_array($id_orders = Order::getOrderIdsByStatus((int)$id_order_state)))
			$id_orders_list = array_merge($id_orders_list, $id_orders);

    $orders = array();
    foreach ($id_orders_list as $id_order)
        $orders[] = new Order((int)$id_order);

    generatePDF($orders, PDF::TEMPLATE_INVOICE);
}

function generateOrderSlipsPDF()
{
	$id_order_slips_list = OrderSlip::getSlipsIdByDate($_GET['date_from'], $_GET['date_to']);
	if (!count($id_order_slips_list))
		die (Tools::displayError('No order slips found'));

    $order_slips = array();
    foreach ($id_order_slips_list as $id_order_slips)
        $order_slips[] = new OrderSlip((int)$id_order_slips);

    generatePDF($order_slips, PDF::TEMPLATE_ORDER_SLIP);
}

function generateDeliverySlipsPDF()
{
	$slips = unserialize(urldecode($_GET['deliveryslips']));
	if (is_array($slips))
        generatePDF($slips, PDF::TEMPLATE_DELIVERY_SLIP);
}


function generatePDF($object, $template)
{
    global $smarty;
    $pdf = new PDF($object, $template, $smarty);
    $pdf->render();
}

