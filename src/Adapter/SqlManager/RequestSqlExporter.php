<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter\SqlManager;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResultQuery;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;
use PrestaShop\PrestaShop\Core\Export\ExportDirectory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class RequestSqlExporter exports given Request SQL data.
 */
class RequestSqlExporter
{
    /**
     * @var ExportDirectory
     */
    private $exportDirectory;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param ExportDirectory $exportDirectory
     * @param ConfigurationInterface $configuration
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        ExportDirectory $exportDirectory,
        ConfigurationInterface $configuration,
        CommandBusInterface $queryBus
    ) {
        $this->exportDirectory = $exportDirectory;
        $this->configuration = $configuration;
        $this->queryBus = $queryBus;
    }

    /**
     * Export request sql data.
     *
     * @param int $requestSqlId
     *
     * @return BinaryFileResponse|null
     */
    public function export($requestSqlId)
    {
        try {
            $command = new GetSqlRequestExecutionResultQuery($requestSqlId);

            /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
            $sqlRequestExecutionResult = $this->queryBus->handle($command);
        } catch (SqlRequestException $e) {
            return null;
        }

        if (empty($sqlRequestExecutionResult->getRows())) {
            return null;
        }

        $fileName = sprintf('request_sql_%s.csv', $requestSqlId);
        if (!($csv = fopen($this->exportDirectory . $fileName, 'w'))) {
            return null;
        }

        fputcsv($csv, $sqlRequestExecutionResult->getColumns(), ';');

        foreach ($sqlRequestExecutionResult->getRows() as $row) {
            fputcsv($csv, $row, ';');
        }

        if (!file_exists($this->exportDirectory . $fileName)) {
            return null;
        }

        $charset = $this->configuration->get('PS_ENCODING_FILE_MANAGER_SQL') ?: CharsetEncoding::UTF_8;

        $response = new BinaryFileResponse($this->exportDirectory . $fileName);
        $response->setCharset($charset);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
