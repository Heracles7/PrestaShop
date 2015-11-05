<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Admin\AbstractAdminQueryBuilder;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface;

/**
 * Data provider for new Architecture, about Product object model.
 *
 * This class will provide data from DB / ORM about Products for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 */
class AdminProductDataProvider extends AbstractAdminQueryBuilder implements ProductInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * Constructor
     *
     * Entity manager is automatically injected.
     *
     * @param EntityManager $entityManager
     * @param ImageManager $imageManager
     */
    public function __construct(EntityManager $entityManager, ImageManager $imageManager)
    {
        $this->entityManager = $entityManager;
        $this->imageManager = $imageManager;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::getPersistedFilterParameters()
     */
    public function getPersistedFilterParameters()
    {
        $employee = \Context::getContext()->employee;
        $shop = \Context::getContext()->shop;
        $filter = $this->entityManager->getRepository('PrestaShopBundle:AdminFilter')->findOneBy(array(
            'employee' => $employee->id ?: 0,
            'shop' => $shop->id ?: 0,
            'controller' => 'ProductController',
            'action' => 'catalogAction'
        ));
        /* @var $filter AdminFilter */
        if (!$filter) {
            return AdminFilter::getProductCatalogEmptyFilter();
        }
        return $filter->getProductCatalogFilter();
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::isCategoryFiltered()
     */
    public function isCategoryFiltered()
    {
        $filters = $this->getPersistedFilterParameters();
        return (isset($filters['filter_category']) && $filters['filter_category'] > 0);
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::isColumnFiltered()
     */
    public function isColumnFiltered()
    {
        $filters = $this->getPersistedFilterParameters();
        foreach ($filters as $filterKey => $filterValue) {
            if (strpos($filterKey, 'filter_column_') === 0 && $filterValue !== '') {
                return true; // break at first column filter found
            }
        }
        return false;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::persistFilterParameters()
     */
    public function persistFilterParameters(array $parameters)
    {
        $employee = \Context::getContext()->employee;
        $shop = \Context::getContext()->shop;
        $filter = $this->entityManager->getRepository('PrestaShopBundle:AdminFilter')->findOneBy(array(
            'employee' => $employee->id ?: 0,
            'shop' => $shop->id ?: 0,
            'controller' => 'ProductController',
            'action' => 'catalogAction'
        ));

        if (!$filter) {
            $filter = new AdminFilter();
            $filter->setEmployee($employee->id ?: 0)->setShop($shop->id ?: 0)->setController('ProductController')->setAction('catalogAction');
        }

        $filter->setProductCatalogFilter($parameters);
        $this->entityManager->persist($filter);

        // if each filter is == '', then remove item from DB :)
        if (count(array_diff($filter->getProductCatalogFilter(), array(''))) == 0) {
            $this->entityManager->remove($filter);
        }

        $this->entityManager->flush();
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::combinePersistentCatalogProductFilter()
     */
    public function combinePersistentCatalogProductFilter($paramsIn = array())
    {
        $paramsOut = array();
        // retrieve persisted filter parameters
        $persistedParams = $this->getPersistedFilterParameters();
        // merge with new values
        $paramsOut = array_merge($persistedParams, (array)$paramsIn);
        // persist new values
        $this->persistFilterParameters($paramsOut);
        // return new values
        return $paramsOut;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::getCatalogProductList()
     */
    public function getCatalogProductList($offset, $limit, $orderBy, $sortOrder, $post = array())
    {
        $filterParams = $this->combinePersistentCatalogProductFilter($post);
        $showPositionColumn = $this->isCategoryFiltered();
        if ($orderBy == 'position_ordering' && $showPositionColumn) {
            foreach ($filterParams as $key => $param) {
                if (strpos($key, 'filter_column_') === 0) {
                    $filterParams[$key] = '';
                }
            }
        }
        if ($orderBy == 'position_ordering') {
            $orderBy = 'position';
        }

        $idShop = \Context::getContext()->shop->id;
        $idLang = \Context::getContext()->language->id;

        $sqlSelect = array(
            'id_product' => array('table' => 'p', 'field' => 'id_product', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
            'reference' => array('table' => 'p', 'field' => 'reference', 'filtering' => self::FILTERING_LIKE_BOTH),
            'price' => array('table' => 'p', 'field' => 'price', 'filtering' => ' %s '),
            'id_shop_default' => array('table' => 'p', 'field' => 'id_shop_default'),
            'is_virtual' => array('table' => 'p', 'field' => 'is_virtual'),
            'name' => array('table' => 'pl', 'field' => 'name', 'filtering' => self::FILTERING_LIKE_BOTH),
            'active' => array('table' => 'sa', 'field' => 'active', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
            'shopname' => array('table' => 'shop', 'field' => 'name'),
            'id_image' => array('table' => 'image_shop', 'field' => 'id_image'),
            'name_category' => array('table' => 'cl', 'field' => 'name', 'filtering' => self::FILTERING_LIKE_BOTH),
            'price_final' => '0',
            'nb_downloadable' => array('table' => 'pd', 'field' => 'nb_downloadable'),
            'sav_quantity' => array('table' => 'sav', 'field' => 'quantity', 'filtering' => ' %s '),
            'badge_danger' => array('select' => 'IF(sav.`quantity`<=0, 1, 0)', 'filtering' => 'IF(sav.`quantity`<=0, 1, 0) = %s')
        );
        $sqlTable = array(
            'p' => 'product',
            'pl' => array(
                'table' => 'product_lang',
                'join' => 'LEFT JOIN',
                'on' => 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$idLang.' AND pl.`id_shop` = '.$idShop
            ),
            'sav' => array(
                'table' => 'stock_available',
                'join' => 'LEFT JOIN',
                'on' => 'sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop_group = 1 AND sav.id_shop = 0' // FIXME, from legacy request, why these settings?
            ),
            'sa' => array(
                'table' => 'product_shop',
                'join' => 'JOIN',
                'on' => 'p.`id_product` = sa.`id_product` AND sa.id_shop = '.$idShop
            ),
            'cl' => array(
                'table' => 'category_lang',
                'join' => 'LEFT JOIN',
                'on' => 'sa.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.$idLang.' AND cl.id_shop = '.$idShop
            ),
            'c' => array(
                'table' => 'category',
                'join' => 'LEFT JOIN',
                'on' => 'c.`id_category` = cl.`id_category`'
            ),
            'shop' => array(
                'table' => 'shop',
                'join' => 'LEFT JOIN',
                'on' => 'shop.id_shop = '.$idShop
            ),
            'image_shop' => array(
                'table' => 'image_shop',
                'join' => 'LEFT JOIN',
                'on' => 'image_shop.`id_product` = p.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$idShop
            ),
            'i' => array(
                'table' => 'image',
                'join' => 'LEFT JOIN',
                'on' => 'i.`id_image` = image_shop.`id_image`'
            ),
            'pd' => array(
                'table' => 'product_download',
                'join' => 'LEFT JOIN',
                'on' => 'pd.`id_product` = p.`id_product`'
            )
        );
        $sqlWhere = array('AND', 1);
        foreach ($filterParams as $filterParam => $filterValue) {
            if (!$filterValue && $filterValue !== '0') {
                continue;
            }
            if (strpos($filterParam, 'filter_column_') === 0) {
                $field = substr($filterParam, 14); // 'filter_column_' takes 14 chars
                if (isset($sqlSelect[$field]['table'])) {
                    $sqlWhere[] = $sqlSelect[$field]['table'].'.`'.$sqlSelect[$field]['field'].'` '.sprintf($sqlSelect[$field]['filtering'], $filterValue);
                } else {
                    $sqlWhere[] = '('.sprintf($sqlSelect[$field]['filtering'], $filterValue).')';
                }
            }
            // for 'filter_category', see next if($showPositionColumn) block.
        }

        $sqlOrder = array($orderBy.' '.$sortOrder);
        if ($orderBy != 'id_product') {
            $sqlOrder[] = 'id_product asc'; // secondary order by (useful when ordering by active, quantity, price, etc...)
        }
        $sqlLimit = $offset.', '.$limit;

        // Column 'position' added if filtering by category
        if ($showPositionColumn) {
            $sqlSelect['position'] = array('table' => 'cp', 'field' => 'position');
            $sqlTable['cp'] = array(
                'table' => 'category_product',
                'join' => 'INNER JOIN',
                'on' => 'cp.`id_product` = p.`id_product` AND cp.`id_category` = '.$filterParams['filter_category']
            );
        } elseif ($orderBy == 'position') {
            // We do not show position column, so we do not join the table, so we do not order by position!
            $sqlOrder = array('id_product ASC');
        }

        // exec legacy hook but with different parameters (retro-compat < 1.7 is broken here)
        \Hook::exec('actionAdminProductsListingFieldsModifier', array(
            '_ps_version' => _PS_VERSION_,
            'sql_select' => &$sqlSelect,
            'sql_table' => &$sqlTable,
            'sql_where' => &$sqlWhere,
            'sql_order' => &$sqlOrder,
            'sql_limit' => &$sqlLimit
        ));

        $sql = $this->compileSqlQuery($sqlSelect, $sqlTable, $sqlWhere, $sqlOrder, $sqlLimit);
        $products = \Db::getInstance()->executeS($sql, true, false);
        $total = \Db::getInstance()->executeS('SELECT FOUND_ROWS();', true, false);
        $total = $total[0]['FOUND_ROWS()'];

        // post treatment
        $currency = new \Currency(\Configuration::get('PS_CURRENCY_DEFAULT'));
        foreach ($products as &$product) {
            $product['price'] = \Tools::displayPrice($product['price'], $currency);
            $product['total'] = $total; // total product count (filtered)
            $product['price_final'] = \Product::getPriceStatic($product['id_product'], true, null,
                    (int)\Configuration::get('PS_PRICE_DISPLAY_PRECISION'), null, false, true, 1,
                    true, null, null, null, $nothing, true, true);
            $product['price_final'] = \Tools::displayPrice($product['price_final'], $currency);
            $product['image'] = $this->imageManager->getThumbnailForListing($product['id_image']);
        }

        // post treatment by hooks
        // exec legacy hook but with different parameters (retro-compat < 1.7 is broken here)
        \Hook::exec('actionAdminProductsListingResultsModifier', array(
            '_ps_version' => _PS_VERSION_,
            'products' => &$products,
            'total' => $total
        ));

        return $products;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataProvider\Admin\ProductInterface::countAllProducts()
     */
    public function countAllProducts()
    {
        $idShop = \Context::getContext()->shop->id;
        $sqlSelect = array(
            'id_product' => array('table' => 'p', 'field' => 'id_product')
        );
        $sqlTable = array(
            'p' => 'product',
            'sa' => array(
                'table' => 'product_shop',
                'join' => 'JOIN',
                'on' => 'p.`id_product` = sa.`id_product` AND sa.id_shop = '.$idShop
            )
        );

        $sql = $this->compileSqlQuery($sqlSelect, $sqlTable);
        \Db::getInstance()->executeS($sql, true, false);
        $total = \Db::getInstance()->executeS('SELECT FOUND_ROWS();', true, false);
        $total = $total[0]['FOUND_ROWS()'];
        return $total;
    }

    /**
     * Translates new Core route parameters into their Legacy equivalent.
     *
     * @param string[] $coreParameters The new Core route parameters
     * @return string[] The URL parameters for Legacy URL (GETs)
     */
    public function mapLegacyParametersProductForm($coreParameters = array())
    {
        $params = array();
        if ($coreParameters['id'] == '0') {
            $params['addproduct'] = 1;
        } else {
            $params['updateproduct'] = 1;
            $params['id_product'] = $coreParameters['id'];
        }
        return $params;
    }
}
