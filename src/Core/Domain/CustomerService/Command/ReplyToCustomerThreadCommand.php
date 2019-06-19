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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

class ReplyToCustomerThreadCommand
{
    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @var string
     */
    private $replyMessage;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param int $customerThreadId
     * @param string $replyMessage
     * @param string $email
     */
    public function __construct($customerThreadId, $replyMessage, $email)
    {
        $this->customerThreadId = new CustomerThreadId($customerThreadId);
        $this->replyMessage = $replyMessage;
        $this->email = new Email($email);
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return string
     */
    public function getReplyMessage()
    {
        return $this->replyMessage;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
