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

const $ = window.$;

class EmailSendingTest {
  constructor() {
    $('.js-send-test-email-btn').on('click', (event) => {
      console.log('test');
      event.preventDefault();

      this._handle(event);
    });
  }

  /**
   * Handle test email sending
   *
   * @param {Event} event
   *
   * @private
   */
  _handle(event) {
    // fill test email sending form with configured values
    $('#test_email_sending_mail_method').val($('input[name="form[email_config][mail_method]"]:checked').val());
    $('#test_email_sending_smtp_server').val($('#form_smtp_config_server').val());
    $('#test_email_sending_smtp_username').val($('#form_smtp_config_username').val());
    $('#test_email_sending_smtp_password').val($('#form_smtp_config_password').val());
    $('#test_email_sending_smtp_port').val($('#form_smtp_config_port').val());
    $('#test_email_sending_smtp_encryption').val($('#form_smtp_config_encryption').val());

    const $testEmailSendingForm = $(event.target).closest('form');
    console.log($testEmailSendingForm.serialize());

    $.post({
      url: $testEmailSendingForm.attr('action'),
      data: $testEmailSendingForm.serialize(),
    }).then((response) => {
      console.log(response);
    });
  }
}

export default EmailSendingTest;
