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

namespace PrestaShop\PrestaShop\Adapter\Routing;

use Link;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderInterface;

/**
 * Class AdminLinkBuilder is able to build entity links based on the Link::getAdminLink
 * method (which indirectly allows it to build symfony url as well).
 */
class AdminLinkBuilder implements EntityLinkBuilderInterface
{
    /** @var Link */
    private $link;

    /** @var array */
    private $entitiesControllers;

    /**
     * @param Link $link
     * @param array $entitiesControllers
     */
    public function __construct(Link $link, array $entitiesControllers)
    {
        $this->link = $link;
        $this->entitiesControllers = $entitiesControllers;
    }

    /**
     * {@inheritdoc}
     */
    public function buildViewLink($entity, array $parameters)
    {
        $controller = $this->entitiesControllers[$entity];
        $parameters = $this->buildActionParameters('view', $entity, $parameters);

        return $this->link->getAdminLink($controller, true, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditLink($entity, array $parameters)
    {
        $controller = $this->entitiesControllers[$entity];
        $parameters = $this->buildActionParameters('update', $entity, $parameters);

        return $this->link->getAdminLink($controller, true, $parameters);
    }

    /**
     * @param string $action
     * @param string $entity
     * @param array $parameters
     *
     * @return array
     */
    private function buildActionParameters($action, $entity, array $parameters)
    {
        $editAction = $action . $entity;
        $entityId = 'id_' . $entity;

        return array_merge(
            $parameters,
            [$entityId => $parameters[$entityId], $editAction => 1]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function canBuild($entity)
    {
        return array_key_exists($entity, $this->entitiesControllers);
    }
}
