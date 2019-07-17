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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\MoneyAmount;

/**
 * A cart rule condition for amount of money
 */
class MoneyAmountCondition
{
    /**
     * @var MoneyAmount
     */
    private $amount;

    /**
     * @var bool
     */
    private $taxExcluded;

    /**
     * @var bool|null
     */
    private $shippingExcluded;

    /**
     * @param float $amount
     * @param int $currencyId
     * @param bool $taxExcluded
     * @param bool|null $shippingExcluded
     *
     * @throws DomainConstraintException
     */
    public function __construct(float $amount, int $currencyId, bool $taxExcluded, bool $shippingExcluded = null)
    {
        $this->amount = new MoneyAmount($amount, $currencyId);
        $this->taxExcluded = $taxExcluded;
        $this->shippingExcluded = $shippingExcluded;
    }

    /**
     * @return MoneyAmount
     */
    public function getMoneyAmount(): MoneyAmount
    {
        return $this->amount;
    }

    /**
     * @return bool
     */
    public function isTaxExcluded(): bool
    {
        return $this->taxExcluded;
    }

    /**
     * @return bool|null
     */
    public function isShippingExcluded(): ?bool
    {
        return $this->shippingExcluded;
    }
}
