require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');

// Import data
const {Aliases} = require('@data/demo/search');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_search_search_quickEditStatus';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;
let numberOfSearch = 0;

/*
Quick edit status
 */
describe('Quick edit status', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'ShopParameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSearch).to.be.above(0);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

    await searchPage.resetFilter(page);

    await searchPage.filterTable(page, 'input', 'alias', Aliases.bloose.alias);

    const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
    await expect(textAlias).to.contains(Aliases.bloose.alias);
  });

  const statuses = [
    {args: {status: 'disable', enable: false}},
    {args: {status: 'enable', enable: true}},
  ];

  statuses.forEach((aliasStatus) => {
    it(`should ${aliasStatus.args.status} status`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${aliasStatus.args.status}Status`, baseContext);

      const isActionPerformed = await searchPage.setStatus(page, 1, aliasStatus.args.enable);

      if (isActionPerformed) {
        const resultMessage = await searchPage.getTextContent(page, searchPage.alertSuccessBlock);
        await expect(resultMessage).to.contains(searchPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await searchPage.getStatus(page, 1);
      await expect(currentStatus).to.be.equal(aliasStatus.args.enable);
    });
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfSearchAfterReset = await searchPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSearchAfterReset).to.be.equal(numberOfSearch);
  });
});
