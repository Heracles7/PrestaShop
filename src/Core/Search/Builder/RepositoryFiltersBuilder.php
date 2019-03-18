<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;


use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\AdminFilter;

/**
 * This builder is able to get the employee saved filter:
 *  - thanks to filters uuid if one has been specified (either in the config or by the Filters sub class)
 *  - thanks to controller/action matching from the request
 */
final class RepositoryFiltersBuilder extends AbstractRepositoryFiltersBuilder
{
    /**
     * @inheritDoc
     */
    public function buildFilters(Filters $filters = null)
    {
        if (!$this->employeeProvider->getId() || !$this->shopId) {
            return $filters;
        }

        $filtersUuid = $this->getFiltersUuid($filters);
        $parameters = $this->getParametersFromRepository($filtersUuid);

        if (null !== $filters) {
            $filters->add($parameters);
        } else {
            $filters = new Filters($parameters, $filtersUuid);
        }

        return $filters;
    }

    /**
     * @param string $filtersUuid
     *
     * @return array
     */
    private function getParametersFromRepository($filtersUuid)
    {
        if (empty($filtersUuid) && (empty($this->controller) || empty($this->action))) {
            return [];
        }

        if (!empty($filtersUuid)) {
            /** @var AdminFilter $adminFilter */
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndUuid(
                $this->employeeProvider->getId(),
                $this->shopId,
                $filtersUuid
            );
        } else {
            /** @var AdminFilter $adminFilter */
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndRouteParams(
                $this->employeeProvider->getId(),
                $this->shopId,
                $this->controller,
                $this->action
            );
        }

        if (!$adminFilter) {
            return [];
        }

        return json_decode($adminFilter->getFilter(), true);
    }
}
