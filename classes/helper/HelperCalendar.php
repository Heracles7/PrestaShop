<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperCalendarCore extends Helper
{
	const DEFAULT_DATE_FORMAT = 'dd-mm-yyyy';

	private $_actions;
	private $_compare_date_from;
	private $_compare_date_to;
	private $_date_format;
	private $_date_from;
	private $_date_to;

	public function __construct()
	{
		$this->base_folder = 'helpers/calendar/';
		$this->base_tpl = 'calendar.tpl';
		parent::__construct();
	}

	public function setActions($value)
	{
		if (!is_array($value) && !$value instanceof Traversable)
			throw new PrestaShopException('Actions value must be an traversable array');

		$this->_actions = $value;
		return $this;
	}

	public function getActions()
	{
		if (!isset($this->_actions))
			$this->_actions = array();

		return $this->_actions;
	}

	public function setCompareDateFrom($value)
	{
		if (!is_string($value))
			throw new PrestaShopException('Date format must be string');

		$this->_compare_date_from = $value;
		return $this;
	}

	public function getCompareDateFrom()
	{
		if (!isset($this->_compare_date_from))
			$this->_compare_date_from = date('d-m-Y', strtotime("-31 days"));

		return $this->_compare_date_from;
	}

	public function setCompareDateTo($value)
	{
		if (!is_string($value))
			throw new PrestaShopException('Date format must be string');

		$this->_compare_date_to = $value;
		return $this;
	}

	public function getCompareDateTo()
	{
		if (!isset($this->_compare_date_to))
			$this->_compare_date_to = date('d-m-Y');

		return $this->_compare_date_to;
	}

	public function setDateFormat($value)
	{
		if (!is_string($value))
			throw new PrestaShopException('Date format must be string');

		$this->_date_format = $value;
		return $this;
	}

	public function getDateFormat()
	{
		if (!isset($this->_date_format))
			$this->_date_format = self::DEFAULT_DATE_FORMAT;

		return $this->_date_format;
	}

	public function setDateFrom($value)
	{
		if (!is_string($value))
			throw new PrestaShopException('Date format must be string');

		$this->_date_from = $value;
		return $this;
	}

	public function getDateFrom()
	{
		if (!isset($this->_date_from))
			$this->_date_from = date('d-m-Y', strtotime("-31 days"));

		return $this->_date_from;
	}

	public function setDateTo($value)
	{
		if (!is_string($value))
			throw new PrestaShopException('Date format must be string');

		$this->_date_to = $value;
		return $this;
	}

	public function getDateTo()
	{
		if (!isset($this->_date_to))
			$this->_date_to = date('d-m-Y');

		return $this->_date_to;
	}

	public function addAction($action)
	{
		if (!isset($this->_actions))
			$this->_actions = array();

		$this->_actions[] = $action;

		return $this;
	}

	public function generate()
	{
		$context =  Context::getContext();
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$bo_theme = ((Validate::isLoadedObject($context->employee)
			&& $context->employee->bo_theme) ? $context->employee->bo_theme : 'default');

		if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
			.'template'))
			$bo_theme = 'default';

		if ($context->controller->ajax)
			$html = '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/date-range-picker.js"></script>';
		else
		{
			$html = '';
			$context->controller->addJs(__PS_BASE_URI__.$admin_webpath
				.'/themes/'.$bo_theme.'/js/date-range-picker.js');
		}

		$this->tpl = $this->createTemplate($this->base_tpl);
		$this->tpl->assign(array(
			'date_format'       => $this->getDateFormat(),
			'date_from'         => $this->getDateFrom(),
			'date_to'           => $this->getDateTo(),
			'compare_date_from' => $this->getCompareDateFrom(),
			'compare_date_to'   => $this->getCompareDateTo(),
			'actions'           => $this->getActions()
		));

		$html .= parent::generate();
		return $html;
	}
}
