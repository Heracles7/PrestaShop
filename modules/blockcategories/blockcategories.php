<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockCategories extends Module
{
	public function __construct()
	{
		$this->name = 'blockcategories';
		$this->tab = 'front_office_features';
		$this->version = '2.1';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Categories block');
		$this->description = $this->l('Adds a block featuring product categories.');
	}

	public function install()
	{
		if (!parent::install() ||
			!$this->registerHook('leftColumn') ||
			!$this->registerHook('footer') ||
			!$this->registerHook('header') ||
			// Temporary hooks. Do NOT hook any module on it. Some CRUD hook will replace them as soon as possible.
			!$this->registerHook('categoryAddition') ||
			!$this->registerHook('categoryUpdate') ||
			!$this->registerHook('categoryDeletion') ||
			!$this->registerHook('actionAdminMetaControllerUpdate_optionsBefore') ||
			!$this->registerHook('actionAdminLanguagesControllerStatusBefore') ||
			!$this->registerHook('displayBackOfficeCategory') ||
			!$this->registerHook('actionBackOfficeCategory') ||
			!Configuration::updateValue('BLOCK_CATEG_MAX_DEPTH', 4) ||
			!Configuration::updateValue('BLOCK_CATEG_DHTML', 1))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
			!Configuration::deleteByName('BLOCK_CATEG_MAX_DEPTH') ||
			!Configuration::deleteByName('BLOCK_CATEG_DHTML'))
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockCategories'))
		{
			$maxDepth = (int)(Tools::getValue('BLOCK_CATEG_MAX_DEPTH'));
			$dhtml = Tools::getValue('BLOCK_CATEG_DHTML');
			$nbrColumns = Tools::getValue('BLOCK_CATEG_NBR_COLUMN_FOOTER', 4);
			if ($maxDepth < 0)
				$output .= $this->displayError($this->l('Maximum depth: Invalid number.'));
			elseif ($dhtml != 0 && $dhtml != 1)
				$output .= $this->displayError($this->l('Dynamic HTML: Invalid choice.'));
			else
			{
				Configuration::updateValue('BLOCK_CATEG_MAX_DEPTH', (int)$maxDepth);
				Configuration::updateValue('BLOCK_CATEG_DHTML', (int)$dhtml);
				Configuration::updateValue('BLOCK_CATEG_NBR_COLUMN_FOOTER', (int)$nbrColumns);
				Configuration::updateValue('BLOCK_CATEG_SORT_WAY', Tools::getValue('BLOCK_CATEG_SORT_WAY'));
				Configuration::updateValue('BLOCK_CATEG_SORT', Tools::getValue('BLOCK_CATEG_SORT'));

				$this->_clearBlockcategoriesCache();
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}

	public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();

		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (!isset($resultIds[$id_category]))
			return false;
		$return = array('id' => $id_category, 'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
					 'name' => $resultIds[$id_category]['name'], 'desc'=> $resultIds[$id_category]['description'],
					 'children' => $children);
		return $return;
	}

	public function hookDisplayBackOfficeCategory($params)
	{
		$category = new Category((int)Tools::getValue('id_category'));

		if ($category->level_depth != 2)
			return;

		for ($i=0;$i<3;$i++)
			$images[$i] = ImageManager::thumbnail(_PS_CAT_IMG_DIR_.(int)$category->id.'-'.$i.'_thumb.jpg', $this->context->controller->table.'_'.(int)$category->id.'-'.$i.'_thumb.jpg', 100, 'jpg', true);

		$this->smarty->assign(array(
			'name'    => 'thumb',
			'images'  => $images
		));	
		
		return $this->display(__FILE__, 'views/blockcategories_admin.tpl');
	}

	public function hookActionBackOfficeCategory($params)
	{
		$total_errors = array();

		for ($i=0; $i<3; $i++)
			if (isset($_FILES['thumb-'.$i]) && $_FILES['thumb-'.$i]['size'] > 0)
			{
				$errors = array();
				// Check image validity
				$max_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');

				if ($error = ImageManager::validateUpload($_FILES['thumb-'.$i], Tools::getMaxUploadSize($max_size)))
					$errors[] = $error;

				$tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');

				if (!$tmp_name)
					$errors[] = Tools::displayError('Invalid Temporary directory');

				if (!move_uploaded_file($_FILES['thumb-'.$i]['tmp_name'], $tmp_name))
					$errors[] = Tools::displayError('Error uploading thumbnail image');

				// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
				if (!ImageManager::checkImageMemoryLimit($tmp_name))
					$errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');

				// Copy new image
				if (empty($errors) && !ImageManager::resize($tmp_name, _PS_CAT_IMG_DIR_.(int)Tools::getValue('id_category').'-'.$i.'_thumb.jpg'))
					$errors[] = Tools::displayError('An error occurred while uploading the image.');

				if (count($errors))
					$total_errors = array_merge($total_errors, $errors);

				unlink($tmp_name);
			}

		if (($id_thumb = Tools::getValue('deleteThumb', false)) !== false)
		{
			if (file_exists(_PS_CAT_IMG_DIR_.(int)Tools::getValue('id_category').'-'.(int)$id_thumb.'_thumb.jpg')
				&& !unlink(_PS_CAT_IMG_DIR_.(int)Tools::getValue('id_category').'-'.(int)$id_thumb.'_thumb.jpg'))
				$this->context->controller->errors[] = Tools::displayError('Error while delet');
		}

		if (count($total_errors))
			$this->context->controller->errors = array_merge($this->context->controller->errors, $total_errors);
	}

	public function hookLeftColumn($params)
	{	
		if (!$this->isCached('blockcategories.tpl', $this->getCacheId()))
		{
			// Get all groups for this customer and concatenate them as a string: "1,2,3..."
			$groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
			$maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
			if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
				FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
				INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
				WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
				AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
				'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
				AND c.id_category IN (SELECT id_category FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` IN ('.pSQL($groups).'))
				ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC')))
				return;

			$resultParents = array();
			$resultIds = array();

			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}

			$blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEG_MAX_DEPTH'));
			unset($resultParents, $resultIds);

			$id_category = (int)Tools::getValue('id_category');
			$id_product = (int)Tools::getValue('id_product');
			
			$isDhtml = (Configuration::get('BLOCK_CATEG_DHTML') == 1 ? true : false);
			if (Tools::isSubmit('id_category'))
			{
				$this->context->cookie->last_visited_category = $id_category;
				$this->smarty->assign('currentCategoryId', $this->context->cookie->last_visited_category);
			}
			if (Tools::isSubmit('id_product'))
			{
				if (!isset($this->context->cookie->last_visited_category)
					|| !Product::idIsOnCategoryId($id_product, array('0' => array('id_category' => $this->context->cookie->last_visited_category)))
					|| !Category::inShopStatic($this->context->cookie->last_visited_category, $this->context->shop))
				{
					$product = new Product($id_product);
					if (isset($product) && Validate::isLoadedObject($product))
						$this->context->cookie->last_visited_category = (int)$product->id_category_default;
				}
				$this->smarty->assign('currentCategoryId', (int)$this->context->cookie->last_visited_category);
			}
			$this->smarty->assign('blockCategTree', $blockCategTree);

			if (file_exists(_PS_THEME_DIR_.'modules/blockcategories/blockcategories.tpl'))
				$this->smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategories/category-tree-branch.tpl');
			else
				$this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategories/category-tree-branch.tpl');
			$this->smarty->assign('isDhtml', $isDhtml);
		}
		$display = $this->display(__FILE__, 'blockcategories.tpl', $this->getCacheId());
		return $display;
	}

	protected function getCacheId($name = null)
	{
		parent::getCacheId($name);

		$groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
		$id_product = (int)Tools::getValue('id_product', 0);
		$id_category = (int)Tools::getValue('id_category', 0);
		$id_lang = (int)$this->context->language->id;
		return 'blockcategories|'.(int)Tools::usingSecureMode().'|'.$this->context->shop->id.'|'.$groups.'|'.$id_lang.'|'.$id_product.'|'.$id_category;
	}

	public function hookFooter($params)
	{
		// Get all groups for this customer and concatenate them as a string: "1,2,3..."
		if (!$this->isCached('blockcategories_footer.tpl', $this->getCacheId()))
		{
			$maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
			$groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
			if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
				FROM `'._DB_PREFIX_.'category` c
				'.Shop::addSqlAssociation('category', 'c').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
				WHERE (c.`active` = 1 OR c.`id_category` = 1)
				'.((int)($maxdepth) != 0 ? ' AND `level_depth` <= '.(int)($maxdepth) : '').'
				AND cg.`id_group` IN ('.pSQL($groups).')
				ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'category_shop.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC')))
				return;
			$resultParents = array();
			$resultIds = array();

			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}
			//$nbrColumns = Configuration::get('BLOCK_CATEG_NBR_COLUMNS_FOOTER');
			$nbrColumns = Configuration::get('BLOCK_CATEG_NBR_COLUMN_FOOTER');
			if (!$nbrColumns)
				$nbrColumns = 3;
			$numberColumn = abs(count($result) / $nbrColumns);
			$widthColumn = floor(100 / $nbrColumns);
			$this->smarty->assign('numberColumn', $numberColumn);
			$this->smarty->assign('widthColumn', $widthColumn);

			$blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEG_MAX_DEPTH'));
			unset($resultParents, $resultIds);

			$isDhtml = (Configuration::get('BLOCK_CATEG_DHTML') == 1 ? true : false);

			$id_category = (int)Tools::getValue('id_category');
			$id_product = (int)Tools::getValue('id_product');

			if (Tools::isSubmit('id_category'))
			{
				$this->context->cookie->last_visited_category = $id_category;
				$this->smarty->assign('currentCategoryId', $this->context->cookie->last_visited_category);
			}
			if (Tools::isSubmit('id_product'))
			{
				if (!isset($this->context->cookie->last_visited_category) || !Product::idIsOnCategoryId($id_product, array('0' => array('id_category' => $this->context->cookie->last_visited_category))))
				{
					$product = new Product($id_product);
					if (isset($product) && Validate::isLoadedObject($product))
						$this->context->cookie->last_visited_category = (int)($product->id_category_default);
				}
				$this->smarty->assign('currentCategoryId', (int)($this->context->cookie->last_visited_category));
			}
			$this->smarty->assign('blockCategTree', $blockCategTree);

			if (file_exists(_PS_THEME_DIR_.'modules/blockcategories/blockcategories_footer.tpl'))
				$this->smarty->assign('branche_tpl_path', _PS_THEME_DIR_.'modules/blockcategories/category-tree-branch.tpl');
			else
				$this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'blockcategories/category-tree-branch.tpl');
			$this->smarty->assign('isDhtml', $isDhtml);
		}
		$display = $this->display(__FILE__, 'blockcategories_footer.tpl', $this->getCacheId());

		return $display;
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookHeader()
	{
		$this->context->controller->addJS(_THEME_JS_DIR_.'tools/treeManagement.js');
		$this->context->controller->addCSS(($this->_path).'blockcategories.css', 'all');
	}

	private function _clearBlockcategoriesCache()
	{
		$this->_clearCache('blockcategories.tpl');
		$this->_clearCache('blockcategories_footer.tpl');
	}

	public function hookCategoryAddition($params)
	{
		$this->_clearBlockcategoriesCache();
	}

	public function hookCategoryUpdate($params)
	{
		$this->_clearBlockcategoriesCache();
	}

	public function hookCategoryDeletion($params)
	{
		$this->_clearBlockcategoriesCache();
	}

	public function hookActionAdminMetaControllerUpdate_optionsBefore($params)
	{
		$this->_clearBlockcategoriesCache();
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Maximum depth'),
						'name' => 'BLOCK_CATEG_MAX_DEPTH',
						'desc' => $this->l('Set the maximum depth of sublevels displayed in this block (0 = infinite)'),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Dynamic'),
						'name' => 'BLOCK_CATEG_DHTML',
						'desc' => $this->l('Activate dynamic (animated) mode for sublevels.'),
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
								),
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Sort'),
						'name' => 'BLOCK_CATEG_SORT',
						'values' => array(
							array(
								'id' => 'name',
								'value' => 1,
								'label' => $this->l('By name')
							),
							array(
								'id' => 'position',
								'value' => 0,
								'label' => $this->l('By position')
							),
						)
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Sort order'),
						'name' => 'BLOCK_CATEG_SORT_WAY',
						'values' => array(
							array(
								'id' => 'name',
								'value' => 1,
								'label' => $this->l('Descending')
							),
							array(
								'id' => 'position',
								'value' => 0,
								'label' => $this->l('Ascending')
							),
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('How many footer columns would you like?'),
						'name' => 'BLOCK_CATEG_NBR_COLUMN_FOOTER',
					),
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockCategories';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'BLOCK_CATEG_MAX_DEPTH' => Tools::getValue('BLOCK_CATEG_MAX_DEPTH', Configuration::get('BLOCK_CATEG_MAX_DEPTH')),
			'BLOCK_CATEG_DHTML' => Tools::getValue('BLOCK_CATEG_DHTML', Configuration::get('BLOCK_CATEG_DHTML')),
			'BLOCK_CATEG_NBR_COLUMN_FOOTER' => Tools::getValue('BLOCK_CATEG_NBR_COLUMN_FOOTER', Configuration::get('BLOCK_CATEG_NBR_COLUMN_FOOTER')),
			'BLOCK_CATEG_SORT_WAY' => Tools::getValue('BLOCK_CATEG_SORT_WAY', Configuration::get('BLOCK_CATEG_SORT_WAY')),
			'BLOCK_CATEG_SORT' => Tools::getValue('BLOCK_CATEG_SORT', Configuration::get('BLOCK_CATEG_SORT')),
		);
	}
}
