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

namespace PrestaShopBundle\Controller\ArgumentResolver;

use PrestaShop\PrestaShop\Core\Search\ControllerAction;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\SearchParametersInterface;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * If an action inject instance of Filters, this class is responsible of
 * creating it from available sources.
 */
class SearchParametersResolver implements ArgumentValueResolverInterface
{
    /**
     * @var SearchParametersInterface
     */
    private $searchParameters;

    /**
     * @var AdminFilterRepository
     */
    private $adminFilterRepository;

    /**
     * @var Employee|void
     */
    private $employee;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var int
     */
    private $shopId;

    /**
     * SearchParametersResolver constructor.
     *
     * @param SearchParametersInterface $searchParameters
     * @param TokenStorageInterface $tokenStorage
     * @param AdminFilterRepository $adminFilterRepository
     * @param EventDispatcherInterface $dispatcher
     * @param int $shopId The Shop id
     */
    public function __construct(
        SearchParametersInterface $searchParameters,
        TokenStorageInterface $tokenStorage,
        AdminFilterRepository $adminFilterRepository,
        EventDispatcherInterface $dispatcher,
        $shopId
    ) {
        $this->searchParameters = $searchParameters;
        $this->adminFilterRepository = $adminFilterRepository;
        $this->employee = $this->getEmployee($tokenStorage);
        $this->shopId = $shopId;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return is_subclass_of($argument->getType(), Filters::class) &&
            $this->employee instanceof Employee;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $filtersClass = $argument->getType();
        list($controller, $action) = ControllerAction::fromString($request->get('_controller'));
        /** @var Filters $filters */
        $filters = new $filtersClass($filtersClass::getDefaults());
        //Override with saved filters if present
        if ($request->isMethod('GET')) {
            /** @var Filters $savedFilters */
            $savedFilters = $this->searchParameters->getFiltersFromRepository(
                $this->employee->getId(),
                $this->shopId,
                $controller,
                $action,
                $filtersClass
            );
            if ($savedFilters) {
                $filters->add($savedFilters->all());
            }
        }
        //Then override with query filters if present
        $query = $request->query;
        $queryHasFilters = false;
        foreach (SearchParametersInterface::FILTER_TYPES as $filterType) {
            if ($query->has($filterType)) {
                $queryHasFilters = true;
                break;
            }
        }
        if ($queryHasFilters) {
            /** @var Filters $queryFilters */
            $queryFilters = $this->searchParameters->getFiltersFromRequest($request, $filtersClass);
            $filters->add($queryFilters->all());
            //Update the saved filters (which have been modified by the query)
            $filtersToSave = $filters->all();
            unset($filtersToSave['offset']); //We don't save the page as it can be confusing for UX
            $this->adminFilterRepository->createOrUpdateByEmployeeAndRouteParams(
                $this->employee->getId(),
                $this->shopId,
                $filtersToSave,
                $controller,
                $action
            );
        }
        $filterSearchParametersEvent = new FilterSearchCriteriaEvent($filters);
        $this->dispatcher->dispatch(FilterSearchCriteriaEvent::NAME, $filterSearchParametersEvent);
        yield $filterSearchParametersEvent->getSearchCriteria();
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Employee|null
     */
    private function getEmployee(TokenStorageInterface $tokenStorage)
    {
        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($employee = $token->getUser())) {
            return null;
        }

        return $employee;
    }
}
