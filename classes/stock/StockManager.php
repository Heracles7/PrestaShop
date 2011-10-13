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
*  @version  Release: $Revision: 9202 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * StockManager : implementation of StockManagerInterface
 * @since 1.5.0
 */
class StockManagerCore implements StockManagerInterface
{

	/**
	 * @see StockManagerInterface::isAvailable()
	 */
	public static function isAvailable()
	{
		// Default Manager : always available
		return true;
	}

	/**
	 * @see StockManagerInterface::addProduct()
	 */
	public function addProduct($id_product,
							   $id_product_attribute = 0,
							   Warehouse $warehouse,
							   $quantity,
							   $id_stock_mvt_reason,
							   $price_te,
							   $is_usable = true,
							   $id_supplier_order = null)
	{
		if (!Validate::isLoadedObject($warehouse) || !$price_te || !$quantity || !$id_product)
			return false;

		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_INC_REASON_DEFAULT');

		$context = Context::getContext();

		$mvt_params = array(
			'id_stock' => null,
			'physical_quantity' => $quantity,
			'id_stock_mvt_reason' => $id_stock_mvt_reason,
			'id_supplier_order' => $id_supplier_order,
			'price_te' => round($price_te, 6),
			'last_wa' => null,
			'current_wa' => null,
			'id_employee' => $context->employee->id,
			'sign' => 1
		);

		$stock_exists = false;

		// switch on MANAGEMENT_TYPE
		switch ($warehouse->management_type)
		{
			// case CUMP mode
			case 'WA':

				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);

				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// for a warehouse using WA, there is one and only one stock for a given product
					$stock = $stock_collection[0];

					// calculates WA price
					$last_wa = round($stock->price_te, 6);
					$current_wa = round($this->calculateWA($stock, $quantity, $price_te), 6);

					$mvt_params['id_stock'] = $stock->id;
					$mvt_params['last_wa'] = $last_wa;
					$mvt_params['current_wa'] = $current_wa;

					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'price_te' => $current_wa,
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
						'id_warehouse' => $warehouse->id,
					);

					// saves stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
				}
				else // else, the product is not in sock
				{
					$mvt_params['last_wa'] = 0;
					$mvt_params['current_wa'] = round($price_te, 6);
				}
			break;

			// case FIFO mode
			case 'FIFO':
			case 'LIFO':

				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id, $price_te);

				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// there is one and only one stock for a given product in a warehouse and at the current unit price
					$stock = $stock_collection[0];

					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
					);

					// updates stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();

					// sets mvt_params
					$mvt_params['id_stock'] = $stock->id;

				}

			break;

			default:
				return false;
			break;
		}

		if (!$stock_exists)
		{
			$stock = new Stock();

			$stock_params = array(
				'id_product_attribute' => $id_product_attribute,
				'id_product' => $id_product,
				'physical_quantity' => $quantity,
				'price_te' => round($price_te, 6),
				'usable_quantity' => ($is_usable ? $quantity : 0),
				'id_warehouse' => $warehouse->id,
				'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT')
			);

			// saves stock in warehouse
			$stock->hydrate($stock_params);
			$stock->add();

			$mvt_params['id_stock'] = $stock->id;
		}

		// saves stock mvt
		$stock_mvt = new StockMvt();
		$stock_mvt->hydrate($mvt_params);
		$stock_mvt->add();

		return true;
	}

	/**
	 * @see StockManagerInterface::removeProduct()
	 */
	public function removeProduct($id_product,
								  $id_product_attribute = null,
								  Warehouse $warehouse,
								  $quantity,
								  $id_stock_mvt_reason,
								  $is_usable = true,
								  $id_order = null)
	{
		$return = array();

		if (!Validate::isLoadedObject($warehouse) || !$quantity || !$id_product)
			return $return;

		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_DEC_REASON_DEFAULT');

		$context = Context::getContext();

		// gets total quantities in stock for the current product
		$quantity_in_stock = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), $is_usable);

		// checks if it's possible to remove the given quantity
		if ($quantity_in_stock < $quantity)
			return $return;

		$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);

		// check if the collection is loaded
		if (count($stock_collection) <= 0)
			return $return;

		$stock_history_qty_available = array();
		$mvt_params = array();
		$stock_params = array();
		$quantity_to_decrement_by_stock = array();
		$global_quantity_to_decrement = $quantity;

		// switch on MANAGEMENT_TYPE
		switch ($warehouse->management_type)
		{
			// case CUMP mode
			case 'WA':
				// There is one and only one stock for a given product in a warehouse in this mode
				$stock = $stock_collection[0];

				$mvt_params = array(
					'id_stock' => $stock->id,
					'physical_quantity' => $quantity,
					'id_stock_mvt_reason' => $id_stock_mvt_reason,
					'id_order' => $id_order,
					'price_te' => $stock->price_te,
					'last_wa' => $stock->price_te,
					'current_wa' => $stock->price_te,
					'id_employee' => $context->employee->id,
					'sign' => -1
				);
				$stock_params = array(
					'physical_quantity' => ($stock->physical_quantity - $quantity),
					'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $quantity) : $stock->usable_quantity),
					'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT')
				);

				// saves stock in warehouse
				$stock->hydrate($stock_params);
				$stock->update();

				// saves stock mvt
				$stock_mvt = new StockMvt();
				$stock_mvt->hydrate($mvt_params);
				$stock_mvt->save();

				$return[$stock->id]['quantity'] = $quantity;
				$return[$stock->id]['price_te'] = $stock->price_te;

			break;

			case 'LIFO':
			case 'FIFO':

				// for each stock, parse its mvts history to calculate the quantities left for each positive mvt,
				// according to the instant available quantities for this stock
				foreach ($stock_collection as $stock)
				{
					$left_quantity_to_check = $stock->physical_quantity;
					if ($left_quantity_to_check <= 0)
						continue;

					$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
						SELECT sm.`id_stock_mvt`, sm.`date_add`, sm.`physical_quantity`,
							IF ((sm2.`physical_quantity` is null), sm.`physical_quantity`, (sm.`physical_quantity` - SUM(sm2.`physical_quantity`))) as qty
						FROM `'._DB_PREFIX_.'stock_mvt` sm
						LEFT JOIN `'._DB_PREFIX_.'stock_mvt` sm2 ON sm2.`referer` = sm.`id_stock_mvt`
						WHERE sm.`sign` = 1
						AND sm.`id_stock` = '.(int)$stock->id.'
						GROUP BY sm.`id_stock_mvt`
						ORDER BY sm.`date_add` DESC'
					);

					while ($row = Db::getInstance()->nextRow($resource))
					{
						// break - in FIFO mode, we have to retreive the oldest positive mvts for which there are left quantities
						if ($warehouse->management_type == 'FIFO')
							if ($row['qty'] == 0)
								break;

						// converts date to timestamp
						$date = new DateTime($row['date_add']);
						$timestamp = $date->format('U');

						// history of the mvt
						$stock_history_qty_available[$timestamp] = array(
							'id_stock' => $stock->id,
							'id_stock_mvt' => (int)$row['id_stock_mvt'],
							'qty' => (int)$row['qty']
						);

						// break - in LIFO mode, checks only the necessary history to handle the global quantity for the current stock
						if ($warehouse->management_type == 'LIFO')
						{
							$left_quantity_to_check -= (int)$row['physical_quantity'];
							if ($left_quantity_to_check <= 0)
								break;
						}
					}
				}

				if ($warehouse->management_type == 'LIFO')
					// orders stock history by timestamp to get newest history first
					krsort($stock_history_qty_available);
				else
					// orders stock history by timestamp to get oldest history first
					ksort($stock_history_qty_available);

				// checks each stock to manage the real quantity to decrement for each of them
				foreach ($stock_history_qty_available as $entry)
				{
					if ($entry['qty'] >= $global_quantity_to_decrement)
					{
						$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $global_quantity_to_decrement;
						$global_quantity_to_decrement = 0;
					}
					else
					{
						$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $entry['qty'];
						$global_quantity_to_decrement -= $entry['qty'];
					}

					if ($global_quantity_to_decrement <= 0)
						break;
				}

				// for each stock, decrements it and logs the mvts
				foreach ($stock_collection as $stock)
				{
					if (array_key_exists($stock->id, $quantity_to_decrement_by_stock) && is_array($quantity_to_decrement_by_stock[$stock->id]))
					{
						$total_quantity_for_current_stock = 0;

						foreach ($quantity_to_decrement_by_stock[$stock->id] as $id_mvt_referrer => $qte)
						{
							$mvt_params = array(
								'id_stock' => $stock->id,
								'physical_quantity' => $qte,
								'id_stock_mvt_reason' => $id_stock_mvt_reason,
								'id_order' => $id_order,
								'price_te' => $stock->price_te,
								'sign' => -1,
								'referer' => $id_mvt_referrer,
								'id_employee' => $context->employee->id
							);

							// saves stock mvt
							$stock_mvt = new StockMvt();
							$stock_mvt->hydrate($mvt_params);
							$stock_mvt->save();

							$total_quantity_for_current_stock += $qte;
						}

						$stock_params = array(
							'physical_quantity' => ($stock->physical_quantity - $total_quantity_for_current_stock),
							'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $total_quantity_for_current_stock) : $stock->usable_quantity),
							'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT')
						);

						$return[$stock->id]['quantity'] = $total_quantity_for_current_stock;
						$return[$stock->id]['price_te'] = $stock->price_te;

						// saves stock in warehouse
						$stock->hydrate($stock_params);
						$stock->update();
					}
				}
			break;
		}
		return $return;
	}

	/**
	 * @see StockManagerInterface::getProductPhysicalQuantities()
	 */
	public function getProductPhysicalQuantities($id_product, $id_product_attribute, $id_warehouses = null, $usable = false)
	{
		$query = new DbQuery();
		$query->select('SUM('.($usable ? 's.usable_quantity' : 's.physical_quantity').')');
		$query->from('stock s');
		$query->where('s.id_product = '.(int)$id_product.' AND s.id_product_attribute = '.(int)$id_product_attribute);
		if (count($id_warehouses))
			$query->where('s.id_warehouse IN('.implode(', ', $id_warehouses).')');
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * @see StockManagerInterface::getProductRealQuantities()
	 */
	public function getProductRealQuantities($id_product, $id_product_attribute, $id_warehouses = null, $usable = false)
	{
		// Gets clients_orders_qty
		$query = new DbQuery();
		$query->select('SUM(od.product_quantity)');
		$query->from('order_detail od');
		$query->leftjoin('orders o ON o.id_order = od.id_order');
		$query->where('od.product_id = '.(int)$id_product.' AND od.product_attribute_id = '.(int)$id_product_attribute);
		$query->where('o.delivery_number = 0');
		$query->where('o.valid = 1');
		$clients_orders_qty = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

		// Gets {physical OR usable}_qty
		$qty = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, $id_warehouses, $usable);

		// Returns real_qty = qty - clients_orders_qty
		// @TODO include suplliers orders in the calcul when they will be implemented
		return ($qty - $clients_orders_qty);
	}

	/**
	 * @see StockManagerInterface::transferBetweenWarehouses()
	 */
	public function transferBetweenWarehouses($id_product,
											  $id_product_attribute,
											  $quantity,
											  $id_stock_mvt_reason,
											  $id_warehouse_from,
											  $id_warehouse_to,
											  $usable_from = true,
											  $usable_to = true)
	{
		// Checks if this transfer is possible
		if ($this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($id_warehouse_from), $usable_from) < $quantity)
			return false;

		// Checks if the given warehouses are available
		$warehouse_from = new Warehouse($id_warehouse_from);
		$warehouse_to = new Warehouse($id_warehouse_to);
		if (!Validate::isLoadedObject($warehouse_from) ||
			!Validate::isLoadedObject($warehouse_to))
			return false;

		// Removes from warehouse_from
		$stocks = $this->removeProduct($id_product,
									   $id_product_attribute,
									   $warehouse_from,
									   $quantity,
									   $id_stock_mvt_reason,
									   $usable_from);
		if (!count($stocks))
			return false;

		// Adds in warehouse_to
		foreach ($stocks as $stock)
		{
			if (!$this->addProduct($id_product,
								   $id_product_attribute,
								   $warehouse_to,
								   $stock['quantity'],
								   $id_stock_mvt_reason,
								   $stock['price_te'],
								   $usable_to))
				return false;
		}
		return true;
	}

	/**
	 * For a given stock, calculates its new WA(Weighted Average) price based on the new quantities and price
	 * Formula : (physicalStock * lastCump + quantityToAdd * unitPrice) / (physicalStock + quantityToAdd)
	 *
	 * @param Stock $stock
	 * @param int $quantity
	 * @param float $price_te
	 * @return int WA
	 */
	protected function calculateWA($stock, $quantity, $price_te)
	{
		return ((($stock->physical_quantity * $stock->price_te) + ($quantity * $price_te)) / ($stock->physical_quantity + $quantity));
	}

	/**
	 * For a given product, retrieves the stock collection
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @return array
	 */
	protected function getStockCollection($id_product, $id_product_attribute, $id_warehouse = null, $price_te = null)
	{
		// build query
		$query = new DbQuery();
		$query->select('s.id_stock, s.physical_quantity, s.usable_quantity, s.price_te, s.id_product, s.id_product_attribute, s.id_warehouse');
		$query->from('stock s');
		$query->where('s.id_product = '.(int)$id_product.' AND s.id_product_attribute = '.(int)$id_product_attribute);
		if ($id_warehouse)
			$query->where('s.id_warehouse = '.(int)$id_warehouse);
		if ($price_te)
			$query->where('s.price_te = '.(float)$price_te);

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		return ObjectModel::hydrateCollection('Stock', $results);
	}
}