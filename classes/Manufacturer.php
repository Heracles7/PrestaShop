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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ManufacturerCore extends ObjectModel
{
	public $id;

	/** @var integer manufacturer ID */
	public $id_manufacturer;//FIXME is it really usefull...?

	/** @var string Name */
	public $name;

	/** @var string A description */
	public $description;

	/** @var string A short description */
	public $short_description;

	/** @var int Address */
	public $id_address;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/** @var string Friendly URL */
	public $link_rewrite;

	/** @var string Meta title */
	public $meta_title;

	/** @var string Meta keywords */
	public $meta_keywords;

	/** @var string Meta description */
	public $meta_description;

	/** @var boolean active */
	public $active;

 	protected $fieldsRequired = array('name');
 	protected $fieldsSize = array('name' => 64);
 	protected $fieldsValidate = array('name' => 'isCatalogName');

	protected $fieldsSizeLang = array('short_description' => 254, 'meta_title' => 128, 'meta_description' => 255, 'meta_description' => 255);
	protected $fieldsValidateLang = array(
		'description' => 'isString',
		'short_description' => 'isString',
		'meta_title' => 'isGenericName',
		'meta_description' => 'isGenericName',
		'meta_keywords' => 'isGenericName'
	);

	protected $table = 'manufacturer';
	protected $identifier = 'id_manufacturer';

	protected $webserviceParameters = array(
		'fields' => array(
			'active' => array(),
			'link_rewrite' => array('getter' => 'getLink', 'setter' => false),
		),
		'associations' => array(
			'addresses' => array('resource' => 'address', 'setter' => false, 'fields' => array(
				'id' => array('xlink_resource' => 'addresses'),
			)),
		),
	);

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		/* Get the manufacturer's id_address */
		$this->id_address = $this->getManufacturerAddress();
		$this->link_rewrite = $this->getLink();
		$this->image_dir = _PS_MANU_IMG_DIR_;
	}

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_manufacturer'] = (int)$this->id;
		$fields['name'] = pSQL($this->name);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)$this->active;
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array(
			'meta_description',
			'meta_keywords',
			'meta_title',
			'description' => array('html' => true),
			'short_description' => array('html' => true),
		));
	}

	public function delete()
	{
		$address = new Address($this->id_address);
		if (!$address->delete())
			return false;
		if (parent::delete())
			return $this->deleteImage();
	}

	/**
	 * Delete several objects from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		if (!is_array($selection) || !Validate::isTableOrIdentifier($this->identifier) || !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection as $id)
		{
			$this->id = (int)$id;
			$this->id_address = self::getManufacturerAddress();
			$result = $result && $this->delete();
		}
		return $result;
	}

	protected function getManufacturerAddress()
	{
		if (!(int)$this->id)
			return false;
		$result = Db::GetInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `id_address`
			FROM '._DB_PREFIX_.'address
			WHERE `id_manufacturer` = '.(int)$this->id
		);
		if (!$result)
			return false;
		return $result['id_address'];
	}

	/**
	  * Return manufacturers
	  *
	  * @param boolean $get_nb_products [optional] return products numbers for each
	  * @return array Manufacturers
	  */
	public static function getManufacturers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false, $id_group_shop = false)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		if (!$id_group_shop)
			$id_group_shop = (Context::getContext()->shop->id ? Shop::getGroupFromShop(Context::getContext()->shop->id) : Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')));

		$sql = 'SELECT m.*, ml.`description`';
		$sql .= 'FROM `'._DB_PREFIX_.'manufacturer_group_shop` mgs
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.id_manufacturer = mgs.id_manufacturer)
		LEFT JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
		WHERE mgs.id_group_shop='.(int)$id_group_shop.($active ? ' AND m.`active` = 1' : '');
		$sql .= ' ORDER BY m.`name` ASC'.($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');
		$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($manufacturers === false)
			return false;
		if ($get_nb_products)
		{
			$sql_groups = '';
			if (!$all_group)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($manufacturers as $key => $manufacturer)
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
				WHERE m.`id_manufacturer` = '.(int)$manufacturer['id_manufacturer'].
				($active ? ' AND p.`active` = 1 ' : '').
				($all_group ? '' : ' AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sql_groups.')'));

				$manufacturers[$key]['nb_products'] = count($result);
			}
		}
		$total_manufacturers = count($manufacturers);
		for ($i = 0; $i < $total_manufacturers; $i++)
			if ((int)Configuration::get('PS_REWRITING_SETTINGS'))
				$manufacturers[$i]['link_rewrite'] = Tools::link_rewrite($manufacturers[$i]['name'], false);
			else
				$manufacturers[$i]['link_rewrite'] = 0;
		return $manufacturers;
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_manufacturer Manufacturer ID
	  * @return string name
	  */
	static protected $cacheName = array();
	public static function getNameById($id_manufacturer)
	{
		if (!isset(self::$cacheName[$id_manufacturer]))
			self::$cacheName[$id_manufacturer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `name`
				FROM `'._DB_PREFIX_.'manufacturer`
				WHERE `id_manufacturer` = '.(int)$id_manufacturer.'
				AND `active` = 1'
			);
		return self::$cacheName[$id_manufacturer];
	}

	public static function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM `'._DB_PREFIX_.'manufacturer`
		WHERE `name` = \''.pSQL($name).'\'');
		if (isset($result['id_manufacturer']))
			return (int)$result['id_manufacturer'];
		return false;
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	public static function getProducts($id_manufacturer, $id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $active_category = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		if ($p < 1)
			$p = 1;

	 	if (empty($order_by) || $order_by == 'position')
	 		$order_by = 'name';

	 	if (empty($order_way)) $order_way = 'ASC';

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1';

		/* Return only the number of products */
		if ($get_total)
		{
			$sql = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p').'
				WHERE p.id_manufacturer = '.(int)$id_manufacturer
				.($active ? ' AND p.`active` = 1' : '').'
				AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
					($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
					WHERE cg.`id_group` '.$sql_groups.'
				)';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			return (int)count($result);
		}

		$sql = 'SELECT p.*, sa.out_of_stock, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
				pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name,
				tl.`name` AS tax_name, t.`rate`, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
				INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
				(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice
				FROM `'._DB_PREFIX_.'product` p
				'.$context->shop->addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product` AND default_on = 1)
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
					ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
						AND tr.`id_country` = '.(int)$context->country->id.'
						AND tr.`id_state` = 0
						AND tr.`zipcode_from` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t
					ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
					ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
					ON (sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0)
				WHERE p.`id_manufacturer` = '.(int)$id_manufacturer.($active ? '
					AND p.`active` = 1' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp
							ON (cp.`id_category` = cg.`id_category`)'.
					($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
						WHERE cg.`id_group` '.$sql_groups.'
					)
				ORDER BY '.(($order_by == 'id_product') ? 'p.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (!$result)
			return false;
		if ($order_by == 'price')
			Tools::orderbyPrice($result, $order_way);
		return Product::getProductsProperties($id_lang, $result);
	}

	public function getProductsLite($id_lang)
	{
		$sql = 'SELECT p.`id_product`,  pl.`name`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('pl').')
				WHERE p.`id_manufacturer` = '.(int)$this->id;
		return Db::getInstance()->executeS($sql);
	}
	/*
	* Specify if a manufacturer already in base
	*
	* @param $id_manufacturer Manufacturer id
	* @return boolean
	*/
	public static function manufacturerExists($id_manufacturer)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM '._DB_PREFIX_.'manufacturer m
		WHERE m.`id_manufacturer` = '.(int)$id_manufacturer);

		return isset($row['id_manufacturer']);
	}

	public function getAddresses($id_lang)
	{
		return Db::getInstance()->executeS('
		SELECT a.*, cl.name AS `country`, s.name AS `state`
		FROM `'._DB_PREFIX_.'address` AS a
		LEFT JOIN `'._DB_PREFIX_.'country_lang` AS cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'state` AS s ON (s.`id_state` = a.`id_state`)
		WHERE `id_manufacturer` = '.(int)$this->id.'
		AND a.`deleted` = 0');
	}

	public function getWsAddresses()
	{
			return Db::getInstance()->executeS('
		SELECT a.id_address as id
		FROM `'._DB_PREFIX_.'address` AS a
		WHERE `id_manufacturer` = '.(int)$this->id.'
		AND a.`deleted` = 0');
	}

	public function setWsAddresses($id_addresses)
	{
		$ids = array();
		foreach ($id_addresses as $id)
			$ids[] = (int)$id['id'];
		$result1 = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'address` 
			SET id_manufacturer = 0 
			WHERE id_manufacturer = '.(int)$this->id.' 
			AND deleted = 0') !== false);
		$result2 = true;
		if (count($ids))
			$result2 = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'address` 
			SET id_customer = 0, id_supplier = 0, id_manufacturer = '.(int)$this->id.' 
			WHERE id_address IN('.implode(',', $ids).') 
			AND deleted = 0') !== false);
		return ($result1 && $result2);
	}
}

