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

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EnableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\EnableCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;

/**
 * Handles command which enables given customer.
 *
 * @internal 
 */
final class EnableCustomerHandler implements EnableCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EnableCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($command->getCustomerId()->getValue());

        if (!$customer->id) {
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found.', $customerId->getValue())
            );
        }

        $customer->active = true;
        $customer->save();
    }
}
