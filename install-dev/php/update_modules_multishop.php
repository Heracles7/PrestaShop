<?php

function update_modules_multishop()
{
	//BlockCMS
	$block_cms = Module::getInstanceByName('blockcms');
	if ($block_cms->id)
	{
		Db::getInstance()->execute('CREATE TABLE '._DB_PREFIX_.'blocklink_shop (
											`id_blocklink` int(2) NOT NULL AUTO_INCREMENT, 
											`id_shop` varchar(255) NOT NULL,
											PRIMARY KEY(`id_blocklink`, `id_shop`))
											ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');

		Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block_shop` (
				`id_cms_block` int(10) unsigned NOT NULL auto_increment,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_cms_block`, `id_shop`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'cms_block_shop (cms_block, id_shop) 
			(SELECT id_cms_block, 1 FROM '._DB_PREFIX_.'cms_block)');
	}
	$block_cms = Module::getInstanceByName('blocklink');
	Db::getInstance()->execute('
			CREATE TABLE '._DB_PREFIX_.'blocklink_shop (
			`id_blocklink` int(2) NOT NULL AUTO_INCREMENT, 
			`id_shop` varchar(255) NOT NULL,
			PRIMARY KEY(`id_blocklink`, `id_shop`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
	Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'blocklink_shop (id_blocklink, id_shop) 
			(SELECT id_blocklink, 1 FROM '._DB_PREFIX_.'blocklink)');
}

