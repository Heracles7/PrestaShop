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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * StockManagerInterface : defines a way to manage stock
 * @since 1.5.0
 */
interface StockManagerInterface
{

	/**
	 * Checks if the StockManager is available
	 *
	 * @return StockManagerInterface
	 */
	public static function isAvailable();

	/**
	 * For a given product, adds a given quantity
	 *
	 * @param int $id_product
	 * @param int id_product_attribute
	 * @param int $id_warehouse
	 * @param int $quantity
	 * @param int $id_stock_mouvement_reason
	 * @param float $price_te
	 * @param bool $is_usable
	 * @param int $id_supplier_order Optionnal
	 * @return bool
	 */
	public function addProduct($id_product, $id_product_attribute, $id_warehouse, $quantity, $id_stock_mouvement_reason, $price_te, $is_usable, $id_supplier_order = null);

	/**
	 * For a given product, removes a given quantity
	 *
	 * @param int $id_product
	 * @param int id_product_attribute
	 * @param int $id_warehouse
	 * @param int $quantity
	 * @param int $id_stock_mouvement_reason
	 * @param int $id_order Optionnal
	 * @return bool
	 */
	public function removeProduct($id_product, $id_product_attribute, $id_warehouse, $quantity, $id_stock_mouvement_reason, $id_order = null);

	/**
	 * For a given product, returns its physical quantity
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse Optionnal
	 * @param bool $usable false default - in this case we retrieve all physical quantities, otherwise we retrieve physical quantities flagged as usable
	 * @return int
	 */
	public function getProductPhysicalQuantities($id_product, $id_product_attribute, $id_warehouse = null, $usable = false);

	/**
	 * For a given product, returns its real quantity
	 * Real quantity : (physical_qty + supplier_orders_qty - client_orders_qty)
	 * If $usable is defined, real quantity: usable_qty + supplier_orders_qty - client_orders_qty
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param bool $usable false by default
	 * @param int $id_warehouse Optionnal
	 * @return int
	 */
	public function getProductRealQuantities($id_product, $id_product_attribute, $id_warehouse = null, $usable = false);

	/**
	 * For a given product, transfers quantities between two warehouses
	 * By default, it manages usable quantities
	 * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 2
	 * It is also possible to transfer a usable quantity from warehouse 1 in an unusable quantity to warehouse 1
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $quantity
	 * @param int $id_warehouse_from
	 * @param int $id_warehouse_to
	 * @param bool $usable_from true by default
	 * @param bool $usable_to true by default
	 * @return bool
	 */
	public function transferBetweenWarehouses($id_product, $id_product_attribute, $quantity, $id_warehouse_from, $id_warehouse_to, $usable_from = true, $usable_to = true);

}