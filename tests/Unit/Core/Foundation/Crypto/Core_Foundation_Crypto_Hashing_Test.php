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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC;

use Exception;

use PHPUnit_Framework_TestCase;

use Core_Foundation_Crypto_Hashing as Hashing;

define('_COOKIE_KEY_', '2349123849231-4123');

class Core_Foundation_Crypto_Hashing_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->hashing = new Hashing;
    }

    public function test_simple_check_hash_md5()
    {
        $this->assertTrue($this->hashing->checkHash("123", md5(_COOKIE_KEY_."123"), _COOKIE_KEY_));
        $this->assertFalse($this->hashing->checkHash("23", md5(_COOKIE_KEY_."123"), _COOKIE_KEY_));
    }

    public function test_simple_encrypt()
    {
        $this->assertTrue(is_string($this->hashing->encrypt("123", _COOKIE_KEY_)));
    }

}
