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

/**
 * Handles UI interactions of choice tree
 */
export default class ChoiceTree {
  constructor() {
    const $container = $('.js-choice-tree-container');

    $container.on('click', '.js-input-wrapper', (event) => {
      const $inputWrapper = $(event.currentTarget);

      this._toggleChildTree($inputWrapper);
    });

    $container.on('click', '.js-toggle-choice-tree-action', (event) => {
      const $action = $(event.currentTarget);

      this._toggleTree($action);
    });

    return {};
  }

  /**
   * Collapse or expand sub-tree for single parent
   *
   * @param {jQuery} $inputWrapper
   *
   * @private
   */
  _toggleChildTree($inputWrapper) {
    const $parentWrapper = $inputWrapper.closest('li');

    if ($parentWrapper.hasClass('expanded')) {
      $parentWrapper
        .removeClass('expanded')
        .addClass('collapsed');

      return;
    }

    if ($parentWrapper.hasClass('collapsed')) {
      $parentWrapper
        .removeClass('collapsed')
        .addClass('expanded');
    }
  }

  /**
   * Collapse or expand whole tree
   *
   * @param {jQuery} $action
   *
   * @private
   */
  _toggleTree($action) {
    const $parentContainer = $action.closest('.js-choice-tree-container');
    const action = $action.data('action');

    // toggle action configuration
    const config = {
      addClass: {
        expand: 'expanded',
        collapse: 'collapsed',
      },
      removeClass: {
        expand: 'collapsed',
        collapse: 'expanded',
      },
      nextAction: {
        expand: 'collapse',
        collapse: 'expand',
      },
      text: {
        expand: 'collapsed-text',
        collapse: 'expanded-text',
      },
      icon: {
        expand: 'collapsed-icon',
        collapse: 'expanded-icon',
      }
    };

    $parentContainer.find('li').each((index, item) => {
      const $item = $(item);

      if ($item.hasClass(config.removeClass[action])) {
          $item.removeClass(config.removeClass[action])
            .addClass(config.addClass[action]);
      }
    });

    $action.data('action', config.nextAction[action]);
    $action.find('.material-icons').text($action.data(config.icon[action]));
    $action.find('.js-toggle-text').text($action.data(config.text[action]));
  }
}
