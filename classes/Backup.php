<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class BackupCore
{
	/** @var integer Object id */
	public $id;

	/** @var string Last error messages */
	public $error;

	/**
	 * Creates a new backup object
	 *
	 * @param string $filename Filename of the backup file
	 */
	public function __construct($filename = NULL)
	{
		if ($filename)
			$this->id = self::getBackupPath($filename);
	}

	/**
	 * Get the full path of the backup file
	 *
	 * @param string $filename Filename of the backup file
	 * @return The full path of the backup file, or false if the backup file does not exists
	 */
	public static function getBackupPath($filename)
	{
		$backupdir = realpath(PS_ADMIN_DIR.'/backups/');

		if ($backupdir === false)
			die(Tools::displayError('Backups directory does not exist.'));

		// Check the realpath so we can validate the backup file is under the backup directory
		$backupfile = realpath($backupdir.'/'.$filename);
		if ($backupfile === false OR strncmp($backupdir, $backupfile, strlen($backupdir)) != 0)
			die (Tools::displayError());

		return $backupfile;
	}

	/**
	 * Get the URL used to retreive this backup file
	 *
	 * @return The url used to request the backup file
	 */
	public function getBackupURL()
	{
		$adminDir = __PS_BASE_URI__.substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__) );
		$adminDir = substr($adminDir, 0, strrpos($adminDir, '/'));

		return $adminDir.'/backup.php?filename='.basename($this->id);
	}

	/**
	 * Delete the current backup file
	 *
	 * @return boolean Deletion result, true on success
	 */
	public function delete()
	{
		if (!$this->id || !unlink($this->id))
		{
			$this->error = Tools::displayError('Error deleting').' '.($this->id ? '"'.$this->id.'"' : Tools::displayError('Invalid ID'));
			return false;
		}
		return true;
	}

	/**
	 * Deletes a range of backup files
	 *
	 * @return boolean True on success
	 */
	public function deleteSelection($list)
	{
		foreach ($list as $file)
		{
			$backup = new Backup($file);
			if (!$backup->delete())
			{
				$this->error = $backup->error;
				return false;
			}
		}
		return true;
	}

	/**
	 * Creates a new backup file
	 *
	 * @return boolean true on successful backup
	 */
	public function add()
	{
		if ( _DB_TYPE_ !== 'MySQL' )
		{
			$this->error = Tools::displayError('Sorry, backup currently only supports MySQL database types. You are using') . ' "' . _DB_TYPE_ . '"';
			return false;
		}

		if (!Configuration::get('PS_BACKUP_ALL'))
			$ignore_insert_table = array(_DB_PREFIX_.'connections', _DB_PREFIX_.'connections_page', _DB_PREFIX_.'connections_source', _DB_PREFIX_.'guest', _DB_PREFIX_.'statssearch');
		else
			$ignore_insert_table = array();
		
		// Generate some random number, to make it extra hard to guess backup file names
		$rand = dechex ( mt_rand(0, min(0xffffffff, mt_getrandmax() ) ) );
		$date = time();
		$backupfile = PS_ADMIN_DIR . '/backups/' . $date . '-' . $rand . '.sql';

		// Figure out what compression is available and open the file
		if (function_exists('bzopen'))
		{
			$backupfile .= '.bz2';
			$fp = @bzopen($backupfile, 'w');
		}
		else if (function_exists('gzopen'))
		{
			$backupfile .= '.gz';
			$fp = @gzopen($backupfile, 'w');
		}
		else
			$fp = @fopen($backupfile, 'w');

		if ($fp === false)
		{
			echo Tools::displayError('Unable to create backup file') . ' "' . addslashes($backupfile) . '"';
			return false;
		}

		$this->id = realpath($backupfile);

		fwrite($fp, '/* Backup for ' . Tools::getHttpHost(false, false) . __PS_BASE_URI__ . "\n *  at " . date($date) . "\n */\n");
		fwrite($fp, "\n".'SET NAMES \'utf8\';'."\n\n");

		// Find all tables
		$tables = Db::getInstance()->ExecuteS('SHOW TABLES');
		$found = 0;
		foreach ($tables as $table)
		{
			$table = current($table);

			// Skip tables which do not start with _DB_PREFIX_
			if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0)
				continue;

			// Export the table schema
			$schema = Db::getInstance()->ExecuteS('SHOW CREATE TABLE `' . $table . '`');

			if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table']))
			{
				fclose($fp);
				$this->delete();
				echo Tools::displayError('An error occurred while backing up. Unable to obtain the schema of').' "'.$table;
				return false;
			}

			fwrite($fp, '/* Scheme for table ' . $schema[0]['Table'] . " */\n");
			fwrite($fp, $schema[0]['Create Table'] . ";\n\n");

			if (!in_array($schema[0]['Table'], $ignore_insert_table))
			{
				$data = Db::getInstance()->ExecuteS('SELECT * FROM `' . $schema[0]['Table'] . '`', false);
				$sizeof = DB::getInstance()->NumRows();
				$lines = explode("\n", $schema[0]['Create Table']);

				if ($data AND $sizeof > 0)
				{
					// Export the table data
					fwrite($fp, 'INSERT INTO `' . $schema[0]['Table'] . "` VALUES\n");
					$i = 1;
					while ($row = DB::getInstance()->nextRow($data))
					{
						$s = '(';
						
						foreach ($row as $field => $value)
						{
							$tmp = "'" . mysql_real_escape_string($value) . "',";
							if($tmp != "'',")
								$s .= $tmp;
							else
							{
								foreach($lines AS $line)
									if (strpos($line, '`'.$field.'`') !== false)
									{	
										if (preg_match('/(.*NOT NULL.*)/Ui', $line))
											$s .= "'',";
										else
											$s .= 'NULL,';
										break;
									}
							}
						}
						$s = rtrim($s, ',');

						if ($i%200 == 0 AND $i < $sizeof)
							$s .= ");\nINSERT INTO `".$schema[0]['Table']."` VALUES\n";
						elseif ($i < $sizeof)
							$s .= "),\n";
						else
							$s .= ");\n";

						fwrite($fp, $s);
						++$i;
					}
				}
			}
			$found++;
		}

		fclose($fp);
		if ($found == 0)
		{
			$this->delete();
			echo Tools::displayError('No valid tables were found to backup.' );
			return false;
		}

		return true;
	}

}
