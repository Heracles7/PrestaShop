<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminImagesControllerCore extends AdminController
{
	protected $start_time = 0;
	protected $max_execution_time = 7200;

	public function __construct()
	{
		$this->table = 'image_type';
		$this->className = 'ImageType';
		$this->lang = false;

	 	$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fields_list = array(
			'id_image_type' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'size' => 16),
			'width' => array('title' => $this->l('Width'), 'align' => 'right', 'suffix' => ' px', 'width' => 50, 'size' => 5),
			'height' => array('title' => $this->l('Height'), 'align' => 'right', 'suffix' => ' px', 'width' => 50, 'size' => 5)
		);

		$this->fields_options = array(
			'images' => array(
				'title' =>	$this->l('Images'),
				'icon' =>	'tab-orders',
				'top' => '',
				'bottom' => '',
				'description' => $this->l('JPEG images have a small file size and standard quality. PNG images have a bigger file size, a higher quality and support transparency. Note that in all cases the image files will have the .jpg extension.').'
								  <br /><br />'.$this->l('WARNING: This feature may not be compatible with your theme or with some modules. In particular, PNG mode is not compatible with the Watermark module. If you encounter any issues, turn it off by selecting "Use JPEG".'),
				'fields' =>	array(
					'PS_IMAGE_QUALITY' => array(
						'title' => $this->l('Image quality'),
						'show' => true,
						'required' => true,
						'type' => 'radio',
						'choices' => array('jpg' => $this->l('Use JPEG'), 'png' => $this->l('Use PNG only if the base image is in PNG format'), 'png_all' => $this->l('Use PNG for all images'))
					),
					'PS_JPEG_QUALITY' => array(
						'title' => $this->l('JPEG quality'),
						'desc' => $this->l('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file)'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text'
					),
		 			'PS_PNG_QUALITY' => array(
						 'title' => $this->l('PNG quality'),
						 'desc' => $this->l('Ranges from 9 (worst quality, smallest file) to 0 (best quality, biggest file)'),
						 'validation' => 'isUnsignedId',
						 'required' => true,
						 'cast' => 'intval',
						 'type' => 'text'
					 ),
				),
				'submit' => array('name' => 'submitImagePreferences'),
			),
			'product_images' => array(
				'title' =>	$this->l('Product images'),
				'fields' =>	array(
					'PS_IMAGE_GENERATION_METHOD' => array(
						'title' => $this->l('Image generated by:'),
						'validation' => 'isUnsignedId',
						'required' => false,
						'cast' => 'intval',
						'type' => 'select',
						'list' => array(
							array(
								'id' => '0',
								'name' => $this->l('auto')
							),
							array(
								'id' => '1',
								'name' => $this->l('width')
							),
							array(
								'id' => '2',
								'name' => $this->l('height')
							)
						),
						'identifier' => 'id',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_PRODUCT_PICTURE_MAX_SIZE' => array(
						'title' => $this->l('Maximum size of product pictures:'),
						'desc' => $this->l('The maximum size of pictures uploadable by customers (in Bytes)'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text',
						'suffix' => $this->l('bytes'),
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_PRODUCT_PICTURE_WIDTH' => array(
						'title' => $this->l('Product picture width:'),
						'desc' => $this->l('The maximum width of pictures uploadable by customers'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text',
						'width' => 'px',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_PRODUCT_PICTURE_HEIGHT' => array(
						'title' => $this->l('Product picture height:'),
						'desc' => $this->l('The maximum height of pictures uploadable by customers'),
						'validation' => 'isUnsignedId',
						'required' => true,
						'cast' => 'intval',
						'type' => 'text',
						'height' => 'px',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_LEGACY_IMAGES' => array(
						'title' => $this->l('Use the legacy image filesystem:'),
						'desc' => $this->l('This should be set to yes unless you successfully moved images in Preferences > Images tab'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'required' => false,
						'type' => 'bool',
						'visibility' => Shop::CONTEXT_ALL
					),
				),
				'submit' => array('title' => $this->l('   Save   '), 'class' => 'button'),
			),
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Images'),
				'image' => '../img/admin/picture.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Type name:'),
					'name' => 'name',
					'required' => true,
					'desc' => $this->l('Letters and minus sign (-) only (e.g. small, medium, large, extra-large)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Width:'),
					'name' => 'width',
					'required' => true,
					'size' => 4,
					'maxlength' => 5,
					'desc' => $this->l('Maximum image width in pixels')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Height:'),
					'name' => 'height',
					'required' => true,
					'size' => 4,
					'maxlength' => 5,
					'desc' => $this->l('Maximum image height in pixels')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Products:'),
					'name' => 'products',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be used for product images'),
					'values' => array(
						array(
							'id' => 'products_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'products_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Categories:'),
					'name' => 'categories',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be applied to Category images'),
					'values' => array(
						array(
							'id' => 'categories_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'categories_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Manufacturers:'),
					'name' => 'manufacturers',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be applied to Manufacturer images'),
					'values' => array(
						array(
							'id' => 'manufacturers_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'manufacturers_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Suppliers:'),
					'name' => 'suppliers',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be used for Supplier images'),
					'values' => array(
						array(
							'id' => 'suppliers_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'suppliers_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Scenes:'),
					'name' => 'scenes',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be used for scene images'),
					'values' => array(
						array(
							'id' => 'scenes_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'scenes_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Stores:'),
					'name' => 'stores',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('This type will be used for Store images'),
					'values' => array(
						array(
							'id' => 'stores_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'stores_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						),
					)
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
		// When moving images, if duplicate images were found they are moved to a folder named duplicates/
		if (file_exists(_PS_PROD_IMG_DIR_.'duplicates/'))
		{
			$this->warnings[] = $this->l('Duplicate images were found when moving the product images. It is likely caused by unused demonstration images. Please make sure that the folder ').
				_PS_PROD_IMG_DIR_.'duplicates/'.
				$this->l('only contains demonstration images, and then delete this folder.');
		}

		if (Tools::getValue('submitRegenerate'.$this->table))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if ($this->_regenerateThumbnails(Tools::getValue('type'), Tools::getValue('erase')))
					Tools::redirectAdmin(self::$currentIndex.'&conf=9'.'&token='.$this->token);
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}elseif (Tools::getValue('submitMoveImages'.$this->table))
		{
			if ($this->tabAccess['edit'] === '1')
		 	{
				if ($this->_moveImagesToNewFileSystem())
					Tools::redirectAdmin(self::$currentIndex.'&conf=25'.'&token='.$this->token);
		 	}
		else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}elseif (Tools::getValue('submitImagePreferences'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if ((int)Tools::getValue('PS_JPEG_QUALITY') < 0
					|| (int)Tools::getValue('PS_JPEG_QUALITY') > 100)
					$this->errors[] = Tools::displayError('Incorrect value for JPEG image quality.');
				elseif ((int)Tools::getValue('PS_PNG_QUALITY') < 0
					|| (int)Tools::getValue('PS_PNG_QUALITY') > 9)
					$this->errors[] = Tools::displayError('Incorrect value for PNG image quality.');
				elseif (!Configuration::updateValue('PS_IMAGE_QUALITY', Tools::getValue('PS_IMAGE_QUALITY'))
					|| !Configuration::updateValue('PS_JPEG_QUALITY', Tools::getValue('PS_JPEG_QUALITY'))
					|| !Configuration::updateValue('PS_PNG_QUALITY', Tools::getValue('PS_PNG_QUALITY')))
					$this->errors[] = Tools::displayError('Unknown error.');
				else
					Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		else
			parent::postProcess();
	}

	protected function _childValidation()
	{
		if (!Tools::getValue('id_image_type') && Validate::isImageTypeName($typeName = Tools::getValue('name')) && ImageType::typeAlreadyExists($typeName))
			$this->errors[] = Tools::displayError('This name already exists.');
	}

	/**
	  * Init display for the thumbnails regeneration block
	  */
	public function initRegenerate()
	{
		$types = array(
			'categories' => $this->l('Categories'),
			'manufacturers' => $this->l('Manufacturers'),
			'suppliers' => $this->l('Suppliers'),
			'scenes' => $this->l('Scenes'),
			'products' => $this->l('Products'),
			'stores' => $this->l('Stores')
		);

		$formats = array();
		foreach ($types as $i => $type)
			$formats[$i] = ImageType::getImagesTypes($i);

		$this->context->smarty->assign(array(
			'types' => $types,
			'formats' => $formats,
		));
	}

	/**
	  * Delete resized image then regenerate new one with updated settings
	  */
	protected function _deleteOldImages($dir, $type, $product = false)
	{
		if (!is_dir($dir))
			return false;
		$toDel = scandir($dir);
		foreach ($toDel as $d)
			foreach ($type as $imageType)
				if (preg_match('/^[0-9]+\-'.($product ? '[0-9]+\-' : '').$imageType['name'].'\.jpg$/', $d) || preg_match('/^([[:lower:]]{2})\-default\-(.*)\.jpg$/', $d))
					if (file_exists($dir.$d))
						unlink($dir.$d);

		// delete product images using new filesystem.
		if ($product)
		{
			$productsImages = Image::getAllImages();
			foreach ($productsImages as $image)
			{
				$imageObj = new Image($image['id_image']);
				$imageObj->id_product = $image['id_product'];
				if (file_exists($dir.$imageObj->getImgFolder()))
				{
					$toDel = scandir($dir.$imageObj->getImgFolder());
					foreach ($toDel as $d)
						foreach ($type as $imageType)
							if (preg_match('/^[0-9]+\-'.$imageType['name'].'\.jpg$/', $d))
								if (file_exists($dir.$imageObj->getImgFolder().$d))
									unlink($dir.$imageObj->getImgFolder().$d);
	}
			}
		}
	}

	/**
	 * Regenerate images
	 *
	 * @param $dir
	 * @param $type
	 * @param bool $productsImages
	 * @return bool|string
	 */
	protected function _regenerateNewImages($dir, $type, $productsImages = false)
	{
		if (!is_dir($dir))
			return false;
		$errors = false;
		$toRegen = scandir($dir);
		if (!$productsImages)
		{
			foreach ($toRegen as $image)
				if (preg_match('/^[0-9]*\.jpg$/', $image))
					foreach ($type as $k => $imageType)
					{
						// Customizable writing dir
						$newDir = $dir;
						if ($imageType['name'] == 'thumb_scene')
							$newDir .= 'thumbs/';
						if (!file_exists($newDir))
							continue;
						if (!file_exists($newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'.jpg'))
							if (!ImageManager::resize($dir.$image, $newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height'])))
								$errors = true;
						if (time() - $this->start_time > $this->max_execution_time - 4) // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
							return 'timeout';
					}
		}
		else
		{
			$productsImages = Image::getAllImages();
			foreach ($productsImages as $image)
			{
				$imageObj = new Image($image['id_image']);
				if (file_exists($dir.$imageObj->getExistingImgPath().'.jpg'))
					foreach ($type as $imageType)
					{
						if (!file_exists($dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg'))
							if (!ImageManager::resize($dir.$imageObj->getExistingImgPath().'.jpg', $dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height'])))
								$errors = true;
						if (time() - $this->start_time > $this->max_execution_time - 4) // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
							return 'timeout';
					}
			}
		}

		return $errors;
	}

	/**
	 * Regenerate no-pictures images
	 *
	 * @param $dir
	 * @param $type
	 * @param $languages
	 * @return bool
	 */
	protected function _regenerateNoPictureImages($dir, $type, $languages)
	{
		$errors = false;
		foreach ($type as $imageType)
		{
			foreach ($languages as $language)
			{
				$file = $dir.$language['iso_code'].'.jpg';
				if (!file_exists($file))
					$file = _PS_PROD_IMG_DIR_.Language::getIsoById((int)(Configuration::get('PS_LANG_DEFAULT'))).'.jpg';
				if (!file_exists($dir.$language['iso_code'].'-default-'.stripslashes($imageType['name']).'.jpg'))
					if (!ImageManager::resize($file, $dir.$language['iso_code'].'-default-'.stripslashes($imageType['name']).'.jpg', (int)$imageType['width'], (int)$imageType['height']))
						$errors = true;
			}
		}
		return $errors;
	}

	/* Hook watermark optimization */
	protected function _regenerateWatermark($dir)
	{
		$result = Db::getInstance()->executeS('
		SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \'watermark\' AND m.`active` = 1');

		if ($result && count($result))
		{
			$productsImages = Image::getAllImages();
			foreach ($productsImages as $image)
			{
				$imageObj = new Image($image['id_image']);
				if (file_exists($dir.$imageObj->getExistingImgPath().'.jpg'))
					foreach ($result as $module)
					{
						if ($moduleInstance = Module::getInstanceByName($module['name']) && is_callable(array($moduleInstance, 'hookwatermark')))
							call_user_func(array($moduleInstance, 'hookwatermark'), array('id_image' => $imageObj->id, 'id_product' => $imageObj->id_product));
						if (time() - $this->start_time > $this->max_execution_time - 4) // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
							return 'timeout';
					}
		}
	}
	}

	protected function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
	{
		$this->start_time = time();
		ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
		$this->max_execution_time = (int)ini_get('max_execution_time');
		$languages = Language::getLanguages(false);

		$process =
			array(
				array('type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_),
				array('type' => 'manufacturers', 'dir' => _PS_MANU_IMG_DIR_),
				array('type' => 'suppliers', 'dir' => _PS_SUPP_IMG_DIR_),
				array('type' => 'scenes', 'dir' => _PS_SCENE_IMG_DIR_),
				array('type' => 'products', 'dir' => _PS_PROD_IMG_DIR_),
				array('type' => 'stores', 'dir' => _PS_STORE_IMG_DIR_)
			);

		// Launching generation process
		foreach ($process as $proc)
		{
			if ($type != 'all' && $type != $proc['type'])
				continue;

			// Getting format generation
			$formats = ImageType::getImagesTypes($proc['type']);
			if ($type != 'all')
			{
				$format = strval(Tools::getValue('format_'.$type));
				if ($format != 'all')
					foreach ($formats as $k => $form)
						if ($form['id_image_type'] != $format)
							unset($formats[$k]);
			}

			if ($deleteOldImages)
				$this->_deleteOldImages($proc['dir'], $formats, ($proc['type'] == 'products' ? true : false));
			if (($return = $this->_regenerateNewImages($proc['dir'], $formats, ($proc['type'] == 'products' ? true : false))) === true)
				$this->errors[] = Tools::displayError('Cannot write').$proc['type'].Tools::displayError('images. Please check the folder\'s writing permissions.');
			elseif ($return == 'timeout')
				$this->errors[] = Tools::displayError('Only part of the images have been regenerated, server timed out before finishing.');
			else
			{
				if ($proc['type'] == 'products')
					if ($this->_regenerateWatermark($proc['dir']) == 'timeout')
						$this->errors[] = Tools::displayError('Server timed out, the watermark may not have been applied to all images.');
				if (!count($this->errors))
					if ($this->_regenerateNoPictureImages($proc['dir'], $formats, $languages))
						$this->errors[] = Tools::displayError('Cannot write "No picture" image to').' ('.$proc['type'].') '.Tools::displayError('images folder. Please check the folder\'s writing permissions.');
			}
		}
		return (count($this->errors) > 0 ? false : true);
	}

	/**
	 * Init display for move images block
	 */
	public function initMoveImages()
	{
		$this->context->smarty->assign(array(
			'safe_mode' => ini_get('safe_mode'),
			'link_ppreferences' => 'index.php?tab=AdminPPreferences&token='.Tools::getAdminTokenLite('AdminPPreferences').'#PS_LEGACY_IMAGES_on',
		));
}

	/**
	 * Move product images to the new filesystem
	 */
	protected function _moveImagesToNewFileSystem()
	{
		if (!Image::testFileSystem())
			$this->errors[] = Tools::displayError('Error: your server configuration is not compatible with the new image system. No images were moved');
		else
		{
			ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
			$this->max_execution_time = (int)ini_get('max_execution_time');
			$result = Image::moveToNewFileSystem($this->max_execution_time);
			if ($result === 'timeout')
				$this->errors[] = Tools::displayError('Not all images have been moved, server timed out before finishing. Click on "Move images" again to resume moving images');
			else if ($result === false)
				$this->errors[] = Tools::displayError('Error: some or all images could not be moved.');
		}
		return (count($this->errors) > 0 ? false : true);
	}

	public function initContent()
	{
		if ($this->display != 'edit' && $this->display != 'add')
		{
			$this->initRegenerate();
			$this->initMoveImages();

			$this->context->smarty->assign(array(
				'display_regenerate' => true,
				'display_move' => true
			));
		}

		if ($this->display == 'edit')
			$this->warnings[] = $this->l('After modification, do not forget to regenerate thumbnails');

		parent::initContent();
	}
}
