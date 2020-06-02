require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Importing data
const {Statuses} = require('@data/demo/orderStatuses');
const InvoiceOptionsFaker = require('@data/faker/invoice');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_invoicePrefix';

let browser;
let browserContext;
let page;
let fileName;
const invoiceData = new InvoiceOptionsFaker();
const defaultPrefix = '#IN';

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

/*
Edit invoice prefix
Change the Order status to Shipped
Check the invoice file name
Back to the default invoice prefix value
Check the invoice file name
 */
describe('Edit invoice prefix and check the generated invoice file name', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe(`Change the invoice prefix to '${invoiceData.prefix}'then check the invoice file name`, async () => {
    describe(`Change the invoice prefix to '${invoiceData.prefix}'`, async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToUpdatePrefix', baseContext);

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.ordersParentLink,
          this.pageObjects.dashboardPage.invoicesLink,
        );

        await this.pageObjects.invoicesPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it(`should change the invoice prefix to ${invoiceData.prefix}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateInvoicePrefix', baseContext);

        await this.pageObjects.invoicesPage.changePrefix(invoiceData.prefix);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe(`Change the order status to '${Statuses.shipped.status}' and check the invoice file Name`, async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

        await this.pageObjects.invoicesPage.goToSubMenu(
          this.pageObjects.invoicesPage.ordersParentLink,
          this.pageObjects.invoicesPage.ordersLink,
        );

        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

        // View order
        await this.pageObjects.ordersPage.goToOrder(1);

        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'UpdateStatusForUpdatedPrefix', baseContext);

        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

      it(`should check that the invoice file name contain the prefix '${invoiceData.prefix}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

        // Get invoice file name
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.contains(invoiceData.prefix.replace('#', '').trim());
      });
    });
  });

  describe('Back to the default invoice prefix value then check the invoice file name', async () => {
    describe(`Back to the default invoice prefix value '${defaultPrefix}'`, async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageForDefaultPrefix', baseContext);

        await this.pageObjects.viewOrderPage.goToSubMenu(
          this.pageObjects.viewOrderPage.ordersParentLink,
          this.pageObjects.viewOrderPage.invoicesLink,
        );

        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it(`should change the invoice prefix to '${defaultPrefix}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultPrefix', baseContext);

        await this.pageObjects.invoicesPage.changePrefix(defaultPrefix);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the default prefix in the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForDefaultPrefix', baseContext);

        await this.pageObjects.invoicesPage.goToSubMenu(
          this.pageObjects.invoicesPage.ordersParentLink,
          this.pageObjects.invoicesPage.ordersLink,
        );

        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForDefaultPrefix', baseContext);

        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should check that the invoice file name contain the default prefix ${defaultPrefix}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultPrefixInInvoice', baseContext);

        // Get invoice file name
        fileName = await this.pageObjects.viewOrderPage.getFileName();
        expect(fileName).to.contains(defaultPrefix.replace('#', '').trim());
      });
    });
  });
});
