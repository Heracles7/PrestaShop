<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use Symfony\Component\Form\FormFactory;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs\LogsByEmailType;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * This class manages the data manipulated using "Logs By Email" form
 * in "Configure > Advanced Parameters > Logs" page.
 */
final class LogsFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var LogsFormDataProvider
     */
    private $formDataProvider;

    public function __construct(
        FormFactory $formFactory,
        LogsFormDataProvider $formDataProvider
    )
    {
        $this->formFactory = $formFactory;
        $this->formDataProvider = $formDataProvider;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm()
    {
        return $this->formFactory->createBuilder()
            ->add('logs_by_email', LogsByEmailType::class)
            ->setData($this->formDataProvider->getData())
            ->getForm()
        ;
    }

    /**
     * @param array $data
     * @return array errors found if not empty
     * @throws UndefinedOptionsException if data is invalid
     */
    public function save(array $data)
    {
        return $this->formDataProvider->setData($data);
    }
}
