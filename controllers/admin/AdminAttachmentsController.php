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

class AdminAttachmentsControllerCore extends AdminController
{

	private $product_attachements = array();

	public function __construct()
	{
	 	$this->table = 'attachment';
		$this->className = 'Attachment';
	 	$this->lang = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fieldsDisplay = array(
			'id_attachment' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name')
			),
			'file' => array(
				'title' => $this->l('File')
			)
		);

		parent::__construct();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Attachment'),
				'image' => '../img/t/AdminAttachments.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Filename:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'cols' => 40,
					'rows' => 10,
					'lang' => true,
				),
				array(
					'type' => 'file',
					'label' => $this->l('File:'),
					'name' => 'file',
					'desc' => $this->l('Upload file from your computer')
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		return parent::renderForm();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList((int)$id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		if (count($this->_list))
		{
			$this->product_attachements = Attachment::getProductAttached((int)$id_lang, $this->_list);

			$list_product_list = array();
			foreach ($this->_list as $list)
			{
				$product_list = '';
				if (isset($this->product_attachements[$list['id_attachment']]))
				{
					foreach ($this->product_attachements[$list['id_attachment']] as $product)
						$product_list .= $product.', ';
				}
				$list_product_list[$list['id_attachment']] = $product_list;
			}

			// Assign array in list_action_delete.tpl
			$this->tpl_delete_link_vars = array(
				'product_list' => $list_product_list,
				'product_attachements' => $this->product_attachements
			);
		}
	}

	public function postProcess()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->_errors[] = Tools::displayError('This functionnality has been disabled.');
			return;
		}
		/* PrestaShop demo mode*/
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$id = (int)Tools::getValue('id_attachment');
			if ($id && $a = new Attachment($id))
			{
				$_POST['file'] = $a->file;
				$_POST['mime'] = $a->mime;
			}
			if (!count($this->_errors))
			{
				if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']))
				{
					if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$this->_errors[] = $this->l('File too large, maximum size allowed:').' '.
							(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024).' '.$this->l('kb').'. '.
							$this->l('File size you\'re trying to upload is:').number_format(($_FILES['file']['size'] / 1024), 2, '.', '').$this->l('kb');
					else
					{
						do $uniqid = sha1(microtime());
						while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
						if (!copy($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
							$this->_errors[] = $this->l('File copy failed');
						$_POST['file_name'] = $_FILES['file']['name'];
						@unlink($_FILES['file']['tmp_name']);
						$_POST['file'] = $uniqid;
						$_POST['mime'] = $_FILES['file']['type'];
					}
				}
				else if (array_key_exists('file', $_FILES) && (int)$_FILES['file']['error'] === 1)
				{
					$max_upload = (int)ini_get('upload_max_filesize');
					$max_post = (int)ini_get('post_max_size');
					$upload_mb = min($max_upload, $max_post);
					$this->_errors[] = $this->l('the File').' <b>'.$_FILES['file']['name'].'</b> '.
						$this->l('exceeds the size allowed by the server. This limit is set to').' <b>'.$upload_mb.$this->l('Mb').'</b>';
				}
				else if (!empty($_FILES['file']['tmp_name']))
					$this->_errors[] = $this->l('No file or your file isn\'t uploadable, check your server configuration about the upload maximum size.');
			}
			$this->validateRules();
		}
		return parent::postProcess();
	}
}
