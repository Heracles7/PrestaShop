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

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Carrier;
use Cart;
use Category;
use Currency;
use Customer;
use Db;
use Gender;
use Language;
use Link;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\BoughtProductInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerCartInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerCartsInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerOrderInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerOrdersInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\CustomerProductsInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\PersonalInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\Subscriptions;
use PrestaShop\PrestaShop\Core\Domain\Customer\Dto\ViewedProductInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerInformationHandlerInterface;
use Product;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Class GetCustomerInformationHandler
 */
final class GetCustomerInformationHandler implements GetCustomerInformationHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Link
     */
    private $link;

    /**
     * @param TranslatorInterface $translator
     * @param int $contextLangId
     * @param Link $link
     */
    public function __construct(
        TranslatorInterface $translator,
        $contextLangId,
        Link $link
    ) {
        $this->contextLangId = $contextLangId;
        $this->translator = $translator;
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerInformation $query)
    {
        $customerId = $query->getCustomerId();
        $customer = new Customer($customerId->getValue());

        if (!$customer->id) {
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found.', $customerId->getValue())
            );
        }

        return new CustomerInformation(
            $customerId,
            $this->getPersonalInformation($customer),
            $this->getCustomerOrders($customer),
            $this->getCustomerCarts($customer),
            $this->getCustomerProducts($customer)
        );
    }

    /**
     * @param Customer $customer
     *
     * @return PersonalInformation
     */
    private function getPersonalInformation(Customer $customer)
    {
        $customerStats = $customer->getStats();

        $gender = new Gender($customer->id_gender, $this->contextLangId);
        $socialTitle = $gender->name ?: $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');

        if ($customer->birthday && '0000-00-00' !== $customer->birthday) {
            $birthday = sprintf(
                $this->translator->trans('%1$d years old (birth date: %2$s)', [], 'Admin.Orderscustomers.Feature'),
                $customerStats['age'],
                Tools::displayDate($customer->birthday)
            );
        } else {
            $birthday = $this->translator->trans('Unknown', [], 'Admin.Orderscustomers.Feature');
        }

        $registrationDate = Tools::displayDate($customer->date_add, null, true);
        $lastUpdateDate = Tools::displayDate($customer->date_upd, null, true);
        $lastVisitDate = $customerStats['last_visit'] ?
            Tools::displayDate($customerStats['last_visit'], null, true) :
            $this->translator->trans('Never', [], 'Admin.Global')
        ;

        $customerShop = new Shop($customer->id_shop);
        $customerLanguage = new Language($customer->id_lang);

        $customerSubscriptions = new Subscriptions(
            (bool) $customer->newsletter,
            (bool) $customer->optin
        );

        return new PersonalInformation(
            $customer->firstname,
            $customer->lastname,
            $customer->email,
            $customer->isGuest(),
            $socialTitle,
            $birthday,
            $registrationDate,
            $lastUpdateDate,
            $lastVisitDate,
            $this->getCustomerRankBySales($customer->id),
            $customerShop->name,
            $customerLanguage->name,
            $customerSubscriptions
        );
    }

    /**
     * @param int $customerId
     *
     * @return int|null Customer rank or null if customer is not ranked.
     */
    private function getCustomerRankBySales($customerId)
    {
        $sql = 'SELECT SUM(total_paid_real) FROM ' . _DB_PREFIX_ . 'orders WHERE id_customer = ' . (int) $customerId . ' AND valid = 1';

        if ($totalPaid = Db::getInstance()->getValue($sql)) {
            $sql = '
                SELECT SQL_CALC_FOUND_ROWS COUNT(*)
                FROM ' . _DB_PREFIX_ . 'orders
                WHERE valid = 1
                    AND id_customer != ' . (int) $customerId . '
                GROUP BY id_customer
                HAVING SUM(total_paid_real) > ' . (int) $totalPaid
            ;

            Db::getInstance()->getValue($sql);

            return (int) Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
        }

        return null;
    }

    /**
     * @param Customer $customer
     *
     * @return CustomerOrdersInformation
     */
    private function getCustomerOrders(Customer $customer)
    {
        $validOrders = [];
        $invalidOrders = [];

        $orders = Order::getCustomerOrders($customer->id, true);
        $totalSpent = 0;

        foreach ($orders as $order) {
            $order['total_paid_real_not_formated'] = $order['total_paid_real'];
            $order['total_paid_real'] = Tools::displayPrice(
                $order['total_paid_real'],
                new Currency((int) $order['id_currency'])
            );

            if (!isset($order['order_state'])) {
                $order['order_state'] = $this->translator->trans(
                    'There is no status defined for this order.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            }

            $customerOrderInformation = new CustomerOrderInformation(
                (int) $order['id_order'],
                $order['date_add'],
                $order['payment'],
                $order['order_state'],
                (int) $order['nb_products'],
                $order['total_paid_real']
            );

            if ($order['valid']) {
                $validOrders[] = $customerOrderInformation;
                $totalSpent += $order['total_paid_real_not_formated'] / $order['conversion_rate'];
            } else {
                $invalidOrders[] = $customerOrderInformation;
            }
        }

        return new CustomerOrdersInformation(
            Tools::displayPrice($totalSpent),
            $validOrders,
            $invalidOrders
        );
    }

    /**
     * @param Customer $customer
     *
     * @return CustomerCartsInformation
     */
    private function getCustomerCarts(Customer $customer)
    {
        $carts = Cart::getCustomerCarts($customer->id);
        $customerCarts = [];

        foreach ($carts as $cart) {
            $cart = new Cart((int) $cart['id_cart']);
            $currency = new Currency($cart->id_currency);
            $carrier = new Carrier($cart->id_carrier);

            $summary = $cart->getSummaryDetails();

            $customerCarts[] = new CustomerCartInformation(
                sprintf('%06d', $cart->id),
                Tools::displayDate($cart->date_add, null, true),
                Tools::displayPrice($summary['total_price'], $currency),
                $carrier->name
            );
        }

        return new CustomerCartsInformation($customerCarts);
    }

    /**
     * @param Customer $customer
     *
     * @return CustomerProductsInformation
     */
    private function getCustomerProducts(Customer $customer)
    {
        $boughtProducts = [];
        $viewedProducts = [];

        $products = $customer->getBoughtProducts();
        foreach ($products as $product) {
            $boughtProducts[] = new BoughtProductInformation(
                (int) $product['id_order'],
                Tools::displayDate($product['date_add'], null, false),
                $product['product_name'],
                $product['product_quantity']
            );
        }

        $sql = '
            SELECT DISTINCT cp.id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop
            FROM ' . _DB_PREFIX_ . 'cart_product cp
            JOIN ' . _DB_PREFIX_ . 'cart c ON (c.id_cart = cp.id_cart)
            JOIN ' . _DB_PREFIX_ . 'product p ON (cp.id_product = p.id_product)
            WHERE c.id_customer = ' . (int) $customer->id . '
                AND NOT EXISTS (
                        SELECT 1
                        FROM ' . _DB_PREFIX_ . 'orders o
                        JOIN ' . _DB_PREFIX_ . 'order_detail od ON (o.id_order = od.id_order)
                        WHERE product_id = cp.id_product AND o.valid = 1 AND o.id_customer = ' . (int) $customer->id . '
                )
        ';

        $viewedProductsData = Db::getInstance()->executeS($sql);
        foreach ($viewedProductsData as $productData) {
            $product = new Product(
                $productData['id_product'],
                false,
                $this->contextLangId,
                $productData['id_shop']
            );

            if (!Validate::isLoadedObject($product)) {
                continue;
            }

            $productUrl = $this->link->getProductLink(
                $product->id,
                $product->link_rewrite,
                Category::getLinkRewrite($product->id_category_default, $this->contextLangId),
                null,
                null,
                $productData['cp_id_shop']
            );

            $viewedProducts[] = new ViewedProductInformation(
                (int) $product->id,
                Tools::htmlentitiesUTF8($product->name),
                $productUrl
            );
        }

        return new CustomerProductsInformation(
            $boughtProducts,
            $viewedProducts
        );
    }
}
