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

class AttachmentCore extends ObjectModel
{
	public $file;
	public $file_name;
	public $name;
	public $mime;
	public $description;

	/** @var integer position */
	public $position;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'attachment',
		'primary' => 'id_attachment',
		'multilang' => true,
		'fields' => array(
			'file' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 40),
			'mime' => 			array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128),
			'file_name' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),

			// Lang fields
			'name' => 			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
			'description' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
		),
	);

	public function delete()
	{
		@unlink(_PS_DOWNLOAD_DIR_.$this->file);
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'product_attachment WHERE id_attachment = '.(int)$this->id);
		return parent::delete();
	}

	public function deleteSelection($attachments)
	{
		$return = 1;
		foreach ($attachments as $id_attachment)
		{
			$attachment = new Attachment((int)$id_attachment);
			$return &= $attachment->delete();
		}
		return $return;
	}

	public static function getAttachments($id_lang, $id_product, $include = true)
	{
		return Db::getInstance()->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'attachment a
			LEFT JOIN '._DB_PREFIX_.'attachment_lang al
				ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)$id_lang.')
			WHERE a.id_attachment '.($include ? 'IN' : 'NOT IN').' (
				SELECT pa.id_attachment
				FROM '._DB_PREFIX_.'product_attachment pa
				WHERE id_product = '.(int)$id_product.'
			)'
		);
	}

	public static function attachToProduct($id_product, $array)
	{
		$result1 = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'product_attachment WHERE id_product = '.(int)$id_product);
		if (is_array($array))
		{
			$ids = array();
			foreach ($array as $id_attachment)
				$ids[] = '('.(int)$id_product.','.(int)$id_attachment.')';
			Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'product
				SET cache_has_attachments = '.(count($ids) ? '1' : '0').'
				WHERE id_product = '.(int)$id_product.'
				LIMIT 1
			');
			return ($result1 && Db::getInstance()->execute('
				INSERT INTO '._DB_PREFIX_.'product_attachment (id_product, id_attachment)
				VALUES '.implode(',', $ids))
			);
		}
		return $result1;
	}

	public static function getProductAttached($id_lang, $list)
	{
		$ids_attachements = array();
		if (is_array($list))
		{
			foreach ($list as $attachement)
				$ids_attachements[] = $attachement['id_attachment'];

			$sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attachment` pa
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pa.`id_product` = pl.`id_product`'.Context::getContext()->shop->addSqlRestrictionOnLang('pl').')
					WHERE `id_attachment` IN ('.implode(',', array_map('intval', $ids_attachements)).')
						AND pl.`id_lang` = '.(int)$id_lang;
			$tmp = Db::getInstance()->executeS($sql);
			$product_attachements = array();
			foreach ($tmp as $t)
				$product_attachements[$t['id_attachment']][] = $t['name'];
			return $product_attachements;
		}
		else
			return false;
	}
}

