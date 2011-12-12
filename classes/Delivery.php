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

class DeliveryCore extends ObjectModel
{
	/** @var integer */
	public $id_delivery;

	/** @var int **/
	public $id_shop;

	/** @var int **/
	public $id_group_shop;

	/** @var integer */
	public $id_carrier;

	/** @var integer */
	public $id_range_price;

	/** @var integer */
	public $id_range_weight;

	/** @var integer */
	public $id_zone;

	/** @var float */
	public $price;

	

	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'delivery',
		'primary' => 'id_delivery',
		'fields' => array(
			'id_carrier' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_range_price' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_range_weight' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_zone' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'price' => array('type' => 'FILL_ME', 'validate' => 'isPrice', 'required' => true),
		),
	);


	protected $webserviceParameters = array(
			'objectsNodeName' => 'deliveries',
			'fields' => array(
				'id_carrier' => array('xlink_resource' => 'carriers'),
				'id_range_price' => array('xlink_resource' => 'price_ranges'),
				'id_range_weight' => array('xlink_resource' => 'weight_ranges'),
				'id_zone' => array('xlink_resource' => 'zones'),
		)
	);

	public function getFields()
	{
		$this->validateFields();

		if ($this->id_shop)
			$fields['id_shop'] = (int)$this->id_shop;
		if ($this->id_group_shop)
			$fields['id_group_shop'] = (int)$this->id_group_shop;
		$fields['id_carrier'] = (int)$this->id_carrier;
		$fields['id_range_price'] = (int)$this->id_range_price;
		$fields['id_range_weight'] = (int)$this->id_range_weight;
		$fields['id_zone'] = (int)$this->id_zone;
		$fields['price'] = (float)$this->price;

		return $fields;
	}
}

