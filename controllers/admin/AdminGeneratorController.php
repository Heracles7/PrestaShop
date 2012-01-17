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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminGeneratorControllerCore extends AdminController
{
	protected $options = array('array for auto display');

	public function __construct()
	{
		$this->ht_file = _PS_ROOT_DIR_.'/.htaccess';
		$this->rb_file = _PS_ROOT_DIR_.'/robots.txt';
		$this->sm_file = _PS_ROOT_DIR_.'/sitemap.xml';
		$this->rb_data = $this->getRobotsContent();

		return parent::__construct();
	}

	public function initContent()
	{
		$this->tpl_option_vars['checkConfiguration_ht'] = $this->checkConfiguration($this->ht_file);
		$this->tpl_option_vars['checkConfiguration_rb'] = $this->checkConfiguration($this->rb_file);
		$this->tpl_option_vars['ps_htaccess_cache_control'] = Configuration::get('PS_HTACCESS_CACHE_CONTROL');
		$this->tpl_option_vars['ps_rewriting_settings'] = Configuration::get('PS_REWRITING_SETTINGS');
		$this->tpl_option_vars['ps_htaccess_disable_multiviews'] = Configuration::get('PS_HTACCESS_DISABLE_MULTIVIEWS');

		parent::initContent();
	}

	public function checkConfiguration($file)
	{
		if (file_exists($file))
			return is_writable($file);
		return is_writable(dirname($file));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitHtaccess'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', (int)Tools::getValue('PS_HTACCESS_CACHE_CONTROL'));
				Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));
				Configuration::updateValue('PS_HTACCESS_DISABLE_MULTIVIEWS', (int)Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS'));
				if (Tools::generateHtaccess($this->ht_file, null, null, '', Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS')))
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				$this->errors[] = $this->l('Cannot write into file:').' <b>'.$this->ht_file.'</b><br />'.$this->l('Please check write permissions.');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitRobots'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!$write_fd = @fopen($this->rb_file, 'w'))
					$this->errors[] = sprintf(Tools::displayError('Cannot write into file: %s. Please check write permissions.'), $this->rb_file);
				else
				{
					// PS Comments
					fwrite($write_fd, "# robots.txt automaticaly generated by PrestaShop e-commerce open-source solution\n");
					fwrite($write_fd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n");
					fwrite($write_fd, "# This file is to prevent the crawling and indexing of certain parts\n");
					fwrite($write_fd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
					fwrite($write_fd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
					fwrite($write_fd, "# you save bandwidth and server resources.\n");
					fwrite($write_fd, "# For more information about the robots.txt standard, see:\n");
					fwrite($write_fd, "# http://www.robotstxt.org/wc/robots.html\n");

					//GoogleBot specific
					fwrite($write_fd, "# GoogleBot specific\n");
					fwrite($write_fd, "User-agent: Googlebot\n");
					foreach ($this->rb_data['GB'] as $gb)
						fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$gb."\n");

					// User-Agent
					fwrite($write_fd, "# All bots\n");
					fwrite($write_fd, "User-agent: *\n");

					// Directories
					fwrite($write_fd, "# Directories\n");
					foreach ($this->rb_data['Directories'] as $dir)
						fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$dir."\n");

					// Files
					fwrite($write_fd, "# Files\n");
					foreach ($this->rb_data['Files'] as $file)
						fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$file."\n");

					// Sitemap
					fwrite($write_fd, "# Sitemap\n");
					if (file_exists($this->sm_file))
						if (filesize($this->sm_file))
							fwrite(
								$write_fd,
								'Sitemap: '.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].__PS_BASE_URI__.'sitemap.xml'."\n"
							);
					fwrite($write_fd, "\n");

					fclose($write_fd);
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			} else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}

	public function getRobotsContent()
	{
		$tab = array();

		// Directories
		$tab['Directories'] = array('classes/', 'config/', 'download/', 'mails/', 'modules/', 'translations/', 'tools/', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')).'/');

		// Files
		$disallow_controllers = array(
			'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
			'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order', 'order-opc',
			'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
			'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'search', 'statistics','attachment', 'guest-tracking'
		);

		// Rewrite files
		$tab['Files'] = array();
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$sql = 'SELECT ml.url_rewrite
					FROM '._DB_PREFIX_.'meta m
					INNER JOIN '._DB_PREFIX_.'meta_lang ml ON ml.id_meta = m.id_meta
					WHERE m.page IN (\''.implode('\', \'', $disallow_controllers).'\')';
			if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
				foreach ($results as $row)
					$tab['Files'][] = $row['url_rewrite'];
		}

		$tab['GB'] = array(
			'*orderby=','*orderway=','*tag=','*id_currency=','*search_query=','*id_lang=','*back=','*utm_source=','*utm_medium=','*utm_campaign=','*n='
		);

		foreach ($disallow_controllers as $controller)
			$tab['GB'][] = '*controller='.$controller;

		return $tab;
	}
}
