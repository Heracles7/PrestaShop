<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CustomizationFieldCore
 */
class CustomizationFieldCore extends ObjectModel
{
    /** @var int */
    public $id_product;
    /** @var int Customization type (0 File, 1 Textfield) (See Product class) */
    public $type;
    /** @var bool Field is required */
    public $required;
    /** @var bool Field was added by a module */
    public $is_module;
    /** @var string Label for customized field */
    public $name;
    /** @var bool Soft delete */
    public $is_deleted;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customization_field',
        'primary' => 'id_customization_field',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            /* Classic fields */
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'required' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'is_module' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),
            'is_deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 255),
        ),
    );

    /** @var array $webserviceParameters */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products',
                ),
            ),
        ),
    );
}
