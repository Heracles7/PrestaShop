<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Transfers editable tax data
 */
class EditableTax
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * EditableTax constructor.
     *
     * @param TaxId $taxId
     * @param string[] $name
     * @param float $rate
     * @param bool $active
     * @param array $shopAssociation
     */
    public function __construct(TaxId $taxId, array $name, $rate, $active, array $shopAssociation)
    {
        $this->taxId = $taxId;
        $this->name = $name;
        $this->rate = $rate;
        $this->active = $active;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
