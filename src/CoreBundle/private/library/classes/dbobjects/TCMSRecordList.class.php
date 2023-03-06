<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\core\DatabaseAccessLayer\EntityList;
use ChameleonSystem\core\DatabaseAccessLayer\QueryModifierOrderByInterface;
use ChameleonSystem\CoreBundle\BackwardsCompatibilityShims\NamedConstructorSupport;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use Doctrine\DBAL\Connection;

/**
 * creates an iterator of TCMSRecord and exposes its interface the idea is to
 * provide a simply and quick to use class to fetch a collection of records.
 *
 * @template T extends TCMSRecord
 * @extends TIterator<T>
 */
class TCMSRecordList extends TIterator
{
    use NamedConstructorSupport;

    /**
     * class name used for each record.
     *
     * @var string
     * @psalm-var class-string<T>
     */
    public $sTableObject = null;

    /**
     * if the record has translations, then iLanguageId can
     * be used to specify the language to use (set via public function: SetLanguage(id).
     *
     * @var int
     */
    protected $iLanguageId = null;

    /**
     * query used to fetch the data.
     *
     * @var string
     */
    protected $sQuery = null;

    /**
     * table name of the sql object (optional - only required if sTableObject is set).
     *
     * @var string
     */
    public $sTableName = null;

    /**
     * holds a list of all ids of the current record set.
     *
     * @var array
     */
    protected $_aIdList = null;

    /**
     * the number of records to show. if set to -1, then all records starting @iStartAtRecordNumber
     * will be shown (affects GoToEnd).
     *
     * @var int
     */
    protected $iNumberOfRecordsToShow = -1;

    /**
     * at what record to start (GoToStart will automatically move to this record).
     *
     * @var int
     */
    protected $iStartAtRecordNumber = 0;

    /**
     * the number of records found (ignores restriction iNumberOfRecordsToShow and iStartAtRecordNumber).
     *
     * @var int
     */
    protected $iNumberOfRecordsFound = -1;

    /**
     * set to true when the item has just been restored from session. if it has, make sure to run the
     * query again to ensure that you have a valid database pointer.
     *
     * @var bool
     */
    protected $bJustRestoredFromSession = false;

    /**
     * set to true if you want the list to keep a copy of any element it created (MEMORY!!).
     *
     * @var bool
     */
    public $bAllowItemCache = false;

    /**
     * stores the previous query executed by the list - we use this to prevent double execution of the query.
     *
     * @var string
     */
    private $sLastQueryRun = '';

    /**
     * limit the result set to this number of records (-1 = no restriction).
     *
     * @var int $iLimitResultSet
     */
    protected $iLimitResultSet = -1;

    /**
     * @var EntityList
     */
    private $entityList;

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var int|null
     */
    protected $estimationLowerLimit;

    /**
     * PDO style parameters
     * @var array
     */
    private $queryParameters;

    /**
     * PDO style parameters types
     * @var array
     */
    private $queryParameterTypes;

    /**
     * @return int
     */
    protected function getItemPointer()
    {
        return $this->getEntityList()->getCurrentPosition();
    }

    /**
     * @param int $itemPointer
     * @return void
     */
    protected function setItemPointer($itemPointer)
    {
        parent::setItemPointer($itemPointer);
        $this->getEntityList()->seek($itemPointer);
    }

    /**
     * @return int
     */
    protected function getEstimationLowerLimit()
    {
        if (null === $this->estimationLowerLimit) {
            $this->estimationLowerLimit = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.database.estimation_lower_limit');
        }

        return $this->estimationLowerLimit;
    }

    /**
     * @param int $lowerLimit
     * @return void
     */
    protected function setEstimationLowerLimit($lowerLimit)
    {
        $this->estimationLowerLimit = $lowerLimit;
    }

    /**
     * return a key identifying the list (ignores current paging info).
     *
     * @return string
     */
    public function GetIdentString()
    {
        $sIdentString = $this->sTableName.$this->sTableObject.$this->sQuery.$this->iLanguageId.$this->sTableName.$this->iNumberOfRecordsToShow;

        return md5($sIdentString);
    }

    public function __sleep()
    {
        if ($this->bAllowItemCache) {
            $aProperties = get_object_vars($this);
            // make sure to remove private vars since these can not be restored by unserialize
            unset($aProperties['aDynamicParameters']);
            unset($aProperties['sLastQueryRun']);
            unset($aProperties['_sqlpRecordPointer']);
            unset($aProperties['entityList']);
            unset($aProperties['databaseConnection']);
            unset($aProperties['queryParameters']);
            unset($aProperties['queryParameterTypes']);
            $aReturnValues = array_keys($aProperties);
            // the \0CLASSNAME\0PROPERTY is needed for private vars if the class is extended by some other class
            array_push($aProperties, "\0TCMSRecordList\0aDynamicParameters");
            array_push($aProperties, "\0TCMSRecordList\0sLastQueryRun");

            array_push($aReturnValues, "\0TCMSRecordList\0queryParameters");
            array_push($aReturnValues, "\0TCMSRecordList\0queryParameterTypes");

            //array_push($aProperties, "\0TCMSRecordList\0_items");

            return $aReturnValues;
        } else {
            $returnValues = array(
                'sTableObject',
                'iLanguageId',
                'sQuery',
                'sTableName',
                'iNumberOfRecordsToShow',
                'iStartAtRecordNumber',
                'iNumberOfRecordsFound',
                'bJustRestoredFromSession',
            );

            array_push($returnValues, "\0TCMSRecordList\0queryParameters");
            array_push($returnValues, "\0TCMSRecordList\0queryParameterTypes");

            return $returnValues;
        }
    }

    public function __wakeup()
    {
        $this->bJustRestoredFromSession = true;
    }

    /**
     * change the order by of the list.
     *
     * @param array<string, string> $aOrderInfo - must be of the form `table`.`field` => ASC/DESC or fieldalias=>ASC/DESC - fields MUST be quoted!
     * @psalm-param array<string, 'ASC'|'DESC'>
     */
    public function ChangeOrderBy($aOrderInfo)
    {
        $queryModifier = $this->getQueryModifierOrderByService();
        $this->sQuery = $queryModifier->getQueryWithOrderBy($this->sQuery, $aOrderInfo);
        $this->ResetList();
    }

    /**
     * @return QueryModifierOrderByInterface
     */
    protected function getQueryModifierOrderByService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.query_modifier.order_by');
    }

    /**
     * add a custom filter condition to the the beginning of the WHERE statement in the query.
     *
     * @param string $sFilterString
     *
     * @return void
     */
    public function AddFilterString($sFilterString = '')
    {
        $sQuery = $this->sQuery;
        if (!empty($sFilterString)) {
            $sQuery = str_replace("\r\n", ' ', $sQuery);
            $sQuery = str_replace("\n\r", ' ', $sQuery);
            $sQuery = str_replace("\n", ' ', $sQuery);
            $sQuery = str_replace("\r", ' ', $sQuery);

            $sQueryToParse = mb_strtoupper($sQuery, 'UTF-8');

            $bReplaced = false;
            $oParser = new SQLParenthesesParser();
            $aQueryParts = $oParser->getStringsNotInParentheses($sQueryToParse);
            $aQueryParts = array_reverse($aQueryParts, true);
            foreach ($aQueryParts as $iPosition => $sQueryPart) {
                $iPos = mb_strpos($sQueryPart, 'WHERE ');
                if (false !== $iPos) {
                    $iPos = $iPosition + $iPos + 5;
                    $sQuery = substr($sQuery, 0, $iPos).' ('.$sFilterString.') AND '.substr($sQuery, $iPos);
                    $bReplaced = true;
                    break;
                }
            }

            if (!$bReplaced) {
                reset($aQueryParts);
                foreach ($aQueryParts as $iPosition => $sQueryPart) {
                    $iPos = mb_strpos($sQueryPart, 'ORDER BY');
                    if (false !== $iPos) {
                        $iPos = $iPosition + $iPos;
                        $sQuery = substr($sQuery, 0, $iPos).' WHERE ('.$sFilterString.') '.substr(
                            $sQuery,
                            $iPos
                        );
                        $bReplaced = true;
                        break;
                    }
                }
            }

            if (!$bReplaced) {
                $sQuery = $this->sQuery.' WHERE ('.$sFilterString.')';
            }
        }
        $this->sQuery = $sQuery;
        $this->ResetList();
    }

    /**
     * reset the list (call this when the base query changes to ensure that the new query takes effekt).
     *
     * @return void
     */
    protected function ResetList()
    {
        $this->resetEntityList();

        $this->_items = array();
        $this->_aIdList = null;
        $this->sLastQueryRun = '';
        $this->iNumberOfRecordsFound = -1;
    }

    /**
     * create record - query is used to fetch the data, sTableName is the database
     * table name, and sTableObject is the CMSTable object name.
     *
     * @param class-string<T> $sTableObject
     * @param string $sTableName
     * @param string $sQuery
     * @param string $sLanguageId
     */
    public function __construct($sTableObject = 'TCMSRecord', $sTableName = null, $sQuery = null, $sLanguageId = null)
    {
        $this->sTableObject = $sTableObject;
        $this->sTableName = $sTableName;
        $this->SetLanguage($sLanguageId);
        $this->sQuery = $sQuery;
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSRecordList()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * set the language of the record to fetch. Make sure to call this function
     * before calling anything else!
     *
     * @param string $iLanguageId - id from table: cms_language
     * @return void
     */
    public function SetLanguage($iLanguageId)
    {
        $this->iLanguageId = $iLanguageId;
    }

    /**
     * returns number of records for current page (based on the page info).
     *
     * @return int
     */
    public function GetNumberOfRecordsForCurrentPage()
    {
        if ($this->iNumberOfRecordsToShow > 0) {
            $iRecs = $this->iNumberOfRecordsToShow;
            $iCurrentRecNumb = $this->iStartAtRecordNumber;
            if (($iCurrentRecNumb + $iRecs) > $this->Length()) {
                $iRecs = $this->Length() - $iCurrentRecNumb;
            }
        } elseif (0 == $this->iNumberOfRecordsToShow) {
            $iRecs = 0;
        } else {
            $iRecs = $this->Length();
        }

        return $iRecs;
    }

    /**
     * return true if the list has a next page.
     *
     * @return bool
     */
    public function HasNextPage()
    {
        $bHasNextPage = false;
        if ($this->iNumberOfRecordsToShow > 0) {
            $bHasNextPage = (($this->iStartAtRecordNumber + $this->iNumberOfRecordsToShow) < $this->Length());
        }

        return $bHasNextPage;
    }

    /**
     * return true if the list has a previous page.
     *
     * @return bool
     */
    public function HasPreviousPage()
    {
        $bHasPreviousPage = false;
        if ($this->iNumberOfRecordsToShow > 0) {
            $bHasPreviousPage = (($this->iStartAtRecordNumber - $this->iNumberOfRecordsToShow) >= 0);
        }

        return $bHasPreviousPage;
    }

    /**
     * moves the page pointer to the next page. return false if the pointer was not moved.
     *
     * @return bool
     */
    public function SetPagingInfoNextPage()
    {
        $bPointerMoved = false;
        if ($this->iNumberOfRecordsToShow > 0) {
            $iNewStartRecord = $this->iStartAtRecordNumber + $this->iNumberOfRecordsToShow;
            if ($iNewStartRecord < $this->Length()) {
                $bPointerMoved = $this->SetPagingInfo($iNewStartRecord, $this->iNumberOfRecordsToShow);
            }
        }

        return $bPointerMoved;
    }

    /**
     * moves the page pointer to the previous page. return false if the pointer was not moved.
     *
     * @return bool
     */
    public function SetPagingInfoPreviousPage()
    {
        $bPointerMoved = false;
        if ($this->iNumberOfRecordsToShow > 0) {
            $iNewStartRecord = $this->iStartAtRecordNumber - $this->iNumberOfRecordsToShow;
            if ($iNewStartRecord >= 0) {
                $bPointerMoved = $this->SetPagingInfo($iNewStartRecord, $this->iNumberOfRecordsToShow);
            }
        }

        return $bPointerMoved;
    }

    /**
     * move cursor to page $iPage - do not change cursor if the page does not exist.
     *
     * @param int $iPage
     *
     * @return bool
     */
    public function JumpToPage($iPage)
    {
        $bPointerMoved = false;
        if ($this->iNumberOfRecordsToShow > 0) {
            $iNewStartRecord = ($iPage * $this->GetPageSize()); //$this->iStartAtRecordNumber - $this->iNumberOfRecordsToShow;
            if ($iNewStartRecord <= $this->Length()) {
                $bPointerMoved = $this->SetPagingInfo($iNewStartRecord, $this->iNumberOfRecordsToShow);
            }
        }

        return $bPointerMoved;
    }

    /**
     * use this function before calling Load to show only iNumberOfRecords starting
     * at record number iStartRecord. set iNumberOfRecords to -1 if you want to show all
     * records starting at iStartRecord.
     *
     * returns false if the page request is invalid AND the list will show 0 records!
     *
     * @param int $iStartRecord     - at what record to start (first record = 0)
     * @param int $iNumberOfRecords - number of records to show (-1 = all)
     *
     * @return bool
     */
    public function SetPagingInfo($iStartRecord = 0, $iNumberOfRecords = -1)
    {
        $bPageInfoValid = true;
        if (($this->Length() <= $iStartRecord && $iStartRecord > 0 && $this->ExactLength() <= $iStartRecord) || $iStartRecord < 0) {
            $bPageInfoValid = false;
            $this->iStartAtRecordNumber = 0;
            $this->iNumberOfRecordsToShow = 0;
        } else {
            $this->iStartAtRecordNumber = $iStartRecord;
            $this->iNumberOfRecordsToShow = $iNumberOfRecords;
        }
        $this->getEntityList()->setPageSize($this->iNumberOfRecordsToShow);

        if (true === $bPageInfoValid) {
            $this->getEntityList()->setCurrentPage($this->iStartAtRecordNumber / $this->iNumberOfRecordsToShow);
        }

        return $bPageInfoValid;
    }

    /**
     * return start record number.
     *
     * @return int
     */
    public function GetStartRecordNumber()
    {
        return $this->iStartAtRecordNumber;
    }

    /**
     * return the page size.
     *
     * @return int
     */
    public function GetPageSize()
    {
        return $this->iNumberOfRecordsToShow;
    }

    /**
     * @param string $sQuery
     * @param array  $queryParameters     - PDO style parameters
     * @param array  $queryParameterTypes - PDO style parameter types
     *
     * @return void
     */
    public function Load($sQuery, array $queryParameters = array(), array $queryParameterTypes = array())
    {
        $this->sQuery = $sQuery;
        $this->queryParameters = $queryParameters;
        $this->queryParameterTypes = $queryParameterTypes;
        $this->ResetList();
    }

    /**
     * @return array
     */
    protected function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @return array
     */
    protected function getQueryParameterTypes()
    {
        return $this->queryParameterTypes;
    }

    /**
     * this method is not supported in classes of type TCMSRecordList
     * (works only in TIterator).
     *
     * @param string $propertyName
     * @param string $propertyValue
     *
     * @return void
     */
    public function RemoveItem($propertyName, $propertyValue)
    {
        trigger_error('Method RemoveItem not supported in classes of type TCMSRecordList', E_USER_ERROR);
    }

    /**
     * returns length of list.
     *
     * @return int
     */
    public function Length()
    {
        $lowerLimit = $this->getEstimationLowerLimit();
        $estimate = -1;
        if ($lowerLimit > 0) {
            $estimate = $this->getEntityList()->estimateCount();
        }
        $tooFewRecords = ($estimate < $lowerLimit);
        $estimateLessThanPageSize = $this->iNumberOfRecordsToShow > 0 && ($estimate < ($this->iNumberOfRecordsToShow + $this->iStartAtRecordNumber));
        if ($tooFewRecords || $estimateLessThanPageSize) {
            return $this->ExactLength();
        }

        return $estimate;
    }

    /**
     * @return int
     */
    public function ExactLength()
    {
        return $this->getEntityList()->count();
    }

    public function __clone()
    {
        // we need to regenerate the list to prevent use of the same mysql resource. there is no way to copy the resource.
        // if a cloned object contains the same, then MySqlLegacySupport::getInstance()->fetch_assoc(and other cursor operations) will change
        // the resource in the original and the clone - causing all kind of problems.
        $iLength = $this->iNumberOfRecordsFound;
        $this->Load($this->GetActiveQuery());
        $this->iNumberOfRecordsFound = $iLength; // but there is no need to recalculate length
    }

    /**
     * jumps to start of list.
     *
     * @return void
     */
    public function GoToStart()
    {
        $this->getEntityList()->rewind();
    }

    /**
     * jump to end of list.
     *
     * @return void
     */
    public function GoToEnd()
    {
        $this->getEntityList()->end();
    }

    /**
     * returns a random element from the list (list pointer stays as it is).
     *
     * @return T
     */
    public function Random()
    {
        $currentItemPointer = $this->getEntityList()->getCurrentPosition();
        $itemIndex = ($this->iNumberOfRecordsToShow > 0) ? $this->iNumberOfRecordsToShow : $this->Length();
        $this->getEntityList()->seek(rand(0, $itemIndex - 1));
        $item = $this->getItem($this->getEntityList()->key(), $this->getEntityList()->current());
        $this->getEntityList()->seek($currentItemPointer);

        return $item;
    }

    /**
     * Returns true if the last record fetched was the last record in the list (or on the current page if paging is used).
     *
     * Warning: method requires exact length if the result set is not paged - so may be slow
     *
     * @return bool
     */
    public function IsLast()
    {
        $pageSize = $this->GetPageSize();
        /**
         * The iteration implementation is currently a bit flawed. After we called Next() for the first time, the
         * item pointer points on the next item instead of the current one.
         */
        $itemPointer = $this->getItemPointer();
        if ($itemPointer > 0) {
            --$itemPointer;
        }
        if ($pageSize > 0) {
            return $itemPointer >= $this->getEntityList()->getNumberOfResultsOnPage() - 1;
        }

        return $itemPointer >= $this->ExactLength() - 1;
    }

    /**
     * returns the current item from the list without changing the pointer
     * if we are at the end of the record, then the function will return false (like after GoToLast)
     * if we are at the start of the record (like after GoToStart), then it will return the first element.
     *
     * @return T|false
     */
    public function current(): mixed
    {
        $data = $this->getEntityList()->current();
        if (false === $data) {
            return $data;
        }

        return $this->getItem($this->getEntityList()->key(), $data);
    }

    /**
     * returns the next element from the list, moving the pointer to the next
     * record.
     *
     * @return T|false
     */
    public function next(): mixed
    {
        $data = $this->getEntityList()->current();
        if (false === $data) {
            return $data;
        }
        $itemKey = $this->getEntityList()->key();
        $itemPosition = $this->getEntityList()->getCurrentPosition();

        $this->getEntityList()->next();

        return $this->getItem($itemKey, $data);
    }

    public function key(): int
    {
        return $this->getEntityList()->key();
    }

    public function valid(): bool
    {
        return $this->getEntityList()->valid();
    }

    public function rewind(): void
    {
        $this->getEntityList()->rewind();
    }

    /**
     * returns the previous record from the list, moving the pointer back one.
     *
     * @return T|false
     */
    public function Previous()
    {
        $this->getEntityList()->previous();
        $data = $this->getEntityList()->current();
        if (false === $data) {
            return $data;
        }

        return $this->getItem($this->getEntityList()->key(), $data);
    }

    /**
     * @return EntityList<T>
     */
    protected function getEntityList()
    {
        if (null === $this->entityList) {
            $this->entityList = new EntityList($this->getDatabaseConnection(), $this->sQuery, $this->getQueryParameters()??[], $this->getQueryParameterTypes()??[]);
        }

        return $this->entityList;
    }

    /**
     * @return void
     */
    protected function resetEntityList()
    {
        $this->getEntityList()->setQuery($this->sQuery);
    }

    /**
     * factory returning an element for the list.
     *
     * @param array $aData
     *
     * @return T
     */
    protected function _NewElement($aData)
    {
        $oElement = false;
        // try to fetch the element from _items first
        if (!is_null($this->sTableName)) {
            $oElement = new $this->sTableObject();
            $oElement->table = $this->sTableName;
        } else {
            $oElement = new $this->sTableObject();
        }
        /** @var $oElement T */
        $oElement->SetLanguage($this->iLanguageId);
        $oElement->LoadFromRow($aData);

        return $oElement;
    }

    /**
     * returns an array or mysql compatible string holding all the ids for the result set of the list.
     *
     * Returns
     * - An array of ids if ids exist and `$bReturnAsCommaSeparatedString=false` was specified
     * - An empty string if not ids exist and `$bReturnAsCommaSeparatedString=false` was specified
     * - A comma separated string of ids if `$bReturnAsCommaSeparatedString=true` was specified
     *
     * @param string $sFieldName                    - the name of the field from which we want the values
     * @param bool   $bReturnAsCommaSeparatedString - set this to true if you need the id list for a query e.g. WHERE `related_record_id` IN ('1','2','abcd-234')
     *
     * @return string[]|string - returns array or string (empty string if no records found)
     * @psalm-return ($bReturnAsCommaSeparatedString is true ? string : string[]|'')
     */
    public function GetIdList($sFieldName = 'id', $bReturnAsCommaSeparatedString = false)
    {
        $idList = array();
        $currentPosition = $this->getEntityList()->getCurrentPosition();
        $this->getEntityList()->rewind();
        foreach ($this->getEntityList() as $itemData) {
            if (false === isset($itemData[$sFieldName])) {
                continue;
            }
            if (true === $bReturnAsCommaSeparatedString) {
                $itemData[$sFieldName] = $this->getDatabaseConnection()->quote($itemData[$sFieldName]);
            }
            $idList[] = $itemData[$sFieldName];
        }
        $this->getEntityList()->seek($currentPosition);
        if ($bReturnAsCommaSeparatedString) {
            if (0 === count($idList)) {
                return '';
            }

            return implode(', ', $idList);
        } else {
            return $idList;
        }
    }

    /**
     * return the default language id.
     *
     * @return int
     */
    protected static function GetDefaultLanguageId()
    {
        return self::getMyLanguageService()->getActiveLanguageId();
    }

    /**
     * return true if the item is in the list.
     *
     * @param string $ItemId
     *
     * @return bool
     */
    public function IsInList($ItemId)
    {
        $currentPosition = $this->getEntityList()->getCurrentPosition();
        $this->getEntityList()->rewind();
        foreach ($this->getEntityList() as $ItemData) {
            if (isset($ItemData['id']) && $ItemData['id'] === $ItemId) {
                $this->getEntityList()->seek($currentPosition);

                return true;
            }
        }

        $this->getEntityList()->seek($currentPosition);

        return false;
    }

    /**
     * Returns the number of the page we're currently on.
     *
     * @return int Page number
     */
    public function GetCurrentPageNumber()
    {
        $iPageSize = $this->GetPageSize();

        // prevent this method from throwing a division by zero when PageSize is zero
        if ($iPageSize <= 0) {
            return 0;
        }
        $iCurrentPageNumber = ceil($this->iStartAtRecordNumber / $iPageSize) + 1;

        return $iCurrentPageNumber;
    }

    /**
     * Returns the total number of pages in this list.
     *
     * @return int Number of pages
     */
    public function GetTotalPageCount()
    {
        $iPageSize = $this->GetPageSize();

        // prevent this method from throwing a division by zero when PageSize is zero
        if (0 == $iPageSize) {
            return 0;
        }

        $iTotalPageCount = ceil($this->Length() / $iPageSize);

        return $iTotalPageCount;
    }

    /**
     * DO NOT USE THIS METHOD IN TCMSRecordList objects.
     */
    public function ShuffleList()
    {
        trigger_error('ERROR: unable to shuffle TCMSRecordList objects', E_USER_ERROR);
    }

    /**
     * return an array of unique values for the field including the count
     * IMPORTANT: respects paging info! make sure to unset the paging before calling method if you want to look at
     * all results, not just your current page.
     *
     * @param string $sFieldName
     * @param callable(string, string, array<string, string>): void  $aCallbackMethodToEvaluate - allows you to pass a callback array(object, method) to return the true value for the field
     *                                          the callback is passed the field name, the value and an array of all the row
     *
     * @return array
     */
    public function GetItemUniqueValueListForField($sFieldName = 'id', $aCallbackMethodToEvaluate = null)
    {
        // todo

        $aResult = array();
        $iPointer = $this->getEntityList()->getCurrentPosition();
        $this->getEntityList()->rewind();
        $entityList = clone $this->getEntityList();

        $isPagedResultSet = $this->iNumberOfRecordsToShow < $this->iNumberOfRecordsFound;
        if ($isPagedResultSet) {
            $entityList->setPageSize(-1);
        }

        $oCallbackObject = null;
        $sCallbackMethod = '';
        if (!is_null($aCallbackMethodToEvaluate)) {
            $oCallbackObject = $aCallbackMethodToEvaluate[0];
            $sCallbackMethod = $aCallbackMethodToEvaluate[1];
        }

        // note: for performance we move through the raw sql data instead of using real elements
        foreach ($entityList as $key => $entityData) {
            $sFieldVal = $entityData[$sFieldName];

            if (!is_null($oCallbackObject)) {
                $sFieldVal = $oCallbackObject->$sCallbackMethod($sFieldName, $sFieldVal, $entityData);
            }
            // the callback above may return an array (as may be the case when looking at an mlt field)
            // so we need to make sure that we handle this case properly
            if (is_array($sFieldVal)) {
                foreach ($sFieldVal as $sFieldValTmp) {
                    if (!array_key_exists($sFieldValTmp, $aResult)) {
                        $aResult[$sFieldValTmp] = 0;
                    }
                    ++$aResult[$sFieldValTmp];
                }
            } else {
                if (!array_key_exists($sFieldVal, $aResult)) {
                    $aResult[$sFieldVal] = 0;
                }
                ++$aResult[$sFieldVal];
            }
        }

        unset($entityList);

        return $aResult;
    }

    /**
     * returns the active list limit of the object.
     *
     * @return int
     */
    public function GetActiveListLimit()
    {
        return $this->iLimitResultSet;
    }

    /**
     * Set or change the active list limit.
     *
     * @param int $iNewListLimit
     * @return void
     */
    public function SetActiveListLimit($iNewListLimit)
    {
        $this->iLimitResultSet = $iNewListLimit;
        $iNewListLimit = ($iNewListLimit <= 0) ? $iNewListLimit = null : $iNewListLimit;

        $this->entityList->setMaxAllowedResults($iNewListLimit);
    }

    /**
     * add active limit to query.
     *
     * @param string $sOriginalQuery
     *
     * @return string
     */
    protected function AddActiveLimitToQuery($sOriginalQuery)
    {
        $sOriginalQuery = str_replace("\r\n", ' ', $sOriginalQuery);
        $sOriginalQuery = str_replace("\n\r", ' ', $sOriginalQuery);
        $sOriginalQuery = str_replace("\n", ' ', $sOriginalQuery);
        $sOriginalQuery = str_replace("\r", ' ', $sOriginalQuery);
        $sReturnQuery = $sOriginalQuery;
        $sQuery = mb_strtoupper($sOriginalQuery, 'UTF-8');
        $iStrPos = mb_strpos($sQuery, 'LIMIT ');
        if (false !== $iStrPos) {
            $sLimitString = substr($sQuery, $iStrPos + 6);
            if ($this->iLimitResultSet > 0) {
                $sReturnQuery = substr($sOriginalQuery, 0, $iStrPos).' LIMIT 0, '.$this->iLimitResultSet;
            } else {
                $sReturnQuery = substr($sOriginalQuery, 0, $iStrPos);
            }
        } else {
            if ($this->iLimitResultSet > -1) {
                $sReturnQuery = $sOriginalQuery.' LIMIT 0, '.$this->iLimitResultSet.'';
            }
        }

        return $sReturnQuery;
    }

    /**
     * return the current active query.
     *
     * @return string
     */
    public function GetActiveQuery()
    {
        return $this->sQuery;
    }

    /**
     * overwrite this method to add additional JOINS to backend list manager
     * queries.
     *
     * @param TCMSListManager $oListManager
     *
     * @return string
     */
    public function GetListManagerFilterQueryCustomJoins($oListManager)
    {
        return '';
    }

    /**
     * overwrite this method to add additional portal restrictions to
     * backend list manager queries.
     *
     * @param TCMSListManager $oListManager
     * @param string          $sQuery       - the query from TCMSListManager
     *
     * @return string
     */
    public function GetListManagerPortalRestriction($oListManager, $sQuery)
    {
        return $sQuery;
    }

    /**
     * overwrite this method to add an additional GROUP BY to backend list
     * manager queries.
     *
     * @param TCMSListManager $oListManager
     *
     * @return string
     */
    public function GetListManagerCustomGroupBy($oListManager)
    {
        return '';
    }

    /**
     * remove order by from source query.
     *
     * @param string $sSourceQuery
     *
     * @return string
     */
    protected function RemoveOrderByFromQuery($sSourceQuery)
    {
        $sql = str_replace("\n", ' ', $sSourceQuery);
        $sTmpSql = strtoupper($sql);
        // remove order by
        $iStrPos = strrpos($sTmpSql, ' ORDER BY ');
        if (false !== $iStrPos) {
            // check to make sure the query ends with order by...
            $iLength = strlen($sTmpSql);
            $iCountOpenBrackets = 0;
            for ($iPos = $iStrPos; $iPos < $iLength; ++$iPos) {
                if (')' === $sTmpSql[$iPos]) {
                    --$iCountOpenBrackets;
                } elseif ('(' === $sTmpSql[$iPos]) {
                    ++$iCountOpenBrackets;
                }
                if ($iCountOpenBrackets < 0) {
                    break;
                }
            }
            $iLimitPos = strpos($sTmpSql, ' LIMIT ', $iStrPos);
            if ($iCountOpenBrackets >= 0) {
                $sQuery = substr($sSourceQuery, 0, $iStrPos);
                if (false !== $iLimitPos) {
                    $sQuery .= substr($sSourceQuery, $iLimitPos);
                }
            } else {
                $sQuery = substr($sSourceQuery, 0, $iStrPos).substr($sSourceQuery, $iPos, $iLength - $iPos);
            }
        } else {
            $sQuery = $sSourceQuery;
        }

        return $sQuery;
    }

    private function getItem($itemKey, $data)
    {
        if (isset($this->_items[$itemKey])) {
            return $this->_items[$itemKey];
        }

        if (true === $this->bAllowItemCache) {
            $this->_items[$itemKey] = $this->_NewElement($data);

            return $this->_items[$itemKey];
        }

        return $this->_NewElement($data);
    }

    /**
     * @param Connection $connection
     *
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        if (null !== $this->databaseConnection) {
            return $this->databaseConnection;
        }

        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return \ChameleonSystem\CoreBundle\Util\FieldTranslationUtil
     */
    protected static function getFieldTranslationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * Returns the language service.
     * The strange name is to avoid naming conflicts with subclasses (PHP shows a strange behavior: Subclasses cannot
     * define a non-static method with the same name as a static method in the super-class. This is the case even when
     * the methods are private).
     *
     * @return LanguageServiceInterface
     */
    protected static function getMyLanguageService(): LanguageServiceInterface
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
