SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_CONDITIONS';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'PS_PRODUCTS_PER_PAGE';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_PRODUCTS_ORDER_WAY';
UPDATE `PREFIX_configuration` SET value = '4' WHERE name = 'PS_PRODUCTS_ORDER_BY';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_DISPLAY_QTIES';
UPDATE `PREFIX_configuration` SET value = '20' WHERE name = 'PS_NB_DAYS_NEW_PRODUCT';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_BLOCK_CART_AJAX';
UPDATE `PREFIX_configuration` SET value = '131072' WHERE name = 'PS_PRODUCT_PICTURE_MAX_SIZE';
UPDATE `PREFIX_configuration` SET value = '64' WHERE name = 'PS_PRODUCT_PICTURE_WIDTH';
UPDATE `PREFIX_configuration` SET value = '64' WHERE name = 'PS_PRODUCT_PICTURE_HEIGHT';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_MINWORDLEN';
UPDATE `PREFIX_configuration` SET value = '6' WHERE name = 'PS_SEARCH_WEIGHT_PNAME';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'PS_SEARCH_WEIGHT_REF';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_WEIGHT_SHORTDESC';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_WEIGHT_DESC';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_WEIGHT_CNAME';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_WEIGHT_MNAME';
UPDATE `PREFIX_configuration` SET value = '4' WHERE name = 'PS_SEARCH_WEIGHT_TAG';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PS_SEARCH_WEIGHT_ATTRIBUTE';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PS_SEARCH_WEIGHT_FEATURE';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_AJAX';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_DISPLAY_JQZOOM';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_BESTSELLERS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_NEWPRODUCTS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_SPECIALS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_TAX_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_STORES_DISPLAY_CMS';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_STORES_DISPLAY_FOOTER';
UPDATE `PREFIX_configuration` SET value = '224' WHERE name = 'SHOP_LOGO_WIDTH';
UPDATE `PREFIX_configuration` SET value = '73' WHERE name = 'SHOP_LOGO_HEIGHT';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_DISPLAY_SUPPLIERS';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_LEGACY_IMAGES';
UPDATE `PREFIX_configuration` SET value = 'jpg' WHERE name = 'PS_IMAGE_QUALITY';
UPDATE `PREFIX_configuration` SET value = '7' WHERE name = 'PS_PNG_QUALITY';
UPDATE `PREFIX_configuration` SET value = '90' WHERE name = 'PS_JPEG_QUALITY';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PRODUCTS_VIEWED_NBR';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'BLOCK_CATEG_DHTML';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'BLOCK_CATEG_MAX_DEPTH';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'MANUFACTURER_DISPLAY_FORM';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'MANUFACTURER_DISPLAY_TEXT';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'MANUFACTURER_DISPLAY_TEXT_NB';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'NEW_PRODUCTS_NBR';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'BLOCKTAGS_NBR';
UPDATE `PREFIX_configuration` SET value = '0_3|0_4' WHERE name = 'FOOTER_CMS';
UPDATE `PREFIX_configuration` SET value = '0_3|0_4' WHERE name = 'FOOTER_BLOCK_ACTIVATION';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'FOOTER_POWEREDBY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'BLOCKADVERT_LINK';
UPDATE `PREFIX_configuration` SET value = 'store.jpg' WHERE name = 'BLOCKSTORE_IMG';
UPDATE `PREFIX_configuration` SET value = 'jpg' WHERE name = 'BLOCKADVERT_IMG_EXT';
UPDATE `PREFIX_configuration` SET value = 'CAT2,CAT3,CAT4' WHERE name = 'MOD_BLOCKTOPMENU_ITEMS';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'MOD_BLOCKTOPMENU_SEARCH';
UPDATE `PREFIX_configuration` SET value = 'http://www.facebook.com/prestashop' WHERE name = 'blocksocial_facebook';
UPDATE `PREFIX_configuration` SET value = 'http://www.twitter.com/prestashop' WHERE name = 'blocksocial_twitter';
UPDATE `PREFIX_configuration` SET value = 'RSS' WHERE name = 'blocksocial_rss';
UPDATE `PREFIX_configuration` SET value = 'Prestashop' WHERE name = 'blockcontactinfos_company';
UPDATE `PREFIX_configuration` SET value = '41, boulevard des capucines, 75002 Paris, France' WHERE name = 'blockcontactinfos_address';
UPDATE `PREFIX_configuration` SET value = '+33 (0)1.40.18.30.04' WHERE name = 'blockcontactinfos_phone';
UPDATE `PREFIX_configuration` SET value = 'pub@prestashop.com' WHERE name = 'blockcontactinfos_email';
UPDATE `PREFIX_configuration` SET value = '+33 (0)1.40.18.30.04' WHERE name = 'blockcontact_telnumber';
UPDATE `PREFIX_configuration` SET value = 'pub@prestashop.com' WHERE name = 'blockcontact_email';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_TEXT';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'SUPPLIER_DISPLAY_TEXT_NB';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_FORM';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'BLOCK_CATEG_NBR_COLUMN_FOOTER';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPDB_FILENAME';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPFILES_FILENAME';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'blockreinsurance_nbblocks';


TRUNCATE `ps_hook_module`;
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPayment'), (SELECT id_module FROM `ps_module` WHERE name = 'cheque'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPayment'), (SELECT id_module FROM `ps_module` WHERE name = 'bankwire'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPayment'), (SELECT id_module FROM `ps_module` WHERE name = 'moneybookers'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPaymentReturn'), (SELECT id_module FROM `ps_module` WHERE name = 'cheque'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPaymentReturn'), (SELECT id_module FROM `ps_module` WHERE name = 'bankwire'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayPaymentReturn'), (SELECT id_module FROM `ps_module` WHERE name = 'moneybookers'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blocknewproducts'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockspecials'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcms'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockstore'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcontact'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayRightColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blocknewsletter'), 8);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blocktags'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockviewed'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksupplier'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockmanufacturer'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcms'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockadvertising'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumn'), (SELECT id_module FROM `ps_module` WHERE name = 'blockpaymentlogo'), 8);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHome'), (SELECT id_module FROM `ps_module` WHERE name = 'homeslider'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHome'), (SELECT id_module FROM `ps_module` WHERE name = 'homefeatured'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockpaymentlogo'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockpermanentlinks'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockviewed'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcart'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksocial'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockmyaccount'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'homeslider'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 9);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockspecials'), 10);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcurrencies'), 11);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocknewproducts'), 12);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockuserinfo'), 13);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocklanguages'), 14);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockmanufacturer'), 15);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcms'), 16);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockadvertising'), 17);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocktags'), 18);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockstore'), 19);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksearch'), 20);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcontactinfos'), 21);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocktopmenu'), 22);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'favoriteproducts'), 22);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'homefeatured'), 23);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocknewsletter'), 24);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcontact'), 24);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksupplier'), 25);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayHeader'), (SELECT id_module FROM `ps_module` WHERE name = 'feeder'), 26);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionAuthentication'), (SELECT id_module FROM `ps_module` WHERE name = 'statsdata'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blocklanguages'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcurrencies'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blockpermanentlinks'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksearch'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blockuserinfo'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blocktopmenu'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcart'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'sekeywords'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayTop'), (SELECT id_module FROM `ps_module` WHERE name = 'pagesnotfound'), 8);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminOrder'), (SELECT id_module FROM `ps_module` WHERE name = 'statsorigin'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blockreinsurance'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blockmyaccount'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcms'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksocial'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcontactinfos'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksharefb'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayFooter'), (SELECT id_module FROM `ps_module` WHERE name = 'statsdata'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionCustomerAccountAdd'), (SELECT id_module FROM `ps_module` WHERE name = 'statsdata'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayCustomerAccount'), (SELECT id_module FROM `ps_module` WHERE name = 'favoriteproducts'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsvisits'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statssales'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsregistrations'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statspersonalinfos'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statslive'), 5);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsequipment'), 6);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statscatalog'), 7);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsbestcustomers'), 8);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsorigin'), 9);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'pagesnotfound'), 10);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'sekeywords'), 11);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsproduct'), 12);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsbestproducts'), 13);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsbestvouchers'), 14);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsbestcategories'), 15);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsbestsuppliers'), 16);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statscarrier'), 17);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsnewsletter'), 18);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statssearch'), 19);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statscheckup'), 20);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsstock'), 21);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsModules'), (SELECT id_module FROM `ps_module` WHERE name = 'statsforecast'), 22);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsGraphEngine'), (SELECT id_module FROM `ps_module` WHERE name = 'graphvisifire'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsGraphEngine'), (SELECT id_module FROM `ps_module` WHERE name = 'graphxmlswfcharts'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsGraphEngine'), (SELECT id_module FROM `ps_module` WHERE name = 'graphgooglechart'), 3);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsGraphEngine'), (SELECT id_module FROM `ps_module` WHERE name = 'graphartichow'), 4);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayAdminStatsGridEngine'), (SELECT id_module FROM `ps_module` WHERE name = 'gridhtml'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumnProduct'), (SELECT id_module FROM `ps_module` WHERE name = 'blocksharefb'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayLeftColumnProduct'), (SELECT id_module FROM `ps_module` WHERE name = 'favoriteproducts'), 2);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionSearch'), (SELECT id_module FROM `ps_module` WHERE name = 'statssearch'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionCategoryAdd'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionCategoryUpdate'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionCategoryDelete'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'actionAdminMetaSave'), (SELECT id_module FROM `ps_module` WHERE name = 'blockcategories'), 1);
INSERT IGNORE INTO `ps_hook_module` (`id_hook`, `id_module`, `position`) VALUES ((SELECT id_hook FROM `ps_hook` WHERE name = 'displayMyAccountBlock'), (SELECT id_module FROM `ps_module` WHERE name = 'favoriteproducts'), 1);
