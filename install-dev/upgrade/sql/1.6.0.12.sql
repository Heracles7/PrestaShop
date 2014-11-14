ALTER TABLE `PREFIX_product` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_product_shop` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_pack` ADD `id_product_attribute_item` int(10) UNSIGNED NOT NULL AFTER `id_product_item`;
ALTER TABLE `PREFIX_pack` DROP PRIMARY KEY;

/* PHP:p16012_pack_rework(); */;

ALTER TABLE `PREFIX_pack` ADD PRIMARY KEY (`id_product_pack`, `id_product_item`, `id_product_attribute_item`);
