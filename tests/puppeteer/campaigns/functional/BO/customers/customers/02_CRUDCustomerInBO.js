// Using chai
const {expect} = require('chai');
const helper = require('../../../../utils/helpers');
const loginCommon = require('../../../../commonTests/loginBO');
// Importing pages
const BOBasePage = require('../../../../../pages/BO/BObasePage');
const LoginPage = require('../../../../../pages/BO/login');
const DashboardPage = require('../../../../../pages/BO/dashboard');
const CustomersPage = require('../../../../../pages/BO/customers');
const AddCustomerPage = require('../../../../../pages/BO/addCustomer');
const ViewCustomerPage = require('../../../../../pages/BO/viewCustomer');
const FOLoginPage = require('../../../../../pages/FO/login');
const FOBasePage = require('../../../../../pages/FO/FObasePage');
const CustomerFaker = require('../../../../data/faker/customer');

let browser;
let page;
let numberOfCustomers = 0;
let createCustomerData;
let editCustomerData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
    viewCustomerPage: new ViewCustomerPage(page),
    foLoginPage: new FOLoginPage(page),
    foBasePage: new FOBasePage(page),
  };
};

// Create, Read, Update and Delete Customer in BO
describe('Create, Read, Update and Delete Customer in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createCustomerData = await (new CustomerFaker());
    editCustomerData = await (new CustomerFaker({enabled: false}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();
  it('should go to customers page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });
  it('should reset all filters', async function () {
    if (await this.pageObjects.customersPage.elementVisible(this.pageObjects.customersPage.filterResetButton, 2000)) {
      await this.pageObjects.customersPage.resetFilter();
    }
    numberOfCustomers = await this.pageObjects.customersPage.getNumberFromText(
      this.pageObjects.customersPage.customerGridTitle,
      );
    await expect(numberOfCustomers).to.be.above(0);
  });

  describe('Create Customer in BO and check Sign in in FO', async () => {
    it('should go to add new customer page', async function () {
      await this.pageObjects.customersPage.goToAddNewCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
    });
    it('should create customer and check result', async function () {
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(createCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);
      const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
        );
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
    it('should go to FO and check sign in with new account', async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.foLoginPage.customerLogin(createCustomerData);
      const isCustomerConnected = await this.pageObjects.foBasePage.isCustomerConnected();
      await expect(isCustomerConnected).to.be.true;
      await this.pageObjects.foBasePage.logout();
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  describe('View Customer Created', async () => {
    it('should filter list by email', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(createCustomerData.email);
    });
    it('should click on view customer', async function () {
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });
    it('should check customer personal information', async function () {
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextContent(
        this.pageObjects.viewCustomerPage.cardHeaderDiv.replace('%ID', '1'),
      );
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });
  });
  describe('Update Customer Created', async () => {
    it('should go to customers page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
        );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });
    it('should filter list by email', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(createCustomerData.email);
    });
    it('should go to edit customer page', async function () {
      await this.pageObjects.customersPage.goToEditCustomerPage('1');
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleEdit);
    });
    it('should update customer', async function () {
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(editCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulUpdateMessage);
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterUpdate = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
        );
      await expect(numberOfCustomersAfterUpdate).to.be.equal(numberOfCustomers + 1);
    });
    it('should go to FO and check sign in with edited account', async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.foLoginPage.customerLogin(createCustomerData);
      const isCustomerConnected = await this.pageObjects.foBasePage.isCustomerConnected();
      await expect(isCustomerConnected).to.be.false;
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  describe('View Customer Updated', async () => {
    it('should filter list by email', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(editCustomerData.email);
    });
    it('should click on view customer', async function () {
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });
    it('should check customer personal information', async function () {
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextContent(
        this.pageObjects.viewCustomerPage.cardHeaderDiv.replace('%ID', '1'),
      );
      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
    });
  });
  describe('Delete Customer', async () => {
    it('should go to customers page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
        );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });
    it('should filter list by email', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextContent(
        this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(editCustomerData.email);
    });
    it('should delete customer', async function () {
      const textResult = await this.pageObjects.customersPage.deleteCustomer('1');
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulDeleteMessage);
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterDelete = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
        );
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
