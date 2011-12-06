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
*  @version  Release: $Revision: 7300 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSuppliersControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'supplier';
	 	$this->className = 'Supplier';

		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_select = 'COUNT(p.`id_product`) AS products';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product` p ON (a.`id_supplier` = p.`id_supplier`)';
		$this->_group = 'GROUP BY a.`id_supplier`';

 		$this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');

		$this->fieldsDisplay = array(
			'id_supplier' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'logo' => array('title' => $this->l('Logo'), 'width' => 150, 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
			'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
			'products' => array('title' => $this->l('Number of products'), 'width' => 70, 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true),
			'active' => array('title' => $this->l('Enabled'), 'width' => 70, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Suppliers'),
				'image' => '../img/admin/suppliers.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 40,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'size' => 33,
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Will appear in supplier list')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'size' => 15,
					'maxlength' => 16,
					'desc' => $this->l('Phone number of this supplier')
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
					'size' => 20,
					'maxlength' => 32,
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
					'desc' => $this->l('Country where the state, region or city is located')
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
					'type' => 'file',
					'label' => $this->l('Logo:'),
					'name' => 'logo',
					'display_image' => true,
					'desc' => $this->l('Upload supplier logo from your computer')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title:'),
					'name' => 'smeta_title',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords:'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Enable:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'group_shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
				'values' => Shop::getTree()
			);
		}

		// Set logo image
		$image = cacheImage(_PS_SUPP_IMG_DIR_.'/'.$this->object->id.'.jpg', $this->table.'_'.(int)$this->object->id.'.'.$this->imageType, 350, $this->imageType, true);
		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_SUPP_IMG_DIR_.'/'.$this->object->id.'.jpg') / 1000 : false
		);

		return parent::renderForm();
	}

	public function renderView()
	{
		$products = $this->object->getProductsLite($this->context->language->id);
		$total_product = count($products);
		for ($i = 0; $i < $total_product; $i++)
		{
			$products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
			// Build attributes combinaisons
			$combinaisons = $products[$i]->getAttributeCombinaisons($this->context->language->id);
			foreach ($combinaisons as $k => $combinaison)
			{
				$comb_array[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
				$comb_array[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
				$comb_array[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
				$comb_array[$combinaison['id_product_attribute']]['quantity'] = $combinaison['quantity'];
				$comb_array[$combinaison['id_product_attribute']]['attributes'][] = array(
					$combinaison['group_name'],
					$combinaison['attribute_name'],
					$combinaison['id_attribute']
				);
			}

			if (isset($comb_array))
			{
				foreach ($comb_array as $key => $product_attribute)
				{
					$list = '';
					foreach ($product_attribute['attributes'] as $attribute)
						$list .= $attribute[0].' - '.$attribute[1].', ';
					$comb_array[$key]['attributes'] = rtrim($list, ', ');
				}
				isset($comb_array) ? $products[$i]->combinaison = $comb_array : '';
				unset($comb_array);
			}
		}

		$this->tpl_view_vars = array(
			'supplier' => $this->object,
			'products' => $products,
			'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
			'shopContext' => Context::getContext()->shop(),
		);

		return parent::renderView();
	}

	public function afterImageUpload()
	{
		/* Generate image with differents size */
		if (($id_supplier = (int)Tools::getValue('id_supplier')) AND
			 isset($_FILES) AND count($_FILES) AND file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('suppliers');
			foreach ($images_types as $k => $image_type)
			{
				$file = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
				imageResize($file, _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'.jpg', (int)$image_type['width'], (int)$image_type['height']);
			}
		}
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		// checks access
		if (Tools::isSubmit('submitAdd'.$this->table) && !($this->tabAccess['add'] === '1'))
		{
			$this->_errors[] = Tools::displayError('You do not have the required permissions to add suppliers.');
			return parent::postProcess();
		}

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if (!($obj = $this->loadObject(true)))
				return;

			// updates/creates address if it does not exist
			if (Tools::isSubmit('id_address') && (int)Tools::getValue('id_address') > 0)
				$address = new Address((int)Tools::getValue('id_address')); // updates address
			else
				$address = new Address(); // creates address

			$address->alias = Tools::getValue('name', null);
			$address->lastname = 'supplier'; // skip problem with numeric characters in supplier name
			$address->firstname = 'supplier'; // skip problem with numeric characters in supplier name
			$address->address1 = Tools::getValue('address', null);
			$address->address2 = Tools::getValue('address2', null);
			$address->postcode = Tools::getValue('postcode', null);
			$address->phone = Tools::getValue('phone', null);
			$address->id_country = Tools::getValue('id_country', null);
			$address->id_state = Tools::getValue('id_state', null);
			$address->city = Tools::getValue('city', null);

			$validation = $address->validateController();

			// checks address validity
			if (count($validation) > 0)
			{
				foreach ($validation as $item)
					$this->_errors[] = $item;
				$this->_errors[] = Tools::displayError('The address is not correct. Check if all required fields are filled.');
			}
			else
			{
				if (Tools::isSubmit('id_address') && Tools::getValue('id_address') > 0)
					$address->update();
				else
				{
					$address->save();
					$_POST['id_address'] = $address->id;
				}
			}
			return parent::postProcess();
		}
		else if (Tools::isSubmit('delete'.$this->table))
			if (!($obj = $this->loadObject(true)))
				return;
			else if (SupplyOrder::supplierHasPendingOrders($obj->id))
				$this->_errors[] = $this->l('It is not possible to delete a supplier if there is/are pending supply order(s).');
			else
			{
				$address = new Address($obj->id_address);
				$address->deleted = 1;
				$address->save();
				return parent::postProcess();
			}
	}

	/**
	 * @see AdminController::afterAdd()
	 */
	public function afterAdd($object)
	{
		$address = new Address($object->id_address);
		if (Validate::isLoadedObject($address))
		{
			$address->id_supplier = $object->id_address;
			$address->save();
		}
		return true;
	}

}

