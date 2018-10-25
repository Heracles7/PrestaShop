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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImportDataConfigurationFormHandler defines a form handler for import data configuration form.
 */
final class ImportDataConfigurationFormHandler implements ImportFormHandlerInterface
{
    /**
     * @var FormBuilderInterface the form builder
     */
    protected $formBuilder;

    /**
     * @var HookDispatcherInterface the event dispatcher
     */
    protected $hookDispatcher;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->formBuilder = $formBuilder;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(FormDataProviderInterface $formDataProvider)
    {
        $this->formBuilder->setData($formDataProvider->getData());
        $this->hookDispatcher->dispatchWithParameters(
            'actionImportDataConfigurationForm',
            [
                'form_builder' => $this->formBuilder,
            ]
        );

        return $this->formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function save(FormDataProviderInterface $formDataProvider, array $data)
    {
        $errors = $formDataProvider->setData($data);

        $this->hookDispatcher->dispatchWithParameters(
            'actionImportDataConfigurationSave',
            [
                'errors' => &$errors,
                'form_data' => $data,
            ]
        );

        return $errors;
    }
}
