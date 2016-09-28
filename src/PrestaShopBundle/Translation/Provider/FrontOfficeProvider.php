<?php
/**
 * 2007-2016 PrestaShop.
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
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

class FrontOfficeProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array(
            '^Shop*',
            '^Modules(.*)Shop',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'front';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue($empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->getLocale());

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                array($this->getDefaultResourceDirectory()),
                $this->getLocale(),
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**{@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory.'/default';
    }
}
