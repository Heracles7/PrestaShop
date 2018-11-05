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

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Command;

use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\InvalidThemeNameException;

/**
 * Class EnableThemeCommand enables given Front Office theme for context's shop.
 */
class EnableThemeCommand
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @param string $themeName
     */
    public function __construct($themeName)
    {
        $this->assertThemeNameIsNonEmptyString($themeName);

        $this->themeName = $themeName;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * @param string $themeName
     */
    private function assertThemeNameIsNonEmptyString($themeName)
    {
        if (empty($themeName) || !preg_match('/^[a-zA-Z0-9_.-]+$/', $themeName)) {
            throw new InvalidThemeNameException(
                sprintf('Invalid theme name %s provided.', var_export($themeName, true))
            );
        }
    }
}
