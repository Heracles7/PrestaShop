require('module-alias/register');

const {expect} = require('chai');
// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const HomePageFO = require('@pages/FO/home');
const CategoryPageFO = require('@pages/FO/category');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_pagination_updateNumberOfProductsPerPage';


let browser;
let browserContext;
let page;
const updatedProductPerPage = 5;
const defaultNumberOfProductsPerPage = 10;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    homePageFO: new HomePageFO(page),
    categoryPageFO: new CategoryPageFO(page),
  };
};

/*
Set number of products displayed to 5
Check the update in FO
Set number of products displayed to default value 10
Check the update in FO
 */
describe('Update number of product displayed on FO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  const tests = [
    {args: {numberOfProductsPerPage: updatedProductPerPage}},
    {args: {numberOfProductsPerPage: defaultNumberOfProductsPerPage}},
  ];

  tests.forEach((test, index) => {
    describe(`Update number of product displayed to ${test.args.numberOfProductsPerPage}`, async () => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index + 1}`, baseContext);

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.shopParametersParentLink,
          this.pageObjects.dashboardPage.productSettingsLink,
        );

        await this.pageObjects.productSettingsPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
      });

      it(
        `should set number of products displayed per page to '${test.args.numberOfProductsPerPage}'`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateProductsPerPage${index + 1}`, baseContext);

          const result = await this.pageObjects.productSettingsPage.setProductsDisplayedPerPage(
            test.args.numberOfProductsPerPage,
          );

          await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
        },
      );

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index + 1}`, baseContext);

        page = await this.pageObjects.productSettingsPage.viewMyShop();
        this.pageObjects = await init();

        const isHomePage = await this.pageObjects.homePageFO.isHomePage();
        await expect(isHomePage, 'Home page was not opened').to.be.true;
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToHomeCategory${index + 1}`, baseContext);

        await this.pageObjects.homePageFO.changeLanguage('en');
        await this.pageObjects.homePageFO.goToAllProductsPage();

        const isCategoryPage = await this.pageObjects.categoryPageFO.isCategoryPage();
        await expect(isCategoryPage, 'Home category page was not opened');
      });

      it(`should check that number of products is equal to '${test.args.numberOfProductsPerPage}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProduct${index + 1}`, baseContext);

        const numberOfProducts = await this.pageObjects.categoryPageFO.getNumberOfProductsDisplayed();

        await expect(
          numberOfProducts,
          'Number of product displayed is incorrect',
        ).to.equal(test.args.numberOfProductsPerPage);

        page = await this.pageObjects.homePageFO.closePage(browserContext, 0);
        this.pageObjects = await init();
      });
    });
  });
});
