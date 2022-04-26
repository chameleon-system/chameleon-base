<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder;

use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use Doctrine\Common\Collections\Expr\Comparison;
use ErrorException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TCMSLogChange;
use TPkgCmsException_Log;
use ViewRenderer;

/**
 * QueryWriter writes queries to a file in order to replay them on another Chameleon installation.
 */
class QueryWriter
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; // used to get the non-shared snippet renderer service
    }

    /**
     * @param resource             $filePointer
     * @param LogChangeDataModel[] $dataModels
     *
     * @return void
     */
    public function writeQueries($filePointer, array $dataModels)
    {
        foreach ($dataModels as $dataModel) {
            $this->writeQuery($filePointer, $dataModel);
        }
    }

    /**
     * @param resource           $filePointer
     * @param LogChangeDataModel $dataModel
     *
     * @return void
     */
    public function writeQuery($filePointer, LogChangeDataModel $dataModel)
    {
        switch ($dataModel->getType()) {
            case LogChangeDataModel::TYPE_CUSTOM_QUERY:
                $this->writeLiteralQueryToFile($filePointer, $dataModel->getData());
                break;
            case LogChangeDataModel::TYPE_INSERT:
            case LogChangeDataModel::TYPE_UPDATE:
            case LogChangeDataModel::TYPE_DELETE:
                $this->writeQueryToFile($filePointer, $dataModel->getData(), $dataModel->getType());
                break;
            default:
                throw new \InvalidArgumentException('Invalid update type: '.$dataModel->getType());
        }
    }

    /**
     * @param resource           $filePointer
     * @param MigrationQueryData $migrationQueryData
     *
     * @return void
     */
    public function writeInsertQuery($filePointer, MigrationQueryData $migrationQueryData)
    {
        $this->writeQueryToFile($filePointer, $migrationQueryData, LogChangeDataModel::TYPE_INSERT);
    }

    /**
     * @param resource           $filePointer
     * @param MigrationQueryData $migrationQueryData
     *
     * @return void
     */
    public function writeUpdateQuery($filePointer, MigrationQueryData $migrationQueryData)
    {
        $this->writeQueryToFile($filePointer, $migrationQueryData, LogChangeDataModel::TYPE_UPDATE);
    }

    /**
     * @param resource           $filePointer
     * @param MigrationQueryData $migrationQueryData
     *
     * @return void
     */
    public function writeDeleteQuery($filePointer, MigrationQueryData $migrationQueryData)
    {
        $this->writeQueryToFile($filePointer, $migrationQueryData, LogChangeDataModel::TYPE_DELETE);
    }


    /**
     * @param resource $filePointer
     * @param MigrationQueryData $migrationQueryData
     * @param string $operationType
     *
     * @return void
     */
    private function writeQueryToFile($filePointer, MigrationQueryData $migrationQueryData, $operationType)
    {
        $snippetrenderer = $this->getViewRenderer();
        $snippetrenderer->AddSourceObject('operationType', $operationType);
        $snippetrenderer->AddSourceObject('tableName', $migrationQueryData->getTableName());
        $snippetrenderer->AddSourceObject('language', $migrationQueryData->getLanguage());
        $snippetrenderer->AddSourceObject('fields', $this->getArrayValuesToWrite($operationType, $migrationQueryData->getFields()));
        $snippetrenderer->AddSourceObject('whereEquals', $this->getArrayValuesToWrite($operationType, $migrationQueryData->getWhereEquals()));
        $snippetrenderer->AddSourceObject('whereExpressions', $this->getExpressionValuesToWrite($migrationQueryData->getWhereExpressions()));

        $renderedQuery = $snippetrenderer->Render('MigrationRecorder/migrationQueryTemplate.html.twig');
        fwrite($filePointer, $renderedQuery, strlen($renderedQuery));
    }

    private function getArrayValuesToWrite(string $operationType, array $arrayToWrite): array
    {
        $retValue = [];
        foreach ($arrayToWrite as $fieldName => $value) {
            $retValue[$fieldName] = $this->getValueToWrite($operationType, $fieldName, $value);
        }

        return $retValue;
    }

    /**
     * @param Comparison[] $expressionsToWrite
     * @return array
     */
    private function getExpressionValuesToWrite(array $expressionsToWrite): array
    {
        $retValue = [];
        foreach ($expressionsToWrite as $expression) {
            $retValue[] = new Comparison($expression->getField(), $expression->getOperator(), $this->quotePhpValue($expression->getValue()->getValue()));
        }

        return $retValue;
    }

    /**
     * @param string $operationType
     * @param string $fieldName
     * @param string $value
     *
     * @return string
     */
    private function getValueToWrite($operationType, $fieldName, $value)
    {
        if (LogChangeDataModel::TYPE_DELETE === $operationType) {
            return $this->quotePhpValue($value);
        }
        switch ($fieldName) {
            case 'cms_tbl_conf_id':
                $valueToWrite = $this->getTableConfIdValueToWrite($value);
                break;
            case 'cms_field_type_id':
                $valueToWrite = $this->getFieldTypeValueToWrite($value);
                break;
            default:
                $valueToWrite = $this->quotePhpValue($value);
                break;
        }

        return $valueToWrite;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getTableConfIdValueToWrite($value)
    {
        $tableName = TCMSLogChange::getTableName($value);

        return "TCMSLogChange::GetTableId('$tableName')";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function getFieldTypeValueToWrite($value)
    {
        $constantName = TCMSLogChange::getFieldConstantName($value);

        return "TCMSLogChange::GetFieldType('$constantName')";
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function quotePhpValue($value)
    {
        return "'".str_replace("'", "\'", $value)."'";
    }

    /**
     * @param resource $filePointer
     * @param string   $query
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     *
     * @return void
     */
    public function writeLiteralQueryToFile($filePointer, $query)
    {
        // we need to escape \ chars, and " chars
        $query = str_replace('\\', '\\\\', $query);
        $query = str_replace('"', '\\"', $query);

        // handle field conf table
        $sSearchString = "`cms_tbl_conf_id` = '";
        if (false !== mb_strpos($query, $sSearchString)) {
            $aQueryParts = explode($sSearchString, $query);
            $iPos = mb_strpos($aQueryParts[1], "'");
            if (false !== $iPos) {
                $sTblConfId = mb_substr($aQueryParts[1], 0, $iPos);
                $tableName = TCMSLogChange::getTableName($sTblConfId);
                if (null !== $tableName) {
                    $query = str_replace($sSearchString.$sTblConfId."'", $sSearchString.'".TCMSLogChange::GetTableId(\''.$tableName.'\')."\'', $query);
                }
            }
        }

        // handle field type
        $sSearchString = "`cms_field_type_id` = '";
        if (false !== mb_strpos($query, $sSearchString)) {
            $aQueryParts = explode($sSearchString, $query);
            $iPos = mb_strpos($aQueryParts[1], "'");
            if (false !== $iPos) {
                $sFieldTypeId = mb_substr($aQueryParts[1], 0, $iPos);
                $sFieldTypeConstant = TCMSLogChange::getFieldConstantName($sFieldTypeId);
                if (null !== $sFieldTypeConstant) {
                    $query = str_replace($sSearchString.$sFieldTypeId."'", $sSearchString.'".TCMSLogChange::GetFieldType(\''.$sFieldTypeConstant.'\')."\'', $query);
                }
            }
        }

        $snippetRenderer = $this->getViewRenderer();
        $snippetRenderer->AddSourceObject('query', $query);

        $renderedQuery = $snippetRenderer->Render('MigrationRecorder/migrationLiteralQueryTemplate.html.twig');
        fwrite($filePointer, $renderedQuery, strlen($renderedQuery));
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        $viewRenderer = $this->container->get('chameleon_system_view_renderer.view_renderer');
        $viewRenderer->setShowHTMLHints(false);

        return $viewRenderer;
    }
}
