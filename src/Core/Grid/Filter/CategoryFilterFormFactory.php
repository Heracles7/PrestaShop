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

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CategoryFilterFormFactory decorates original filter factory to add custom submit action
 */
final class CategoryFilterFormFactory implements GridFilterFormFactoryInterface
{
    /**
     * @var GridFilterFormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param GridFilterFormFactoryInterface $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        GridFilterFormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GridDefinitionInterface $definition)
    {
        $categoryFilterForm = $this->formFactory->create($definition);

        $newCategoryFormBuilder = $categoryFilterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId(),
            FormType::class
        );

        /** @var FormInterface $categoryFormItem */
        foreach ($categoryFilterForm as $categoryFormItem) {
            $newCategoryFormBuilder->add(
                $categoryFormItem->getName(),
                get_class($categoryFormItem->getConfig()->getType()->getInnerType()),
                $categoryFormItem->getConfig()->getOptions()
            );
        }

        $newCategoryFormBuilder->setAction(
            $this->urlGenerator->generate('admin_category_listing_search')
        );

        return $newCategoryFormBuilder->getForm();
    }
}
