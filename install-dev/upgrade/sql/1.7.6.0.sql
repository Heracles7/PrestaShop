SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_currency` ADD `numeric_iso_code` varchar(3) NOT NULL DEFAULT '0' AFTER `iso_code`;
ALTER TABLE `PREFIX_currency` ADD `precision` int(2) NOT NULL DEFAULT 6 AFTER `numeric_iso_code`;
ALTER TABLE `PREFIX_currency` ADD KEY `currency_iso_code` (`iso_code`);

/* Localized currency information */
CREATE TABLE `PREFIX_currency_lang` (
    `id_currency` int(10) unsigned NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `symbol` varchar(255) NOT NULL,
    PRIMARY KEY (`id_currency`,`id_lang`)
  ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:ps_1760_copy_data_from_currency_to_currency_lang(); */;

/* Module Manager tab should be the first tab in Modules Tab */
UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminModulesSf' AND `position`= 1;
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminParentModulesCatalog' AND `position`= 0;

/* Fix Problem with missing lang entries in Configuration */
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`)
SELECT `id_configuration`, l.`id_lang`, `value`
  FROM `PREFIX_configuration` c
  JOIN `PREFIX_lang_shop` l on l.`id_shop` = COALESCE(c.`id_shop`, 1)
  WHERE `name` IN (
      'PS_DELIVERY_PREFIX',
      'PS_INVOICE_PREFIX',
      'PS_INVOICE_LEGAL_FREE_TEXT',
      'PS_INVOICE_FREE_TEXT',
      'PS_RETURN_PREFIX',
      'PS_SEARCH_BLACKLIST',
      'PS_CUSTOMER_SERVICE_SIGNATURE',
      'PS_MAINTENANCE_TEXT',
      'PS_LABEL_IN_STOCK_PRODUCTS',
      'PS_LABEL_OOS_PRODUCTS_BOA',
      'PS_LABEL_OOS_PRODUCTS_BOD'
      )
  AND NOT EXISTS (SELECT 1 FROM `PREFIX_configuration_lang` WHERE `id_configuration` = c.`id_configuration`);

/* PHP:ps_1760_update_configuration(); */;
/* PHP:ps_1760_update_tabs(); */;

/* Insert new hooks */
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionListMailThemes', 'List the available email themes and layouts', 'This hook allows to add/remove available email themes (ThemeInterface) and/or to add/remove their layouts (LayoutInterface)', '1'),
  (NULL, 'actionGetMailThemeFolder', 'Define the folder of an email theme', 'This hook allows to change the folder of an email theme (useful if you theme is in a module for example)', '1'),
  (NULL, 'actionBuildMailLayoutVariables', 'Build the variables used in email layout rendering', 'This hook allows to change the variables used when an email layout is rendered', '1'),
  (NULL, 'actionGetMailLayoutTransformations', 'Define the transformation to apply on layout', 'This hook allows to add/remove TransformationInterface used to generate an email layout', '1')
;
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
(NULL,"actionSqlRequestFormBuilderModifier","Modify sql request identifiable object form","This hook allows to modify sql request identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionCustomerFormBuilderModifier","Modify customer identifiable object form","This hook allows to modify customer identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionLanguageFormBuilderModifier","Modify language identifiable object form","This hook allows to modify language identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionCurrencyFormBuilderModifier","Modify currency identifiable object form","This hook allows to modify currency identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionWebserviceKeyFormBuilderModifier","Modify webservice key identifiable object form","This hook allows to modify webservice key identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionMetaFormBuilderModifier","Modify meta identifiable object form","This hook allows to modify meta identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionCategoryFormBuilderModifier","Modify category identifiable object form","This hook allows to modify category identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionRootCategoryFormBuilderModifier","Modify root category identifiable object form","This hook allows to modify root category identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionContactFormBuilderModifier","Modify contact identifiable object form","This hook allows to modify contact identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionCmsPageCategoryFormBuilderModifier","Modify cms page category identifiable object form","This hook allows to modify cms page category identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionTaxFormBuilderModifier","Modify tax identifiable object form","This hook allows to modify tax identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionManufacturerFormBuilderModifier","Modify manufacturer identifiable object form","This hook allows to modify manufacturer identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionEmployeeFormBuilderModifier","Modify employee identifiable object form","This hook allows to modify employee identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionProfileFormBuilderModifier","Modify profile identifiable object form","This hook allows to modify profile identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionCmsPageFormBuilderModifier","Modify cms page identifiable object form","This hook allows to modify cms page identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionManufacturerAddressFormBuilderModifier","Modify manufacturer address identifiable object form","This hook allows to modify manufacturer address identifiable object forms content by modifying form builder data or FormBuilder itself","1"),
(NULL,"actionBeforeUpdateSqlRequestFormHandler","Modify sql request identifiable object data before updating it","This hook allows to modify sql request identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateCustomerFormHandler","Modify customer identifiable object data before updating it","This hook allows to modify customer identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateLanguageFormHandler","Modify language identifiable object data before updating it","This hook allows to modify language identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateCurrencyFormHandler","Modify currency identifiable object data before updating it","This hook allows to modify currency identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateWebserviceKeyFormHandler","Modify webservice key identifiable object data before updating it","This hook allows to modify webservice key identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateMetaFormHandler","Modify meta identifiable object data before updating it","This hook allows to modify meta identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateCategoryFormHandler","Modify category identifiable object data before updating it","This hook allows to modify category identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateRootCategoryFormHandler","Modify root category identifiable object data before updating it","This hook allows to modify root category identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateContactFormHandler","Modify contact identifiable object data before updating it","This hook allows to modify contact identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateCmsPageCategoryFormHandler","Modify cms page category identifiable object data before updating it","This hook allows to modify cms page category identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateTaxFormHandler","Modify tax identifiable object data before updating it","This hook allows to modify tax identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateManufacturerFormHandler","Modify manufacturer identifiable object data before updating it","This hook allows to modify manufacturer identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateEmployeeFormHandler","Modify employee identifiable object data before updating it","This hook allows to modify employee identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateProfileFormHandler","Modify profile identifiable object data before updating it","This hook allows to modify profile identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateCmsPageFormHandler","Modify cms page identifiable object data before updating it","This hook allows to modify cms page identifiable object forms data before it was updated","1"),
(NULL,"actionBeforeUpdateManufacturerAddressFormHandler","Modify manufacturer address identifiable object data before updating it","This hook allows to modify manufacturer address identifiable object forms data before it was updated","1"),
(NULL,"actionAfterUpdateSqlRequestFormHandler","Modify sql request identifiable object data after updating it","This hook allows to modify sql request identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateCustomerFormHandler","Modify customer identifiable object data after updating it","This hook allows to modify customer identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateLanguageFormHandler","Modify language identifiable object data after updating it","This hook allows to modify language identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateCurrencyFormHandler","Modify currency identifiable object data after updating it","This hook allows to modify currency identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateWebserviceKeyFormHandler","Modify webservice key identifiable object data after updating it","This hook allows to modify webservice key identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateMetaFormHandler","Modify meta identifiable object data after updating it","This hook allows to modify meta identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateCategoryFormHandler","Modify category identifiable object data after updating it","This hook allows to modify category identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateRootCategoryFormHandler","Modify root category identifiable object data after updating it","This hook allows to modify root category identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateContactFormHandler","Modify contact identifiable object data after updating it","This hook allows to modify contact identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateCmsPageCategoryFormHandler","Modify cms page category identifiable object data after updating it","This hook allows to modify cms page category identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateTaxFormHandler","Modify tax identifiable object data after updating it","This hook allows to modify tax identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateManufacturerFormHandler","Modify manufacturer identifiable object data after updating it","This hook allows to modify manufacturer identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateEmployeeFormHandler","Modify employee identifiable object data after updating it","This hook allows to modify employee identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateProfileFormHandler","Modify profile identifiable object data after updating it","This hook allows to modify profile identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateCmsPageFormHandler","Modify cms page identifiable object data after updating it","This hook allows to modify cms page identifiable object forms data after it was updated","1"),
(NULL,"actionAfterUpdateManufacturerAddressFormHandler","Modify manufacturer address identifiable object data after updating it","This hook allows to modify manufacturer address identifiable object forms data after it was updated","1"),
(NULL,"actionBeforeCreateSqlRequestFormHandler","Modify sql request identifiable object data before creating it","This hook allows to modify sql request identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateCustomerFormHandler","Modify customer identifiable object data before creating it","This hook allows to modify customer identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateLanguageFormHandler","Modify language identifiable object data before creating it","This hook allows to modify language identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateCurrencyFormHandler","Modify currency identifiable object data before creating it","This hook allows to modify currency identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateWebserviceKeyFormHandler","Modify webservice key identifiable object data before creating it","This hook allows to modify webservice key identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateMetaFormHandler","Modify meta identifiable object data before creating it","This hook allows to modify meta identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateCategoryFormHandler","Modify category identifiable object data before creating it","This hook allows to modify category identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateRootCategoryFormHandler","Modify root category identifiable object data before creating it","This hook allows to modify root category identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateContactFormHandler","Modify contact identifiable object data before creating it","This hook allows to modify contact identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateCmsPageCategoryFormHandler","Modify cms page category identifiable object data before creating it","This hook allows to modify cms page category identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateTaxFormHandler","Modify tax identifiable object data before creating it","This hook allows to modify tax identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateManufacturerFormHandler","Modify manufacturer identifiable object data before creating it","This hook allows to modify manufacturer identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateEmployeeFormHandler","Modify employee identifiable object data before creating it","This hook allows to modify employee identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateProfileFormHandler","Modify profile identifiable object data before creating it","This hook allows to modify profile identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateCmsPageFormHandler","Modify cms page identifiable object data before creating it","This hook allows to modify cms page identifiable object forms data before it was created","1"),
(NULL,"actionBeforeCreateManufacturerAddressFormHandler","Modify manufacturer address identifiable object data before creating it","This hook allows to modify manufacturer address identifiable object forms data before it was created","1"),
(NULL,"actionAfterCreateSqlRequestFormHandler","Modify sql request identifiable object data after creating it","This hook allows to modify sql request identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateCustomerFormHandler","Modify customer identifiable object data after creating it","This hook allows to modify customer identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateLanguageFormHandler","Modify language identifiable object data after creating it","This hook allows to modify language identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateCurrencyFormHandler","Modify currency identifiable object data after creating it","This hook allows to modify currency identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateWebserviceKeyFormHandler","Modify webservice key identifiable object data after creating it","This hook allows to modify webservice key identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateMetaFormHandler","Modify meta identifiable object data after creating it","This hook allows to modify meta identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateCategoryFormHandler","Modify category identifiable object data after creating it","This hook allows to modify category identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateRootCategoryFormHandler","Modify root category identifiable object data after creating it","This hook allows to modify root category identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateContactFormHandler","Modify contact identifiable object data after creating it","This hook allows to modify contact identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateCmsPageCategoryFormHandler","Modify cms page category identifiable object data after creating it","This hook allows to modify cms page category identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateTaxFormHandler","Modify tax identifiable object data after creating it","This hook allows to modify tax identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateManufacturerFormHandler","Modify manufacturer identifiable object data after creating it","This hook allows to modify manufacturer identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateEmployeeFormHandler","Modify employee identifiable object data after creating it","This hook allows to modify employee identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateProfileFormHandler","Modify profile identifiable object data after creating it","This hook allows to modify profile identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateCmsPageFormHandler","Modify cms page identifiable object data after creating it","This hook allows to modify cms page identifiable object forms data after it was created","1"),
(NULL,"actionAfterCreateManufacturerAddressFormHandler","Modify manufacturer address identifiable object data after creating it","This hook allows to modify manufacturer address identifiable object forms data after it was created","1"),
(NULL,"actionShippingPreferencesPageForm","Modify shipping preferences page options form content","This hook allows to modify shipping preferences page options form FormBuilder","1"),
(NULL,"actionOrdersInvoicesByDateForm","Modify orders invoices by date options form content","This hook allows to modify orders invoices by date options form FormBuilder","1"),
(NULL,"actionOrdersInvoicesByStatusForm","Modify orders invoices by status options form content","This hook allows to modify orders invoices by status options form FormBuilder","1"),
(NULL,"actionOrdersInvoicesOptionsForm","Modify orders invoices options options form content","This hook allows to modify orders invoices options options form FormBuilder","1"),
(NULL,"actionCustomerPreferencesPageForm","Modify customer preferences page options form content","This hook allows to modify customer preferences page options form FormBuilder","1"),
(NULL,"actionOrderPreferencesPageForm","Modify order preferences page options form content","This hook allows to modify order preferences page options form FormBuilder","1"),
(NULL,"actionProductPreferencesPageForm","Modify product preferences page options form content","This hook allows to modify product preferences page options form FormBuilder","1"),
(NULL,"actionGeneralPageForm","Modify general page options form content","This hook allows to modify general page options form FormBuilder","1"),
(NULL,"actionLogsPageForm","Modify logs page options form content","This hook allows to modify logs page options form FormBuilder","1"),
(NULL,"actionOrderDeliverySlipOptionsForm","Modify order delivery slip options options form content","This hook allows to modify order delivery slip options options form FormBuilder","1"),
(NULL,"actionOrderDeliverySlipPdfForm","Modify order delivery slip pdf options form content","This hook allows to modify order delivery slip pdf options form FormBuilder","1"),
(NULL,"actionGeolocationPageForm","Modify geolocation page options form content","This hook allows to modify geolocation page options form FormBuilder","1"),
(NULL,"actionLocalizationPageForm","Modify localization page options form content","This hook allows to modify localization page options form FormBuilder","1"),
(NULL,"actionPaymentPreferencesForm","Modify payment preferences options form content","This hook allows to modify payment preferences options form FormBuilder","1"),
(NULL,"actionEmailConfigurationForm","Modify email configuration options form content","This hook allows to modify email configuration options form FormBuilder","1"),
(NULL,"actionRequestSqlForm","Modify request sql options form content","This hook allows to modify request sql options form FormBuilder","1"),
(NULL,"actionBackupForm","Modify backup options form content","This hook allows to modify backup options form FormBuilder","1"),
(NULL,"actionWebservicePageForm","Modify webservice page options form content","This hook allows to modify webservice page options form FormBuilder","1"),
(NULL,"actionMetaPageForm","Modify meta page options form content","This hook allows to modify meta page options form FormBuilder","1"),
(NULL,"actionEmployeeForm","Modify employee options form content","This hook allows to modify employee options form FormBuilder","1"),
(NULL,"actionCurrencyForm","Modify currency options form content","This hook allows to modify currency options form FormBuilder","1"),
(NULL,"actionShopLogoForm","Modify shop logo options form content","This hook allows to modify shop logo options form FormBuilder","1"),
(NULL,"actionTaxForm","Modify tax options form content","This hook allows to modify tax options form FormBuilder","1"),
(NULL,"actionMailThemeForm","Modify mail theme options form content","This hook allows to modify mail theme options form FormBuilder","1"),
(NULL,"actionPerformancePageSave","Modify performance page options form saved data","This hook allows to modify data of performance page options form after it was saved","1"),
(NULL,"actionMaintenancePageSave","Modify maintenance page options form saved data","This hook allows to modify data of maintenance page options form after it was saved","1"),
(NULL,"actionAdministrationPageSave","Modify administration page options form saved data","This hook allows to modify data of administration page options form after it was saved","1"),
(NULL,"actionShippingPreferencesPageSave","Modify shipping preferences page options form saved data","This hook allows to modify data of shipping preferences page options form after it was saved","1"),
(NULL,"actionOrdersInvoicesByDateSave","Modify orders invoices by date options form saved data","This hook allows to modify data of orders invoices by date options form after it was saved","1"),
(NULL,"actionOrdersInvoicesByStatusSave","Modify orders invoices by status options form saved data","This hook allows to modify data of orders invoices by status options form after it was saved","1"),
(NULL,"actionOrdersInvoicesOptionsSave","Modify orders invoices options options form saved data","This hook allows to modify data of orders invoices options options form after it was saved","1"),
(NULL,"actionCustomerPreferencesPageSave","Modify customer preferences page options form saved data","This hook allows to modify data of customer preferences page options form after it was saved","1"),
(NULL,"actionOrderPreferencesPageSave","Modify order preferences page options form saved data","This hook allows to modify data of order preferences page options form after it was saved","1"),
(NULL,"actionProductPreferencesPageSave","Modify product preferences page options form saved data","This hook allows to modify data of product preferences page options form after it was saved","1"),
(NULL,"actionGeneralPageSave","Modify general page options form saved data","This hook allows to modify data of general page options form after it was saved","1"),
(NULL,"actionLogsPageSave","Modify logs page options form saved data","This hook allows to modify data of logs page options form after it was saved","1"),
(NULL,"actionOrderDeliverySlipOptionsSave","Modify order delivery slip options options form saved data","This hook allows to modify data of order delivery slip options options form after it was saved","1"),
(NULL,"actionOrderDeliverySlipPdfSave","Modify order delivery slip pdf options form saved data","This hook allows to modify data of order delivery slip pdf options form after it was saved","1"),
(NULL,"actionGeolocationPageSave","Modify geolocation page options form saved data","This hook allows to modify data of geolocation page options form after it was saved","1"),
(NULL,"actionLocalizationPageSave","Modify localization page options form saved data","This hook allows to modify data of localization page options form after it was saved","1"),
(NULL,"actionPaymentPreferencesSave","Modify payment preferences options form saved data","This hook allows to modify data of payment preferences options form after it was saved","1"),
(NULL,"actionEmailConfigurationSave","Modify email configuration options form saved data","This hook allows to modify data of email configuration options form after it was saved","1"),
(NULL,"actionRequestSqlSave","Modify request sql options form saved data","This hook allows to modify data of request sql options form after it was saved","1"),
(NULL,"actionBackupSave","Modify backup options form saved data","This hook allows to modify data of backup options form after it was saved","1"),
(NULL,"actionWebservicePageSave","Modify webservice page options form saved data","This hook allows to modify data of webservice page options form after it was saved","1"),
(NULL,"actionMetaPageSave","Modify meta page options form saved data","This hook allows to modify data of meta page options form after it was saved","1"),
(NULL,"actionEmployeeSave","Modify employee options form saved data","This hook allows to modify data of employee options form after it was saved","1"),
(NULL,"actionCurrencySave","Modify currency options form saved data","This hook allows to modify data of currency options form after it was saved","1"),
(NULL,"actionShopLogoSave","Modify shop logo options form saved data","This hook allows to modify data of shop logo options form after it was saved","1"),
(NULL,"actionTaxSave","Modify tax options form saved data","This hook allows to modify data of tax options form after it was saved","1"),
(NULL,"actionMailThemeSave","Modify mail theme options form saved data","This hook allows to modify data of mail theme options form after it was saved","1"),
(NULL,"actionCategoryGridDefinitionModifier","Modify category grid definition","This hook allows to alter category grid columns, actions and filters","1"),
(NULL,"actionEmployeeGridDefinitionModifier","Modify employee grid definition","This hook allows to alter employee grid columns, actions and filters","1"),
(NULL,"actionContactGridDefinitionModifier","Modify contact grid definition","This hook allows to alter contact grid columns, actions and filters","1"),
(NULL,"actionCustomerGridDefinitionModifier","Modify customer grid definition","This hook allows to alter customer grid columns, actions and filters","1"),
(NULL,"actionLanguageGridDefinitionModifier","Modify language grid definition","This hook allows to alter language grid columns, actions and filters","1"),
(NULL,"actionCurrencyGridDefinitionModifier","Modify currency grid definition","This hook allows to alter currency grid columns, actions and filters","1"),
(NULL,"actionSupplierGridDefinitionModifier","Modify supplier grid definition","This hook allows to alter supplier grid columns, actions and filters","1"),
(NULL,"actionProfileGridDefinitionModifier","Modify profile grid definition","This hook allows to alter profile grid columns, actions and filters","1"),
(NULL,"actionCmsPageCategoryGridDefinitionModifier","Modify cms page category grid definition","This hook allows to alter cms page category grid columns, actions and filters","1"),
(NULL,"actionTaxGridDefinitionModifier","Modify tax grid definition","This hook allows to alter tax grid columns, actions and filters","1"),
(NULL,"actionManufacturerGridDefinitionModifier","Modify manufacturer grid definition","This hook allows to alter manufacturer grid columns, actions and filters","1"),
(NULL,"actionManufacturerAddressGridDefinitionModifier","Modify manufacturer address grid definition","This hook allows to alter manufacturer address grid columns, actions and filters","1"),
(NULL,"actionCmsPageGridDefinitionModifier","Modify cms page grid definition","This hook allows to alter cms page grid columns, actions and filters","1"),
(NULL,"actionBackupGridQueryBuilderModifier","Modify backup grid query builder","This hook allows to alter Doctrine query builder for backup grid","1"),
(NULL,"actionCategoryGridQueryBuilderModifier","Modify category grid query builder","This hook allows to alter Doctrine query builder for category grid","1"),
(NULL,"actionEmployeeGridQueryBuilderModifier","Modify employee grid query builder","This hook allows to alter Doctrine query builder for employee grid","1"),
(NULL,"actionContactGridQueryBuilderModifier","Modify contact grid query builder","This hook allows to alter Doctrine query builder for contact grid","1"),
(NULL,"actionCustomerGridQueryBuilderModifier","Modify customer grid query builder","This hook allows to alter Doctrine query builder for customer grid","1"),
(NULL,"actionLanguageGridQueryBuilderModifier","Modify language grid query builder","This hook allows to alter Doctrine query builder for language grid","1"),
(NULL,"actionCurrencyGridQueryBuilderModifier","Modify currency grid query builder","This hook allows to alter Doctrine query builder for currency grid","1"),
(NULL,"actionSupplierGridQueryBuilderModifier","Modify supplier grid query builder","This hook allows to alter Doctrine query builder for supplier grid","1"),
(NULL,"actionProfileGridQueryBuilderModifier","Modify profile grid query builder","This hook allows to alter Doctrine query builder for profile grid","1"),
(NULL,"actionCmsPageCategoryGridQueryBuilderModifier","Modify cms page category grid query builder","This hook allows to alter Doctrine query builder for cms page category grid","1"),
(NULL,"actionTaxGridQueryBuilderModifier","Modify tax grid query builder","This hook allows to alter Doctrine query builder for tax grid","1"),
(NULL,"actionManufacturerGridQueryBuilderModifier","Modify manufacturer grid query builder","This hook allows to alter Doctrine query builder for manufacturer grid","1"),
(NULL,"actionManufacturerAddressGridQueryBuilderModifier","Modify manufacturer address grid query builder","This hook allows to alter Doctrine query builder for manufacturer address grid","1"),
(NULL,"actionCmsPageGridQueryBuilderModifier","Modify cms page grid query builder","This hook allows to alter Doctrine query builder for cms page grid","1"),
(NULL,"actionLogsGridDataModifier","Modify logs grid data","This hook allows to modify logs grid data","1"),
(NULL,"actionEmailLogsGridDataModifier","Modify email logs grid data","This hook allows to modify email logs grid data","1"),
(NULL,"actionSqlRequestGridDataModifier","Modify sql request grid data","This hook allows to modify sql request grid data","1"),
(NULL,"actionBackupGridDataModifier","Modify backup grid data","This hook allows to modify backup grid data","1"),
(NULL,"actionWebserviceKeyGridDataModifier","Modify webservice key grid data","This hook allows to modify webservice key grid data","1"),
(NULL,"actionMetaGridDataModifier","Modify meta grid data","This hook allows to modify meta grid data","1"),
(NULL,"actionCategoryGridDataModifier","Modify category grid data","This hook allows to modify category grid data","1"),
(NULL,"actionEmployeeGridDataModifier","Modify employee grid data","This hook allows to modify employee grid data","1"),
(NULL,"actionContactGridDataModifier","Modify contact grid data","This hook allows to modify contact grid data","1"),
(NULL,"actionCustomerGridDataModifier","Modify customer grid data","This hook allows to modify customer grid data","1"),
(NULL,"actionLanguageGridDataModifier","Modify language grid data","This hook allows to modify language grid data","1"),
(NULL,"actionCurrencyGridDataModifier","Modify currency grid data","This hook allows to modify currency grid data","1"),
(NULL,"actionSupplierGridDataModifier","Modify supplier grid data","This hook allows to modify supplier grid data","1"),
(NULL,"actionProfileGridDataModifier","Modify profile grid data","This hook allows to modify profile grid data","1"),
(NULL,"actionCmsPageCategoryGridDataModifier","Modify cms page category grid data","This hook allows to modify cms page category grid data","1"),
(NULL,"actionTaxGridDataModifier","Modify tax grid data","This hook allows to modify tax grid data","1"),
(NULL,"actionManufacturerGridDataModifier","Modify manufacturer grid data","This hook allows to modify manufacturer grid data","1"),
(NULL,"actionManufacturerAddressGridDataModifier","Modify manufacturer address grid data","This hook allows to modify manufacturer address grid data","1"),
(NULL,"actionCmsPageGridDataModifier","Modify cms page grid data","This hook allows to modify cms page grid data","1"),
(NULL,"actionCategoryGridFilterFormModifier","Modify category grid filters","This hook allows to modify filters for category grid","1"),
(NULL,"actionEmployeeGridFilterFormModifier","Modify employee grid filters","This hook allows to modify filters for employee grid","1"),
(NULL,"actionContactGridFilterFormModifier","Modify contact grid filters","This hook allows to modify filters for contact grid","1"),
(NULL,"actionCustomerGridFilterFormModifier","Modify customer grid filters","This hook allows to modify filters for customer grid","1"),
(NULL,"actionLanguageGridFilterFormModifier","Modify language grid filters","This hook allows to modify filters for language grid","1"),
(NULL,"actionCurrencyGridFilterFormModifier","Modify currency grid filters","This hook allows to modify filters for currency grid","1"),
(NULL,"actionSupplierGridFilterFormModifier","Modify supplier grid filters","This hook allows to modify filters for supplier grid","1"),
(NULL,"actionProfileGridFilterFormModifier","Modify profile grid filters","This hook allows to modify filters for profile grid","1"),
(NULL,"actionCmsPageCategoryGridFilterFormModifier","Modify cms page category grid filters","This hook allows to modify filters for cms page category grid","1"),
(NULL,"actionTaxGridFilterFormModifier","Modify tax grid filters","This hook allows to modify filters for tax grid","1"),
(NULL,"actionManufacturerGridFilterFormModifier","Modify manufacturer grid filters","This hook allows to modify filters for manufacturer grid","1"),
(NULL,"actionManufacturerAddressGridFilterFormModifier","Modify manufacturer address grid filters","This hook allows to modify filters for manufacturer address grid","1"),
(NULL,"actionCmsPageGridFilterFormModifier","Modify cms page grid filters","This hook allows to modify filters for cms page grid","1"),
(NULL,"actionCategoryGridPresenterModifier","Modify category grid template data","This hook allows to modify data which is about to be used in template for category grid","1"),
(NULL,"actionEmployeeGridPresenterModifier","Modify employee grid template data","This hook allows to modify data which is about to be used in template for employee grid","1"),
(NULL,"actionContactGridPresenterModifier","Modify contact grid template data","This hook allows to modify data which is about to be used in template for contact grid","1"),
(NULL,"actionCustomerGridPresenterModifier","Modify customer grid template data","This hook allows to modify data which is about to be used in template for customer grid","1"),
(NULL,"actionLanguageGridPresenterModifier","Modify language grid template data","This hook allows to modify data which is about to be used in template for language grid","1"),
(NULL,"actionCurrencyGridPresenterModifier","Modify currency grid template data","This hook allows to modify data which is about to be used in template for currency grid","1"),
(NULL,"actionSupplierGridPresenterModifier","Modify supplier grid template data","This hook allows to modify data which is about to be used in template for supplier grid","1"),
(NULL,"actionProfileGridPresenterModifier","Modify profile grid template data","This hook allows to modify data which is about to be used in template for profile grid","1"),
(NULL,"actionCmsPageCategoryGridPresenterModifier","Modify cms page category grid template data","This hook allows to modify data which is about to be used in template for cms page category grid","1"),
(NULL,"actionTaxGridPresenterModifier","Modify tax grid template data","This hook allows to modify data which is about to be used in template for tax grid","1"),
(NULL,"actionManufacturerGridPresenterModifier","Modify manufacturer grid template data","This hook allows to modify data which is about to be used in template for manufacturer grid","1"),
(NULL,"actionManufacturerAddressGridPresenterModifier","Modify manufacturer address grid template data","This hook allows to modify data which is about to be used in template for manufacturer address grid","1"),
(NULL,"actionCmsPageGridPresenterModifier","Modify cms page grid template data","This hook allows to modify data which is about to be used in template for cms page grid","1");
