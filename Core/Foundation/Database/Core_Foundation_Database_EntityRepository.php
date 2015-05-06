<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Core_Foundation_Database_EntityRepository
{
	protected $entityManager;
	protected $db;
	protected $tablesPrefix;
	protected $entityMetaData;
	protected $queryBuilder;

	public function __construct(
		Core_Foundation_Database_EntityManager $entityManager,
		$tablesPrefix,
		Core_Foundation_Database_EntityMetaData $entityMetaData
	)
	{
		$this->entityManager = $entityManager;
		$this->db = $this->entityManager->getDatabase();
		$this->tablesPrefix = $tablesPrefix;
		$this->entityMetaData = $entityMetaData;
		$this->queryBuilder = new Core_Foundation_Database_EntityManager_QueryBuilder($this->db);
	}

	public function __call($method, $arguments)
	{
		if (0 === strpos($method, 'findOneBy')) {
			$one = true;
			$by  = substr($method, 9);
		} else if (0 === strpos($method, 'findBy')) {
			$one = false;
			$by  = substr($method, 6);
		} else {
			throw new Exception(sprintf('Undefind method %s.', $method));
		}

		if (count($arguments) !== 1) {
			throw new Exception(sprintf('Method %s takes exactly one argument.', $method));
		}

		if (!$by) {
			$where = $arguments[0];
		} else {
			$where = array(
				$by => $arguments[0]
			);
		}

		return $this->doFind($one, $where);
	}

	protected function getIdFieldName()
	{
		$primary = $this->entityMetaData->getPrimaryKeyFieldnames();

		if (count($primary) === 0) {
			throw new Exception(
				sprintf(
					'No primary key defined in entity `%s`.',
					$this->entityMetaData->getEntityClassName()
				)
			);
		} else if (count($primary) > 1) {
			throw new Exception(
				sprintf(
					'Entity `%s` has a composite primary key, which is not supported by entity repositories.',
					$this->entityMetaData->getEntityClassName()
				)
			);
		}

		return $primary[0];
	}

	protected function getTableNameWithPrefix()
	{
		return $this->tablesPrefix . $this->entityMetaData->getTableName();
	}

	private function getNewEntity()
	{
		$entityClassName = $this->entityMetaData->getEntityClassName();
		return new $entityClassName;
	}

	/**
	 * This function takes an array of database rows as input
	 * and returns an hydrated entity if there is one row only.
	 *
	 * Null is returned when there are no rows, and an exception is thrown
	 * if there are too many rows.
	 *
	 * @param array $rows Database rows
	 */
	protected function hydrateOne(array $rows)
	{
		if (count($rows) === 0) {
			return null;
		} else if (count($rows) > 1) {
			throw new Exception('Too many rows returned.');
		} else {
			$data = $rows[0];
			$entity = $this->getNewEntity();
			$entity->hydrate($data);
			return $entity;
		}
	}

	protected function hydrateMany(array $rows)
	{
		$entities = array();
		foreach ($rows as $row) {
			$entity = $this->getNewEntity();
			$entity->hydrate($row);
			$entities[] = $entities;
		}
		return $entities;
	}

	private function doFind($one, array $cumulativeConditions)
	{
		$whereClause = $this->queryBuilder->buildWhereConditions('AND', $cumulativeConditions);

		$sql = 'SELECT * FROM ' . $this->getTableNameWithPrefix() . ' WHERE ' . $whereClause;

		$rows = $this->db->select($sql);

		if ($one) {
			return $this->hydrateOne($rows);
		} else {
			return $this->hydrateMany($rows);
		}
	}

	public function findOne($id)
	{
		$conditions = array();
		$conditions[$this->getIdFieldName()] = $id;

		return $this->doFind(true, $conditions);
	}

	public function findAll()
	{
		$sql = 'SELECT * FROM ' . $this->getTableNameWithPrefix();
		return $this->hydrateMany($this->db->select($sql));
	}
}
