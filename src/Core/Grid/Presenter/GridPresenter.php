<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Presenter;

use PrestaShop\PrestaShop\Core\Grid\Action\PanelActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\MissingColumnInRowException;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;

/**
 * Class GridPresenter is responsible for presenting grid
 */
final class GridPresenter implements GridPresenterInterface
{
    /**
     * {@inheritdoc}
     */
    public function present(GridInterface $grid)
    {
        $gridArray = [
            'id' => $grid->getDefinition()->getId(),
            'name' => $grid->getDefinition()->getName(),
            'filter_form' => $grid->getFilterForm()->createView(),
        ];
        $gridArray['columns'] = $this->presentColumns($grid);

        $gridArray['actions'] = [
            'panel' => $this->presentPanelAction($grid),
            'bulk' => $this->presentBulkActions($grid),
        ];

        $gridArray['data'] = [
            'rows' => $this->presentRows($grid),
            'rows_total' => $grid->getData()->getRowsTotal(),
            'query' => $grid->getData()->getQuery(),
        ];

        $gridArray['pagination'] = [
            'offset' => $grid->getSearchCriteria()->getOffset(),
            'limit' => $grid->getSearchCriteria()->getLimit(),
        ];

        $gridArray['sorting'] = [
            'order_by' => $grid->getSearchCriteria()->getOrderBy(),
            'order_way' => $grid->getSearchCriteria()->getOrderWay(),
        ];

        return $gridArray;
    }

    /**
     * Present grid columns
     *
     * @param GridInterface $grid
     *
     * @return array Presented columns
     */
    private function presentColumns(GridInterface $grid)
    {
        $columnsArray = [];

        $columns = $grid->getDefinition()->getColumns();
        $positions = [];

        /** @var ColumnInterface $column */
        foreach ($columns as $key => $column) {
            $columnsArray[] = [
                'id' => $column->getId(),
                'name' => $column->getName(),
                'is_sortable' => $column->isSortable(),
                'is_filterable' => $column->isFilterable(),
                'type' => $column->getType(),
                'options' => $column->getOptions(),
            ];

            $positions[$key] = $column->getPosition();
        }

        array_multisort($positions, SORT_ASC, $columnsArray);

        return $columnsArray;
    }

    /**
     * Present bulk actions available for grid
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    private function presentBulkActions(GridInterface $grid)
    {
        $bulkActionsArray = [];

        foreach ($grid->getDefinition()->getBulkActions() as $bulkAction) {
            $bulkActionsArray[] = [
                'id' => $bulkAction->getId(),
                'name' => $bulkAction->getName(),
                'icon' => $bulkAction->getIcon(),
            ];
        }

        return $bulkActionsArray;
    }

    /**
     * Present available grid actions
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    private function presentPanelAction(GridInterface $grid)
    {
        $panelActions = [];

        /** @var PanelActionInterface $panelAction */
        foreach ($grid->getDefinition()->getPanelActions() as $panelAction) {
            $actionView = [
                'id' => $panelAction->getId(),
                'name' => $panelAction->getName(),
                'icon' => $panelAction->getIcon(),
                'type' => $panelAction->getType(),
            ];

            $panelActions[] = $actionView;
        }

        return $panelActions;
    }

    /**
     * Present grid data
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    private function presentRows(GridInterface $grid)
    {
        $presentedRows = [];

        $rows = $grid->getData()->getRows();
        $columns = $grid->getDefinition()->getColumns();

        foreach ($rows as $row) {
            $rowData = $this->applyColumnModifications($row, $columns);

            $presentedRows[] = $rowData;
        }

        return $presentedRows;
    }

    /**
     * Some columns may modify row data
     *
     * @param array                     $row
     * @param ColumnCollectionInterface $columns
     *
     * @return array
     */
    private function applyColumnModifications(array $row, ColumnCollectionInterface $columns)
    {
        /** @var ColumnInterface $column */
        foreach ($columns as $column) {
            if (!is_callable($column->getModifier())) {
                continue;
            }

            $row[$column->getId()] = call_user_func($column->getModifier(), $row);
        }

        return $row;
    }
}
