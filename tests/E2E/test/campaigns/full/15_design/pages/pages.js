const {Menu} = require('../../../../selectors/BO/menu.js');
const {Pages} = require('../../../../selectors/BO/desgin/pages');
const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');

let promise = Promise.resolve();

/****Example of category data ****
 * let categoryData = {
 *  name: 'Category',
 *  parent_category: '1',
 *  description: 'Category description',
 *  meta_title: 'Category meta title',
 *  meta_description: 'Category meta description',
 *  meta_keywords: 'Category meta keywords',
 *  sub_category: {
 *    name: 'subCategory',
 *    description: 'sub category description',
 *    meta_title: 'sub category meta title',
 *    meta_description: 'sub category meta description',
 *    meta_keywords: 'sub category meta keywords'
 *  }
 * };
 */

/****Example of page data ****
 * let pageData = {
  *  name: 'page',
  *  meta_description: 'page meta description',
  *  meta_keyword: ["keyword", "page"]
  * };
 */

module.exports = {
  createCategory: function (categoryData) {
    scenario('Create cms category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should click on "Add new page category" button', () => client.waitForExistAndClick(Pages.Category.add_category_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Pages.Common.name_input, categoryData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
      test('should select the "Parent category - home" option ', () => client.waitAndSelectByValue(Pages.Category.parent_category_select, categoryData.parent_category));
      test('should set the "Description" text area ', () => client.waitAndSetValue(Pages.Category.description_textarea, categoryData.description));
      test('should set the "Meta title" input ', () => client.waitAndSetValue(Pages.Category.meta_title_input, categoryData.meta_title));
      test('should set the "Meta description" input ', () => client.waitAndSetValue(Pages.Common.meta_description_input, categoryData.meta_description));
      test('should set the "Meta keywords" input ', () => client.waitAndSetValue(Pages.Category.meta_keywords_input, categoryData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Category.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful creation.'));
      if (categoryData.hasOwnProperty('sub_category')) {
        scenario('Create the cms sub category', client => {
          test('should return to the "Home" page', () => client.waitForExistAndClick(Pages.Category.home_icon));
          test('should get the parent category ID', () => {
            return promise
              .then(() => client.isVisible(Pages.Category.name_filter))
              .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time))
              .then(() => client.getCategoryID(Pages.Category.search_name_result, 2));
          });
          test('should click on "Add new page category" button', () => client.waitForExistAndClick(Pages.Category.add_category_button));
          test('should set the "Name" input', () => client.waitAndSetValue(Pages.Common.name_input, categoryData.sub_category.name + date_time));
          test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
          test('should select the "Parent category" option ', () => client.waitAndSelectByValue(Pages.Category.parent_category_select, global.categoryID));
          test('should set the "Description" text area ', () => client.waitAndSetValue(Pages.Category.description_textarea, categoryData.sub_category.description));
          test('should set the "Meta title" input ', () => client.waitAndSetValue(Pages.Category.meta_title_input, categoryData.sub_category.meta_title));
          test('should set the "Meta description" input ', () => client.waitAndSetValue(Pages.Common.meta_description_input, categoryData.sub_category.meta_description));
          test('should set the "Meta keywords" input ', () => client.waitAndSetValue(Pages.Category.meta_keywords_input, categoryData.sub_category.meta_keywords));
          test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Category.save_button));
          test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful creation.'));
        }, 'design');
      }
    }, 'design');
  },
  checkCategoryBO: function (categoryData) {
    scenario('Check cms category existence in the Back Office', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should check the existence of the cms category', () => {
        return promise
          .then(() => client.isVisible(Pages.Category.name_filter))
          .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time))
          .then(() => client.checkExistence(Pages.Category.search_name_result, categoryData.name + date_time, 3));
      });
      if (categoryData.hasOwnProperty('sub_category')) {
        scenario('Check the cms sub category existence in the Back Office', client => {
          test('should click on "View" button', () => client.waitForExistAndClick(Pages.Category.view_button));
          test('should Check the cms sub category existence', () => {
            return promise
              .then(() => client.isVisible(Pages.Category.name_filter))
              .then(() => client.search(Pages.Category.name_filter, categoryData.sub_category.name + date_time))
              .then(() => client.checkExistence(Pages.Category.search_name_result, categoryData.sub_category.name + date_time, 3));
          });
        }, 'design');
      }
    }, 'design');
  },
  editCategory: function (categoryData, categoryNewData) {
    scenario('Edit page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Category.name_filter))
          .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time));
      });
      test('should click on "Edit" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Category.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Pages.Category.edit_button));
      });
      if (categoryData.hasOwnProperty('sub_category')) {
        test('should select the "Parent category" option ', () => client.waitAndSelectByValue(Pages.Category.parent_category_select, Number(global.categoryID) + 1));
        test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Category.save_button));
        test('should verify the appearance of the error message "The page Category cannot be moved here."', () => client.checkTextValue(Pages.Common.danger_panel, '×\nThe page Category cannot be moved here.'));
        test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
        test('should search for the category in the "Category list"', () => {
          return promise
            .then(() => client.isVisible(Pages.Category.name_filter))
            .then(() => client.search(Pages.Category.name_filter, categoryData.name + date_time));
        });
        test('should click on "Edit" button', () => {
          return promise
            .then(() => client.waitForExistAndClick(Pages.Category.dropdown_toggle))
            .then(() => client.waitForExistAndClick(Pages.Category.edit_button));
        });
      }
      test('should set the new "Name" input', () => client.waitAndSetValue(Pages.Common.name_input, categoryNewData.name + date_time));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
      test('should select the "Parent category" option ', () => client.waitAndSelectByValue(Pages.Category.parent_category_select, categoryNewData.parent_category));
      test('should set the new "Description" text area ', () => client.waitAndSetValue(Pages.Category.description_textarea, categoryNewData.description));
      test('should set the new "Meta title" input ', () => client.waitAndSetValue(Pages.Category.meta_title_input, categoryNewData.meta_title));
      test('should set the new "Meta description" input ', () => client.waitAndSetValue(Pages.Common.meta_description_input, categoryNewData.meta_description));
      test('should set the new "Meta keywords" input ', () => client.waitAndSetValue(Pages.Category.meta_keywords_input, categoryNewData.meta_keywords));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Category.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful update.'));
      if (categoryData.hasOwnProperty('sub_category')) {
        test('should click on "Edit" button', () => {
          return promise
            .then(() => client.waitForExistAndClick(Pages.Category.dropdown_toggle))
            .then(() => client.waitForExistAndClick(Pages.Category.edit_button));
        });
        test('should set the new "Name" input', () => client.waitAndSetValue(Pages.Common.name_input, categoryNewData.sub_category.name + date_time));
        test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
        if (categoryNewData.sub_category.hasOwnProperty('parent_category')) {
          test('should select the "Parent category" option ', () => client.waitAndSelectByValue(Pages.Category.parent_category_select, categoryNewData.sub_category.parent_category));
        }
        test('should set the new "Description" text area ', () => client.waitAndSetValue(Pages.Category.description_textarea, categoryNewData.sub_category.description));
        test('should set the new "Meta title" input ', () => client.waitAndSetValue(Pages.Category.meta_title_input, categoryNewData.sub_category.meta_title));
        test('should set the new "Meta description" input ', () => client.waitAndSetValue(Pages.Common.meta_description_input, categoryNewData.sub_category.meta_description));
        test('should set the new "Meta keywords" input ', () => client.waitAndSetValue(Pages.Category.meta_keywords_input, categoryNewData.sub_category.meta_keywords));
        test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Category.save_button));
        test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful update.'));
      }
    }, 'design');
  },
  deleteCategory: function (categoryName) {
    scenario('Delete page category', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Category.name_filter))
          .then(() => client.search(Pages.Category.name_filter, categoryName + date_time))
      });
      test('should click on "Delete" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Category.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Pages.Category.delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful deletion.'));
    }, 'design');
  },
  deleteCategoryWithBulkActions: function (categoryName) {
    scenario('Delete page category with bulk actions', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the category in the "Category list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Category.name_filter))
          .then(() => client.search(Pages.Category.name_filter, categoryName))
      });
      test('should click on the "Bulk actions - Select all" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Category.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Pages.Category.bulk_actions_select_all_button))
      });
      test('should click on the "Bulk actions - Delete selected" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Category.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Pages.Category.bulk_actions_delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'common_client');
  },
  createPage: function (pageData) {
    scenario('Create a CMS page', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should click on the "Add new page" button', () => client.waitForExistAndClick(Pages.Page.add_new_page_button));
      test('should set the "Meta title" input', () => client.waitAndSetValue(Pages.Common.name_input, pageData.meta_title + date_time));
      test('should set the "Meta description" input', () => client.waitAndSetValue(Pages.Common.meta_description_input, pageData.meta_description));
      for (let i in pageData.meta_keyword) {
        test('should set the "Meta Keywords - ' + pageData.meta_keyword[i] + '" input', () => {
          return promise
            .then(() => client.waitForExistAndClick(Pages.Page.meta_keywords_input))
            .then(() => client.keys(pageData.meta_keyword[i]))
            .then(() => client.keys('Enter'));
        });
      }
      test('should set the "Page content"', () => client.setTextToEditor(Pages.Page.page_content, pageData.page_content));
      test('should set the option "Indexation by search engines" to "Yes"', () => client.waitForExistAndClick(Pages.Page.enable_indexation_option));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Page.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  checkPageBO: function (pageMetaTitle) {
    scenario('Check the page existence in the Back Office', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should check the existence of the page', () => {
        return promise
          .then(() => client.isVisible(Pages.Page.title_filter_input))
          .then(() => client.search(Pages.Page.title_filter_input, pageMetaTitle + date_time))
          .then(() => client.checkExistence(Pages.Page.search_title_result, pageMetaTitle + date_time, 4))
      });
    }, 'common_client');
  },
  checkPageFO: function (pageData) {
    scenario('Check the page existence in the Front Office', client => {
      test('should go to the front Office', () => client.waitForExistAndClick(AccessPageBO.shopname));
      test('should switch to Front Office window', () => client.switchWindow(1));
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should click on the "sitemap" menu', () => client.scrollWaitForExistAndClick(AccessPageFO.sitemap));
      test('should check the existence of the page link in "PAGES" menu', () => client.waitForExistAndClick(AccessPageFO.page_link.replace("%pageName", pageData.meta_title + date_time)));
      test('should check the page content', () => client.checkTextValue(AccessPageFO.page_content, pageData.page_content));
      test('should switch to Back Office window', () => client.switchWindow(0));
    }, 'common_client');
  },
  editPage: function (pageData, newPageData) {
    scenario('Edit the page', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the page in "pages list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Page.title_filter_input))
          .then(() => client.search(Pages.Page.title_filter_input, pageData.meta_title + date_time))
      });
      test('should click on the "Edit" button', () => client.waitForExistAndClick(Pages.Page.edit_button));
      test('should set the new "Meta title" input', () => client.waitAndSetValue(Pages.Common.name_input, newPageData.meta_title + date_time));
      test('should set the new "Meta description" input', () => client.waitAndSetValue(Pages.Common.meta_description_input, newPageData.meta_description));
      for (let j in pageData.meta_keyword) {
        test('should delete the old "Meta Keywords - ' + pageData.meta_keyword[j] + '" input', () => client.waitForExistAndClick(Pages.Page.delete_tag_button.replace("%POS", Number(j) + 1)));
      }
      for (let i in newPageData.meta_keyword) {
        test('should set the new "Meta Keywords - ' + newPageData.meta_keyword[i] + '" input', () => {
          return promise
            .then(() => client.waitForExistAndClick(Pages.Page.meta_keywords_input))
            .then(() => client.keys(newPageData.meta_keyword[i]))
            .then(() => client.keys('Enter'));
        });
      }
      test('should set the "Page content"', () => client.setTextToEditor(Pages.Page.page_content, newPageData.page_content));
      test('should set the option "Indexation by search engines" to "Yes"', () => client.waitForExistAndClick(Pages.Page.enable_indexation_option));
      test('should set the option "Displayed" to "Yes"', () => client.waitForExistAndClick(Pages.Common.enable_display_option));
      test('should click on the "Save" button', () => client.waitForExistAndClick(Pages.Page.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful update.'))
    }, 'common_client');
  },
  deletePage: function (pageData) {
    scenario('Delete the page', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the page in "pages list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Page.title_filter_input))
          .then(() => client.search(Pages.Page.title_filter_input, pageData.meta_title + date_time))
      });
      test('should click on "Delete" button"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Page.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Pages.Page.delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nSuccessful deletion.'));
    }, 'common_client');
  },
  deletePageWithBulkActions: function (pageData) {
    scenario('Delete the page with Bulk actions', client => {
      test('should go to "Design-Pages" list', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
      test('should search for the page in "pages list"', () => {
        return promise
          .then(() => client.isVisible(Pages.Page.title_filter_input))
          .then(() => client.search(Pages.Page.title_filter_input, pageData.meta_title))
      });
      test('should click on "Bulk actions - Select all" button"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Page.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Pages.Page.bulk_actions_select_all_button))
      });
      test('should click on "Bulk actions - Delete selected" button"', () => {
        return promise
          .then(() => client.waitForExistAndClick(Pages.Page.bulk_actions_button))
          .then(() => client.waitForExistAndClick(Pages.Page.bulk_actions_delete_button))
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(Pages.Common.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'common_client');
  }
};