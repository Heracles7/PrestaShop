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

class SupplierCore extends ObjectModel
{
	public $id;

	/** @var integer supplier ID */
	public $id_supplier;

	/** @var string Name */
	public $name;

	/** @var string A short description for the discount */
	public $description;

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
	protected $fieldsSizeLang = array('meta_title' => 128, 'meta_description' => 255, 'meta_keywords' => 255);
	protected $fieldsValidateLang = array(
		'description' => 'isGenericName',
		'meta_title' => 'isGenericName',
		'meta_description' => 'isGenericName',
		'meta_keywords' => 'isGenericName'
	);

	protected $table = 'supplier';
	protected $identifier = 'id_supplier';

	protected	$webserviceParameters = array(
		'fields' => array(
			'link_rewrite' => array('sqlId' => 'link_rewrite'),
		),
	);

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		$this->link_rewrite = $this->getLink();
		$this->image_dir = _PS_SUPP_IMG_DIR_;
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_supplier'] = (int)$this->id;
		$fields['name'] = pSQL($this->name);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)$this->active;
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array('description', 'meta_title', 'meta_keywords', 'meta_description'));
	}

	/**
	  * Return suppliers
	  *
	  * @return array Suppliers
	  */
	public static function getSuppliers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_groups = false)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');
		$query = 'SELECT s.*, sl.`description`';
		$query .= ' FROM `'._DB_PREFIX_.'supplier` as s
		LEFT JOIN `'._DB_PREFIX_.'supplier_lang` sl ON (s.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$id_lang.')
		'.Context::getContext()->shop->addSqlAssociation('supplier', 's').'
		'.($active ? ' WHERE s.`active` = 1 ' : '');
		$query .= ' ORDER BY s.`name` ASC'.($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');
		$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		if ($suppliers === false)
			return false;
		if ($get_nb_products)
		{
			$sql_groups = '';
			if (!$all_groups)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($suppliers as $key => $supplier)
			{
				$sql = '
					SELECT DISTINCT(ps.`id_product`)
					FROM `'._DB_PREFIX_.'product_supplier` ps
					JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
					WHERE ps.`id_supplier` = '.(int)$supplier['id_supplier'].'
					AND ps.id_product_attribute = 0'.
					($active ? ' AND p.`active` = 1' : '').
					($all_groups ? '' :'
					AND ps.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				$suppliers[$key]['nb_products'] = count($result);
			}
		}

		$nb_suppliers = count($suppliers);
		for ($i = 0; $i < $nb_suppliers; $i++)
			if ((int)Configuration::get('PS_REWRITING_SETTINGS'))
				$suppliers[$i]['link_rewrite'] = Tools::link_rewrite($suppliers[$i]['name'], false);
			else
				$suppliers[$i]['link_rewrite'] = 0;
		return $suppliers;
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_supplier Supplier ID
	  * @return string name
	  */
	static protected $cache_name = array();
	public static function getNameById($id_supplier)
	{
		if (!isset(self::$cache_name[$id_supplier]))
			self::$cache_name[$id_supplier] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'supplier` WHERE `id_supplier` = '.(int)$id_supplier);
		return self::$cache_name[$id_supplier];
	}

	public static function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_supplier`
		FROM `'._DB_PREFIX_.'supplier`
		WHERE `name` = \''.pSQL($name).'\'');

		if (isset($result['id_supplier']))
			return (int)$result['id_supplier'];

		return false;
	}

	public static function getProducts($id_supplier, $id_lang, $p, $n,
		$order_by = null, $order_way = null, $get_total = false, $active = true, $active_category = true)
	{
		if ($p < 1) $p = 1;
	 	if (empty($order_by) || $order_by == 'position') $order_by = 'name';
	 	if (empty($order_way)) $order_way = 'ASC';

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		/* Return only the number of products */
		if ($get_total)
		{
			$sql = '
				SELECT DISTINCT(ps.`id_product`)
				FROM `'._DB_PREFIX_.'product_supplier` ps
				JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
				WHERE ps.`id_supplier` = '.(int)$id_supplier.'
				AND ps.id_product_attribute = 0'.
				($active ? ' AND p.`active` = 1' : '').'
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

		$nb_days_new_product = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

		$sql = 'SELECT p.*, sa.out_of_stock,
					pl.`description`,
					pl.`description_short`,
					pl.`link_rewrite`,
					pl.`meta_description`,
					pl.`meta_keywords`,
					pl.`meta_title`,
					pl.`name`,
					i.`id_image`,
					il.`legend`,
					s.`name` AS supplier_name,
					tl.`name` AS tax_name,
					t.`rate`,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.($nb_days_new_product).' DAY)) > 0 AS new,
					(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice,
					m.`name` AS manufacturer_name
				FROM `'._DB_PREFIX_.'product` p
				JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.id_product = p.id_product
					AND ps_id_product_attribute = 0)
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`
					AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
					AND tr.`zipcode_from` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax`
					AND tl.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON s.`id_supplier` = p.`id_supplier`
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.`id_product` = p.`id_product`
					AND sa.id_product_attribute = 0)
				WHERE ps.`id_supplier` = '.(int)$id_supplier.
					($active ? ' AND p.`active` = 1' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
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
		return Db::getInstance()->executeS('
			SELECT p.`id_product`,  pl.`name`
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
			WHERE p.`id_supplier` = '.(int)$this->id
		);
	}

	/*
	* Specify if a supplier already in base
	*
	* @param $id_supplier Supplier id
	* @return boolean
	*/
	public static function supplierExists($id_supplier)
	{
		$row = Db::getInstance()->getRow('
			SELECT `id_supplier`
			FROM '._DB_PREFIX_.'supplier s
			WHERE s.`id_supplier` = '.(int)$id_supplier
		);

		return isset($row['id_supplier']);
	}

	public function delete()
	{
		if (parent::delete())
			return $this->deleteImage();
	}
}

