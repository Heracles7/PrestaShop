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
*  @version  Release: $Revision: 7307 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminWarehousesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'warehouse';
	 	$this->className = 'Warehouse';
		$this->context = Context::getContext();
		$this->lang = false;

		$this->addRowAction('edit');

	 	if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldsDisplay = array(
			'reference' => array('title' => $this->l('Reference'), 'width' => 40),
			'name' => array('title' => $this->l('Name'), 'width' => 300, 'havingFilter' => true),
			'management_type' => array('title' => $this->l('Managment type'), 'width' => 40),
			'employee' => array('title' => $this->l('Manager'), 'width' => 150, 'havingFilter' => true),
			'location' => array('title' => $this->l('Location'), 'width' => 150),
			'contact' => array('title' => $this->l('Phone Number'), 'width' => 50),
		);

		$this->_select = 'reference, name, management_type,
							CONCAT(e.lastname, \' \', e.firstname) AS employee,
							ad.phone AS contact,
							CONCAT(ad.city, \' \', c.iso_code) location';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
						LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.id_address = a.id_address)
						LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.id_country = ad.id_country)';

		// Get employee list for warehouse manager
		$query = new DbQuery();
		$query->select('id_employee, CONCAT(lastname," ",firstname) as name');
		$query->from('employee');
		$query->where('active = 1');
		$this->employees_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Warehouse'),
				'image' => '../img/admin/tab.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_address',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Reference:'),
					'name' => 'reference',
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'p' => $this->l('Code / Reference of this warehouse'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 40,
					'maxlength' => 45,
					'required' => true,
					'p' => $this->l('Name of this warehouse')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'size' => 15,
					'maxlength' => 16,
					'p' => $this->l('Phone number of this warehouse')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Adress:'),
					'name' => 'address',
					'size' => 100,
					'maxlength' => 128,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Adress:').' (2)',
					'name' => 'address2',
					'size' => 100,
					'maxlength' => 128,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postcode/ Zip Code:'),
					'name' => 'postcode',
					'size' => 10,
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('City:'),
					'name' => 'city',
					'size' => 10,
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'required' => true,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id, false),
						'id' => 'id_country',
						'name' => 'name'
					),
					'p' => $this->l('Country where state, region or city is located')
				),
				array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'required' => true,
					'options' => array(
						'id' => 'id_state',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Manager:'),
					'name' => 'id_employee',
					'required' => true,
					'options' => array(
						'query' => $this->employees_array,
						'id' => 'id_employee',
						'name' => 'name'
					),
					'p' => $this->l('Manager of this warehouse')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Management type:'),
					'name' => 'management_type',
					'required' => true,
					'options' => array(
						'query' => array(
							array(
								'id' => 'WA',
								'name' => $this->l('Weight Average')
							),
							array(
								'id' => 'FIFO',
								'name' => $this->l('First In, First Out')
							),
							array(
								'id' => 'LIFO',
								'name' => $this->l('Last In, First Out')
							),
						),
						'id' => 'id',
						'name' => 'name'
					),
					'p' => $this->l('Onventory valuation method'),
					'hint' => $this->l('Do not change this value before the end of the accounting period for this Warehouse.'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Associated shops:'),
					'name' => 'ids_shops[]',
					'required' => true,
					'multiple' => true,
					'options' => array(
						'query' => Shop::getShops(),
						'id' => 'id_shop',
						'name' => 'name'
					),
					'p' => $this->l('Associated shops'),
					'hint' => $this->l('By associating a shop to a warehouse, all products in this warehouse will be available for sale in the associated shop. Shipment of an order of this shop is also possible from this warehouse'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Associated carriers:'),
					'name' => 'ids_carriers[]',
					'required' => true,
					'multiple' => true,
					'options' => array(
						'query' => Carrier::getCarriers($this->context->language->id, true),
						'id' => 'id_carrier',
						'name' => 'name'
					),
					'p' => $this->l('Associated carrier'),
					'hint' => $this->l('You can specifiy the carriers availables for shipping orders from this warehouse'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		parent::__construct();
	}

	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			if (!($obj = $this->loadObject(true)))
				return;

			//handle shops associations
			if (isset($_POST['ids_shops']))
				$obj->setShops($_POST['ids_shops']);

			//handle carriers associations
			if (isset($_POST['ids_carriers']))
				$obj->setCarriers($_POST['ids_carriers']);

			// update/create address if not exists
			if (isset($_POST['id_address']) && $_POST['id_address'] > 0)
				//update address
				$address = new Address((int)$_POST['id_address']);
			else
				//create address
				$address = new Address();

			$address->alias = $_POST['name'];
			$address->lastname = $_POST['name'];
			$address->firstname = $_POST['name'];
			$address->address1 = $_POST['address'];
			$address->address2 = $_POST['address2'];
			$address->postcode = $_POST['postcode'];
			$address->phone = $_POST['phone'];
			$address->id_country = $_POST['id_country'];
			$address->id_state = $_POST['id_state'];
			$address->city = $_POST['city'];

			if (isset($_POST['id_address']) && $_POST['id_address'] > 0)
			{
				//update address
				$address->update();
			}
			else {
				$address->save();
				$_POST['id_address'] = $address->id;
			}
		}

		return parent::postProcess();
	}

	public function initContent()
	{
		if ($this->display != 'edit' && $this->display != 'add')
			$this->display = 'list';
		else
		{
			//loas current warehouse
			if (!($obj = $this->loadObject(true)))
				return;

			//load current address for this warehouse if possible
			if ($obj->id_address > 0)
				$address = new Address($obj->id_address);

			//load current shops associated with this warehouse
			$shops = $obj->getShops();

			//load current carriers associated with this warehouse
			$carriers = $obj->getCarriers();

			//force specific fields values
			$this->fields_value = array(
				'phone' => $address->phone,
				'address' => $address->address1,
				'address2' => $address->address2,
				'postcode' => $address->postcode,
				'city' => $address->city,
				'id_country' => $address->id_country,
				'id_state' => $address->id_state,
				'ids_shops[]' => $shops,
				'ids_carriers[]' => $carriers,
			);
		}

		parent::initContent();
	}
}