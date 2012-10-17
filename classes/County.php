<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


/**
* @deprecated since 1.5
*/
class CountyCore extends ObjectModel
{
	public $id;
	public $name;
	public $id_state;
	public $active;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'county',
		'primary' => 'id_county',
		'fields' => array(
			'name' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
			'id_state' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'active' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	protected static $_cache_get_counties = array();
	protected static $_cache_county_zipcode = array();

	const USE_BOTH_TAX = 0;
	const USE_COUNTY_TAX = 1;
	const USE_STATE_TAX = 2;

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_state' => array('xlink_resource'=> 'states'),
		),
	);

	public function delete()
	{
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getCounties($id_state)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function getZipCodes()
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function addZipCodes($zip_codes)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public function removeZipCodes($zip_codes)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public function breakDownZipCode($zip_codes)
	{
		Tools::displayAsDeprecated();
		return array(0,0);
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getIdCountyByZipCode($id_state, $zip_code)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function isZipCodeRangePresent($zip_codes)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function isZipCodePresent($zip_code)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function deleteZipCodeByIdCounty($id_county)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getIdCountyByNameAndIdState($name, $id_state)
	{
		Tools::displayAsDeprecated();
		return false;
	}

}

