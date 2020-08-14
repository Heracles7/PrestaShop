require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const imageSettingsPage = require('@pages/BO/design/imageSettings');
const addImageTypePage = require('@pages/BO/design/imageSettings/add');

// Import data
const ImageTypeFaker = require('@data/faker/imageType');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_imageSettings_CRUDImageType';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfImageTypes = 0;

const createImageTypeData = new ImageTypeFaker();
const editImageTypeData = new ImageTypeFaker();


describe('Create, update and delete image type in BO', async () => {
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

  it('should go to image settings page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.imageSettingsLink,
    );

    await imageSettingsPage.closeSfToolBar(page);

    const pageTitle = await imageSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
  });

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await imageSettingsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfImageTypes).to.be.above(0);
  });

  describe('Create image type in BO', async () => {
    it('should go to add new image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddImageTypePage', baseContext);

      await imageSettingsPage.goToNewImageTypePage(page);
      const pageTitle = await addImageTypePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addImageTypePage.pageTitleCreate);
    });

    it('should create image type and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createImageType', baseContext);

      const textResult = await addImageTypePage.createEditImageType(page, createImageTypeData);
      await expect(textResult).to.contains(imageSettingsPage.successfulCreationMessage);

      const numberOfImageTypesAfterCreation = await imageSettingsPage.getNumberOfElementInGrid(page);
      await expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1);
    });
  });

  describe('Update imageType created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await imageSettingsPage.resetFilter(page);

      await imageSettingsPage.filterTable(
        page,
        'input',
        'name',
        createImageTypeData.name,
      );

      const textEmail = await imageSettingsPage.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(createImageTypeData.name);
    });

    it('should go to edit image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditImageTypePage', baseContext);

      await imageSettingsPage.gotoEditImageTypePage(page, 1);
      const pageTitle = await addImageTypePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addImageTypePage.pageTitleEdit);
    });

    it('should update image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateImageType', baseContext);

      const textResult = await addImageTypePage.createEditImageType(page, editImageTypeData);
      await expect(textResult).to.contains(imageSettingsPage.successfulUpdateMessage);

      const numberOfImageTypesAfterUpdate = await imageSettingsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfImageTypesAfterUpdate).to.be.equal(numberOfImageTypes + 1);
    });
  });

  describe('Delete imageType', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await imageSettingsPage.resetFilter(page);

      await imageSettingsPage.filterTable(
        page,
        'input',
        'name',
        editImageTypeData.name,
      );

      const textEmail = await imageSettingsPage.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(editImageTypeData.name);
    });

    it('should delete image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImageType', baseContext);

      const textResult = await imageSettingsPage.deleteImageType(page, 1);
      await expect(textResult).to.contains(imageSettingsPage.successfulDeleteMessage);

      const numberOfImageTypesAfterDelete = await imageSettingsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes);
    });
  });
});
