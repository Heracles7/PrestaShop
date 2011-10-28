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
 * @since 1.5.0
 */
class SupplierOrderStateCore extends ObjectModel
{
	/**
	 * @var string Name of the state
	 */
	public $name;

	/**
	 * @var bool Tells if a delivery note can be issued (i.e. the order has been validated)
	 */
	public $delivery_note;

	/**
	 * @var bool Tells if the order is still editable by an employee
	 */
	public $editable;

	/**
	 * @var bool Tells if the the order has been delivered
	 */
	public $receipt_state;

	/**
	 * @var bool Tells if the the order is in a state corresponding to a product pending receipt
	 */
	public $pending_receipt;

	/**
	 * @var string Display state in the specified color (Ex. #FFFF00)
	 */
	public $color;

	protected $fieldsValidate = array(
		'delivery_note' => 'isBool',
		'editable' => 'isBool',
		'receipt_state' => 'isBool',
		'pending_receipt' => 'isBool',
		'color' => 'isColor'
	);

	protected $fieldsRequiredLang = array('name');
 	protected $fieldsSizeLang = array('name' => 128);
 	protected $fieldsValidateLang = array('name' => 'isGenericName');

	/**
	 * @var string Database table name
	 */
	protected $table = 'supplier_order_state';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supplier_order_state';

	/**
	 * @see ObjectModel::getFields()
	 */
	public function getFields()
	{
		$this->validateFields();
		$fields['delivery_note'] = (bool)$this->delivery_note;
		$fields['editable'] = (bool)$this->editable;
		$fields['receipt_state'] = (bool)$this->receipt_state;
		$fields['pending_receipt'] = (bool)$this->pending_receipt;
		$fields['color'] = pSQL($this->color);

		return $fields;
	}

	/**
	 * @see ObjectModel::getTranslationsFieldsChild()
	 */
	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array('name'));
	}

	/**
	 * Gets the list of supplier order states
	 *
	 * @param int $id_lang The language id
	 * @return array
	 */
	public static function getSupplierOrderStates($id_lang = 0)
	{
		if ($id_lang == 0)
			$id_lang = Context::getContext()->language->id;

		$query = new DbQuery();
		$query->select('sl.name, s.id_supplier_order_state');
		$query->from('supplier_order_state s');
		$query->leftjoin('supplier_order_state_lang sl ON (s.id_supplier_order_state = sl.id_supplier_order_state AND sl.id_lang='.(int)$id_lang.')');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
}