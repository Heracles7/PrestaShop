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

namespace PrestaShop\PrestaShop\Core\Import;

use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportTypeException;

/**
 * Class Entity defines available import entities
 */
final class Entity
{
    const TYPE_CATEGORIES = 0;
    const TYPE_PRODUCTS = 1;
    const TYPE_COMBINATIONS = 2;
    const TYPE_CUSTOMERS = 3;
    const TYPE_ADDRESSES = 4;
    const TYPE_BRANDS = 5;
    const TYPE_SUPPLIERS = 6;
    const TYPE_ALIAS = 7;
    const TYPE_STORE_CONTACTS = 8;

    /**
     * Get import entity type from name
     *
     * @param string $importType
     *
     * @return string
     */
    public static function getFromName($importType)
    {
        switch ($importType) {
            case 'categories':
                return self::TYPE_CATEGORIES;
            case 'products':
                return self::TYPE_PRODUCTS;
            case 'combinations':
                return self::TYPE_COMBINATIONS;
            case 'customers':
                return self::TYPE_CUSTOMERS;
            case 'addresses':
                return self::TYPE_ADDRESSES;
            case 'manufacturers':
                return self::TYPE_BRANDS;
            case 'suppliers':
                return self::TYPE_SUPPLIERS;
            case 'alias':
                return self::TYPE_ALIAS;
        }

        throw new NotSupportedImportTypeException(sprintf('Import type with name "%s" is not supported.', $importType));
    }

    /**
     * Class is not suppose to be initialized as it only use case
     */
    private function __construct()
    {
    }
}
