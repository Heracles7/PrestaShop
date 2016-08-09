<?php
/**
 * 2007-2015 PrestaShop.
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
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Product;

class ProductListingPresenter extends ProductPresenter
{
    public function present(
        ProductPresentationSettings $settings,
        array $product,
        \Language $language
    ) {
        $presentedProduct = parent::present(
            $settings,
            $product,
            $language
        );

        if ($product['id_product_attribute'] != 0 && !$settings->allow_add_variant_to_cart_from_listing) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        if ($product['customizable'] == 2 || !empty($product['customization_required'])) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        return $presentedProduct;
    }
}
