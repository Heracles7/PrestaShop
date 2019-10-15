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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import OrderViewPageMap from './OrderViewPageMap';

const $ = window.$;

$(() => {
  handlePaymentDetailsToggle();
  handlePrivateNoteChange();

  $(OrderViewPageMap.privateNoteToggleBtn).on('click', (event) => {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  function handlePaymentDetailsToggle() {
    $(OrderViewPageMap.orderPaymentDetailsBtn).on('click', (event) => {
      const $paymentDetailRow = $(event.currentTarget).closest('tr').next(':first');

      $paymentDetailRow.toggleClass('d-none');
    });
  }

  function togglePrivateNoteBlock() {
    const $block = $(OrderViewPageMap.privateNoteBlock);
    const $btn = $(OrderViewPageMap.privateNoteToggleBtn);
    const isPrivateNoteOpened = $btn.hasClass('is-opened');

    if (isPrivateNoteOpened) {
      $btn.removeClass('is-opened');
      $block.addClass('d-none');
    } else {
      $btn.addClass('is-opened');
      $block.removeClass('d-none');
    }

    const $icon = $btn.find('.material-icons');
    $icon.text(isPrivateNoteOpened ? 'add' : 'remove');
  }

  function handlePrivateNoteChange() {
    const $submitBtn = $(OrderViewPageMap.privateNoteSubmitBtn);

    $(OrderViewPageMap.privateNoteInput).on('input', (event) => {
      const note = $(event.currentTarget).val();

      if (note) {
        $submitBtn.attr('disabled', false);
      } else {
        $submitBtn.attr('disabled', true);
      }
    });
  }
});
