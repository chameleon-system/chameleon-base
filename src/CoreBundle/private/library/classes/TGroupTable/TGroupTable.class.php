<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * class TGroupTable is used to display a flexible table with, and without data groupings.
 * /**/
class TGroupTable
{
    use ChameleonSystem\CoreBundle\BackwardsCompatibilityShims\NamedConstructorSupport;

    /**
     * TGroupTableStyle (see class definition to view available styles).
     *
     * @var TGroupTableStyle
     */
    public $style;

    /**
     * js function name to execute on an onClick event
     * should accept as many parameters as set via the field definition.
     *
     * @var mixed - string or null by default
     */
    public $onClick;

    /**
     * array of TGroupTableField (in order of display).
     *
     * @var TGroupTableField[]
     */
    public $columnList = [];

    /**
     * TGroupTableField to group by.
     *
     * @var TGroupTableField|null
     */
    public $groupByCell;

    /**
     * sql string used to get results for table.
     *
     * @var string
     */
    public $sql = '';

    /**
     * assoc array of db fields. format: field=>[ASC | DESC] (default = empty array).
     *
     * @var array
     */
    public $orderList = [];

    /**
     * indicates whether the sql has a WHERE condition (default = false).
     *
     * @var bool
     */
    public $hasCondition = false;

    /**
     * start display at what record number.
     *
     * @var int
     */
    public $startRecord = 0;

    /**
     * number of records to display (default = -1 => all).
     *
     * @var int
     */
    public $showRecordCount = -1;

    /**
     * the total number of records found.
     *
     * @var int
     */
    public $recordsFound = 0;

    /**
     * holds total number of records returned by main query.
     *
     * @var int
     */
    public $recordCount = 0;

    /**
     *holds the number of columns in the table.
     *
     * @var int
     */
    protected $_columnCount = 0;

    /**
     * set to true to show debug information.
     *
     * @var bool
     */
    public $_debug = false;

    /**
     * sql name of the table.
     *
     * @var string - null by default
     */
    public $sTableName;

    /**
     * the full generated SQL query without GROUP BY.
     *
     * @var string
     */
    public $fullQuery = '';

    /**
     * the full generated SQL query with GROUP BY.
     *
     * @var string
     */
    public $fullQueryGrouped = '';

    /**
     * if this is filled with the class object + callback method name
     * it will be used to style every row.
     *
     * @var array - null by default
     */
    public $rowCallback;

    /**
     * you may fill this via $this->table->SetCustomOrderString($sMyOrderString);
     * if a custom order string is set _GetOrderString() uses this
     * instead of generating it based on current configured order fields.
     *
     * @var string - null by default
     */
    protected $sCustomOrderString;

    private $languageId;

    public function __construct()
    {
        $this->style = new TGroupTableStyle();
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TGroupTable()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * set the group by cell.
     *
     * @param string $name - database name of the column,  name may be a string, or an array. if it is an array it should be of the form 'name'=>'full_name'
     * @param string $align - horizontal alignment to use in the cell
     * @param mixed $format - null by default or a callback function to use for the column (the function will get 2 parameters, the value, and the row. The string returned by the function will be displayed in the cell)
     * @param mixed $linkField - array or null by default, array of dbfield names whoes value will be passed to the javascript function defined by $this->onClick
     * @param int $colSpan - the colSpan parameter of the cell
     * @param mixed $selectFormat - string or null by default
     */
    public function AddGroupField($name, $align = 'left', $format = null, $linkField = null, $colSpan = 1, $selectFormat = null)
    {
        $this->groupByCell = new TGroupTableField($name, $align, $format, $linkField, $colSpan, $selectFormat);
    }

    /**
     * unset the group by cell.
     */
    public function RemoveGroupField()
    {
        $this->groupByCell = null;
    }

    /**
     * add a display column.
     *
     * @param string $name - database name of the column
     * @param string $align - horizontal alignment to use in the cell
     * @param callable(string, array<string, mixed>, string):(string|null)|null $formatCallBack - a callback function to use for the column (the function will get 3 parameters,
     *                                                                                          the value, the row and the name. The string returned by the function will be displayed
     *                                                                                          in the cell
     * @param array $linkField - an array of dbfield names whoes value will be passed to the javascript
     *                         function defined by $this->onClick
     * @param int $colSpan - the colSpan parameter of the cell
     * @param int $selectFormat - the colSpan parameter of the cell
     * @param string $sIdent - column identifier name
     * @param int $columnPosition - the array key position where the header will be added (array key starts with 0)
     * @param string $originalField
     * @param string $originalTable
     */
    public function AddColumn($name, $align = 'left', $formatCallBack = null, $linkField = null, $colSpan = 1, $selectFormat = null, $sIdent = null, $columnPosition = null, $originalField = null, $originalTable = null)
    {
        $this->_columnCount += $colSpan;
        $oField = new TGroupTableField($name, $align, $formatCallBack, $linkField, $colSpan, $selectFormat, $sIdent, $originalField, $originalTable);

        if (null !== $columnPosition && count($this->columnList) > 0) {
            $count = 0;
            $aTmpFields = [];
            reset($this->columnList);
            foreach ($this->columnList as $key => $oTmpField) {
                ++$count;
                if ($key == $columnPosition) {
                    $aTmpFields[$count] = $oField;
                    ++$count;
                }

                $aTmpFields[$count] = $oTmpField;
                ++$count;
            }

            $this->columnList = $aTmpFields;
        } else {
            $this->columnList[] = $oField;
        }
    }

    /**
     * removes a column by unique identifier.
     *
     * @param string $sIdent
     */
    public function RemoveColumn($sIdent)
    {
        reset($this->columnList);
        foreach ($this->columnList as $key => $oField) {
            /** @var $oField TGroupTableField */
            if ($oField->sIdent == $sIdent) {
                $this->_columnCount = $this->_columnCount - $oField->colSpan;
                unset($this->columnList[$key]);
            }
        }

        reset($this->columnList);
        $aTmpFields = [];
        foreach ($this->columnList as $oTmpField) {
            $aTmpFields[] = $oTmpField;
        }
        $this->columnList = $aTmpFields;
    }

    /**
     * display the table body (no <table> tag is included).
     *
     * @param bool $returnAsString - if true then the content is not displayed, but returned as a string
     *                             else the number of records is returned and the content directly shown
     *
     * @return int - count of records found
     */
    public function Display($returnAsString = false)
    {
        $mainSQL = $this->_GetMainSQL();
        $groupList = [];

        $this->recordCount = $this->GetNumberOfResultsForSubGroupQuery();
        if (is_null($mainSQL)) {
            $groupContent = '';
            $recordCount = 0;
            $group = null;
            $groupRow = null;
            $this->_DisplayGroup($groupContent, $group, $groupRow, $recordCount);
            $this->recordsFound = $recordCount;
            if ($returnAsString) {
                return $groupContent;
            } else {
                echo $groupContent;

                return $this->recordsFound;
            }
        } else {
            $aAvailableGroupsForPage = $this->getGroupsAvailableForCurrentPage();
            $groupRes = MySqlLegacySupport::getInstance()->query($mainSQL);
            $sqlError = MySqlLegacySupport::getInstance()->error();
            if (!empty($sqlError)) {
                $groupContent = '';
                $recordCount = 0;
                $group = null;
                $groupRow = null;
                $this->_DisplayGroup($groupContent, $group, $groupRow, $recordCount);
            } else {
                // always add the empty one...
                while ($group = MySqlLegacySupport::getInstance()->fetch_assoc($groupRes)) {
                    if (!empty($this->groupByCell->name)) {
                        $groupList[$group[$this->groupByCell->name]] = $group;
                    }
                }
            }
            $recordCount = 0;
            $table = '';
            // show all that do not belong to any category
            foreach ($groupList as $group => $groupRow) {
                if (!isset($aAvailableGroupsForPage[$group])) {
                    $recordCount += $this->GetNumberOfResultsForSubGroupQuery($group);
                    continue;
                }
                // before each group (if it is not the first) we display the spacer row
                $groupHeader = '';
                // first display the group itself
                $groupHeader .= '<tr>';
                $this->groupByCell->colSpan = $this->_columnCount;
                $groupHeader .= $this->groupByCell->Display($groupRow, $this->style->GetGroup(), $this->onClick);
                $groupHeader .= '</tr>';
                $groupContent = '';
                $limitNotOverstepped = $this->_DisplayGroup($groupContent, $group, $groupRow, $recordCount);
                if (!empty($groupContent)) {
                    $table .= $groupHeader.$groupContent;
                }
                if (!$limitNotOverstepped) {
                    break;
                }
            }
            $this->recordsFound = $recordCount;
            if ($returnAsString) {
                return $table;
            } else {
                echo $table;

                return $this->recordsFound;
            }
        }
    }

    /**
     * display one row. returns true if more records are allowed to display, false if not.
     *
     * @param string $groupContent
     * @param string $group
     * @param string $groupRow
     * @param int $recordCount
     *
     * @return bool
     */
    protected function _DisplayGroup(&$groupContent, $group, $groupRow, &$recordCount)
    {
        $groupContent = '';
        $iRecordsFoundInSubGroup = $this->GetNumberOfResultsForSubGroupQuery($group);
        $sql = $this->_GetSubSQL($group);
        if ($this->_debug) {
            echo "<pre>group ($group) sql:\n{$sql}\n</pre>\n";
        }
        $rowCount = 0;

        // add limit
        $offset = $this->startRecord - $recordCount;
        if ($offset > 0) {
            $recordSetCount = $iRecordsFoundInSubGroup;
            if ($recordSetCount <= $offset) {
                // not enough in this set. increase counter and exit function
                $recordCount += $recordSetCount;
            } else { // otherwise position it into the subset
                $sql .= " LIMIT {$offset}";
                if ($this->showRecordCount > 0) {
                    $sql .= ",{$this->showRecordCount}";
                }
                $recordCount += $offset;
            }
        } else {
            if ($this->showRecordCount > 0) {
                $sql .= " LIMIT 0,{$this->showRecordCount}";
            }
        }

        $rowRes = MySqlLegacySupport::getInstance()->query($sql);
        $sqlError = MySqlLegacySupport::getInstance()->error();
        $showGroup = true;
        $recordsDisplayed = 0;
        if (!empty($sqlError)) {
            $showGroup = false;
            $groupContent .= '<div class="alert alert-danger">'.$this->getTranslator()->trans('chameleon_system_core.record_list.sql_error').'</div>';
            $this->getLogger()->error(sprintf('SQL error occurred during _DisplayGroup in TGroupTable: %s', $sqlError));
        }

        $recordLimitOK = true;
        if ($showGroup) {
            while ($recordLimitOK && ($row = MySqlLegacySupport::getInstance()->fetch_assoc($rowRes))) {
                $row = $this->getFieldTranslationUtil()->copyTranslationsToDefaultFields($row);

                ++$recordsDisplayed;
                ++$recordCount;
                $recordLimitOK = (($this->showRecordCount <= 0) // no limit set
                    || (($this->showRecordCount > 0) // OR a limit is set AND
                        && ($recordCount - $this->startRecord < $this->showRecordCount) // the limit has not been overstepped
                    ));

                $rowStyle = '';
                if (!is_null($this->rowCallback)) {
                    $sRowCSS = call_user_func([$this->rowCallback[0], $this->rowCallback[1]], $row['id'], $row);
                    if (!empty($sRowCSS)) {
                        if (empty($rowStyle)) {
                            $rowStyle = 'class="'.$sRowCSS.'"';
                        } else {
                            $rowStyle = str_replace('class="', 'class="'.$sRowCSS.' ', $rowStyle);
                        }
                    }
                }

                $groupContent .= '<tr data-record-id="'.$row['id'].'" '.$rowStyle." class=\"TGroupTableItemRow\">\n";

                $cssClass = '';
                if (null !== $this->onClick) {
                    $cssClass = 'action';
                }
                foreach ($this->columnList as $columnNumber => $column) {
                    $groupContent .= $column->Display($row, $cssClass, $this->onClick);
                }
                $groupContent .= "</tr>\n";
                ++$rowCount;
            }
        }

        return $recordLimitOK;
    }

    /**
     * builds the sql to get a list of groups...
     *
     * @return string
     */
    protected function _GetMainSQL()
    {
        if (is_null($this->groupByCell)) {
            $sql = null;
        } else {
            $sql = $this->sql;
            $field = $this->_EscapeField($this->groupByCell->name);
            if (stristr($sql, 'GROUP BY')) {
                $sql = str_replace(' GROUP BY ', ' GROUP BY '.$field.', ', $sql);
            } else {
                $sql .= ' GROUP BY '.$field;
            }
            $sql .= ' '.$this->_GetOrderString();
            $this->fullQueryGrouped = $sql;
        }

        return $sql;
    }

    /**
     * get an array of groups that have records on the current page.
     *
     * @return array
     */
    protected function getGroupsAvailableForCurrentPage()
    {
        $aGroups = [];
        if (null !== $this->groupByCell) {
            $sql = $this->sql;
            $oRecordList = new TCMSRecordList();
            $oRecordList->sTableName = $this->sTableName;
            $oRecordList->Load($sql);
            $iLength = $oRecordList->Length();

            $startPos = $this->startRecord; // TODO this might be an "old" paging value; searching (or other?) should reset it
            $limitLength = $iLength - $this->startRecord;

            if ($this->startRecord >= $iLength) {
                $this->startRecord = 0;
                $limitLength = $iLength;
            }
            if ($this->showRecordCount > 0) {
                $limitLength = min($limitLength, $this->showRecordCount);
            }

            $sql .= ' '.$this->_GetOrderString();
            $sql .= ' LIMIT '.$this->startRecord.', '.$limitLength;
            $sGroupedQuery = 'SELECT * FROM ('.$sql.') AS originalQuery GROUP BY '.MySqlLegacySupport::getInstance()->real_escape_string($this->groupByCell->name);
            $rGroupRes = MySqlLegacySupport::getInstance()->query($sGroupedQuery);
            while ($aGroup = MySqlLegacySupport::getInstance()->fetch_assoc($rGroupRes)) {
                if (!empty($this->groupByCell->name)) {
                    $aGroups[$aGroup[$this->groupByCell->name]] = $aGroup;
                }
            }
        }

        return $aGroups;
    }

    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @param null $languageId
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * @param string $group
     *
     * @return int
     */
    protected function getNumberOfRecordsForGroupAlreadyShownOnPreviousPages($group)
    {
        $sql = $this->sql;
        $sql .= ' '.$this->_GetOrderString();
        $sql .= ' LIMIT 0, '.intval($this->startRecord);
        $sCountingQuery = 'SELECT COUNT(*) as count FROM ('.$sql.') AS originalQuery WHERE '.MySqlLegacySupport::getInstance()->real_escape_string($this->groupByCell->name)." = '".MySqlLegacySupport::getInstance()->real_escape_string($group)."'";
        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($sCountingQuery));

        return intval($aRow['count']);
    }

    /**
     * builds the sql to get list entries for a group.
     *
     * @param mixed $groupValue - string or null by default
     *
     * @return string
     */
    protected function _GetSubSQL($groupValue = null)
    {
        $sql = $this->sql;
        if (!is_null($this->groupByCell) && !is_null($groupValue)) {
            if ($this->hasCondition) {
                $sql .= ' AND (';
            } else {
                $sql .= ' WHERE (';
            }
            $field = $this->_EscapeField($this->groupByCell->full_db_name);
            $field = $this->getFieldTranslationUtil()->getTranslatedQuery($field);
            if ('' === $groupValue) {
                $sql .= " ({$field} IS NULL OR {$field} = '') ";
            } else {
                $sql .= " {$field} = '".MySqlLegacySupport::getInstance()->real_escape_string($groupValue)."'";
            }
            $sql .= ')';
        }
        $sql .= ' '.$this->_GetOrderString();
        $this->fullQuery = $sql;

        return $sql;
    }

    /**
     * @param string|null $groupValue
     *
     * @return int
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function GetNumberOfResultsForSubGroupQuery($groupValue = null)
    {
        $sql = $this->sql;
        if (!is_null($this->groupByCell) && !is_null($groupValue)) {
            if ($this->hasCondition) {
                $sql .= ' AND (';
            } else {
                $sql .= ' WHERE (';
            }
            $field = $this->_EscapeField($this->groupByCell->full_db_name);
            $field = $this->getFieldTranslationUtil()->getTranslatedQuery($field);
            if ('' === $groupValue) {
                $sql .= " ({$field} IS NULL OR {$field} = '') ";
            } else {
                $sql .= " {$field} = '".MySqlLegacySupport::getInstance()->real_escape_string($groupValue)."'";
            }
            $sql .= ')';
        }
        /** @var $oRecordList TCMSRecordList */
        $oRecordList = new TCMSRecordList();
        $oRecordList->sTableName = $this->sTableName;
        $oRecordList->Load($sql);

        return $oRecordList->Length();
    }

    /**
     * returns order by string.
     *
     * @return string
     */
    protected function _GetOrderString()
    {
        $orderBy = '';

        if (!is_null($this->sCustomOrderString)) {
            $orderBy = ' '.$this->sCustomOrderString.' ';
        } else {
            $isFirst = true;
            foreach ($this->orderList as $field => $dir) {
                if (!empty($field)) {
                    if ($isFirst) {
                        $isFirst = false;
                    } else {
                        $orderBy .= ', ';
                    }
                    $field = $this->_EscapeField($field);
                    $orderBy .= "{$field} {$dir}";
                }
            }
            if (!empty($orderBy)) {
                $orderBy = ' ORDER BY '.$orderBy;
            }
        }
        $orderBy = $this->getFieldTranslationUtil()->getTranslatedQuery($orderBy);

        return $orderBy;
    }

    /**
     * returns an escaped version of the $field, if it was not already escaped.
     *
     * @param string $field
     *
     * @return string
     */
    protected function _EscapeField($field)
    {
        $databaseConnection = $this->getDatabaseConnection();
        if (false === mb_strpos($field, $databaseConnection->getDatabasePlatform()->getIdentifierQuoteCharacter())) {
            $tfield = $databaseConnection->quoteIdentifier($field);
        } else {
            $tfield = $field;
        }

        return $tfield;
    }

    /**
     * use this method to set a custom ORDER BY string for the query
     * the query part needs to start with "ORDER BY".
     *
     * @param string $sCustomOrderString
     */
    public function SetCustomOrderString($sCustomOrderString = null)
    {
        if (!is_null($sCustomOrderString)) {
            $this->sCustomOrderString = $sCustomOrderString;
        }
    }

    /**
     * @return ChameleonSystem\CoreBundle\Util\FieldTranslationUtil
     */
    protected function getFieldTranslationUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
