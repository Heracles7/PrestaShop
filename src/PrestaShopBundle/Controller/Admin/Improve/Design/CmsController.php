<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Search\Filters\CmsCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CmsController is responsible for handling the logic in "Improve -> Design -> pages" page.
 */
class CmsController extends FrameworkBundleAdminController
{
    /**
     * @Template("@PrestaShop/Admin/Improve/Design/Cms/cms.html.twig")
     *
     * @param int $cmsCategoryParentId
     *
     * @param CmsCategoryFilters $filters
     *
     * @return array
     */
    public function indexAction($cmsCategoryParentId, CmsCategoryFilters $filters)
    {
        //todo : removes this block once SearchCriteriaEvent will be available from Category listing PR.
        $filterDefaults = $filters::getDefaults();
        $filterDefaults['filters']['id_cms_category_parent'] = $cmsCategoryParentId;
        $newSearchCriteria = new CmsCategoryFilters($filterDefaults);

        $cmsCategoryGridFactory = $this->get('prestashop.core.grid.factory.cms_category');
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($newSearchCriteria);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'cmsCategoryGrid' => $gridPresenter->present($cmsCategoryGrid),
        ];
    }
}
