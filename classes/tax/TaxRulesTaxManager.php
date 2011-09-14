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

class TaxRulesTaxManagerCore implements TaxManagerInterface
{
	public $address;
	public $type;
	public $tax_calculator;


	public function __construct(Address $address, $type)
	{
		$this->address = $address;
		$this->type = $type;
	}

	/**
	* Returns true if this tax manager is available for this address
	*
	* @return boolean
	*/
	public static function isAvailableForThisAddress(Address $address)
	{
		return true; // default manager, available for all addresses
	}

	/**
	* Return the tax calculator associated to this address
	*
	* @return TaxCalculator
	*/
	public function getTaxCalculator()
	{
		if (isset($this->tax_calculator))
			return $this->tax_calculator;

		$postcode = 0;
		if (!empty($this->address->postcode))
			$postcode = $this->address->postcode;

		$rows = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'tax_rule`
		WHERE `id_country` = '.(int)$this->address->id_country.'
		AND `id_tax_rules_group` = '.(int)$this->type.'
		AND `id_state` IN (0, '.(int)$this->address->id_state.')
		AND ('.pSQL($postcode).' BETWEEN `zipcode_from` AND `zipcode_to` OR `zipcode_from` = 0 OR `zipcode_from` = '.pSQL($postcode).')
		ORDER BY `zipcode_from` DESC, `zipcode_to` DESC, `id_state` DESC, `id_country` DESC');

		$behavior = 0;
		$first_row = true;
		$taxes = array();

		foreach ($rows as $row)
		{
			$tax = new Tax((int)$row['id_tax']);

			$taxes[] = $tax;

			// the applied behavior correspond to the most specific rules
			if ($first_row)
			{
				$behavior = $row['behavior'];
				$first_row = false;
			}

			if ($row['behavior'] == 0)
				 break;
		}

		return new TaxCalculator($taxes, $behavior);
	}
}

