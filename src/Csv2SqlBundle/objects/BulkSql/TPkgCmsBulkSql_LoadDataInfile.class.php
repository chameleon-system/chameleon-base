<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsBulkSql_LoadDataInfile implements IPkgCmsBulkSql
{
    /**
     * @var string|null
     */
    private $sTableName;
    /**
     * @var string[]
     */
    private $aFields = [];
    /**
     * @var string|null
     */
    private $sFileName;
    /**
     * @var resource|closed-resource|false|null
     */
    private $rFile;

    /**
     * @var mysqli|null
     */
    private $dbConnection;
    /**
     * @var string|null
     */
    private $dbHost;
    /**
     * @var string|null
     */
    private $dbName;
    /**
     * @var string|null
     */
    private $dbPassword;
    /**
     * @var string|null
     */
    private $dbUser;

    /**
     * @param string|null $dbHost
     * @param string|null $dbUser
     * @param string|null $dbPassword
     * @param string|null $dbName
     */
    public function __construct($dbHost = null, $dbUser = null, $dbPassword = null, $dbName = null)
    {
        $this->dbHost = $dbHost;
        if (null === $this->dbHost) {
            $this->dbHost = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('database_host');
        }
        $this->dbName = $dbName;
        if (null === $this->dbName) {
            $this->dbName = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('database_name');
        }
        $this->dbUser = $dbUser;
        if (null === $this->dbUser) {
            $this->dbUser = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('database_user');
        }
        $this->dbPassword = $dbPassword;
        if (null === $this->dbPassword) {
            $this->dbPassword = ChameleonSystem\CoreBundle\ServiceLocator::getParameter('database_password');
        }
    }

    public function __destruct()
    {
        if (null !== $this->dbConnection) {
            $this->dbConnection->close();
        }
    }

    /**
     * returns true if the init was ok - else false.
     *
     * @param string $sTable
     * @param string[] $aFields
     *
     * @return bool
     */
    public function Initialize($sTable, $aFields)
    {
        $this->sTableName = $sTable;
        $this->aFields = $aFields;
        $this->sFileName = CMS_TMP_DIR.'/cms_bulk_'.$this->sTableName;
        $this->rFile = fopen($this->sFileName, 'wb');
        if (false !== $this->rFile) {
            fclose($this->rFile);
            $this->rFile = fopen($this->sFileName, 'wb');
        }

        return false !== $this->rFile;
    }

    /**
     * return true if the data was writ ten to the target file.
     *
     * @param string[] $aData
     *
     * @return bool
     */
    public function AddRow($aData)
    {
        if (false === $this->rFile) {
            return false;
        }
        $aDataEscaped = $this->EscapeData($aData);
        $sRow = "'".implode("'\t'", $aDataEscaped)."'\n";
        $rReturn = fwrite($this->rFile, $sRow);

        return false !== $rReturn;
    }

    public function CommitData()
    {
        fclose($this->rFile);

        $databaseConnection = $this->getDbConnection();
        $quotedTableName = $databaseConnection->real_escape_string($this->sTableName);
        $quotedFields = array_map([$databaseConnection, 'real_escape_string'], $this->aFields);
        $query = "LOAD DATA LOCAL INFILE '{$this->sFileName}'
                         REPLACE
                      INTO TABLE `$quotedTableName`
                   CHARACTER SET 'utf8'
                          FIELDS TERMINATED BY '\\t' ENCLOSED BY '\\''
                           LINES TERMINATED BY '\\n' STARTING BY ''
                                 (`".implode('`,`', $quotedFields).'`)
               ';
        $databaseConnection->query($query);
        $sError = $databaseConnection->error;
        if (empty($sError)) {
            unlink($this->sFileName);

            return true;
        } else {
            trigger_error('MySQL error: "'.$sError.'" in query '.$query.' - switching to backup import via shell call', E_USER_WARNING);
            $this->CommitDataViaShell($query);

            return false;
        }
    }

    /**
     * in some cases (probably due to php bug #55737) php is not able to perform a LOAD DATA
     * LOCAL INFILE using a non-root mysql user. In this case we'll fall back to an import
     * call via shell. Be sure the user is allowed to perform a LOAD DATA LOCAL INFILE command.
     *
     * @param string $sQuery
     *
     * @return int|false - 0 denotes succes; everything else an error
     */
    protected function CommitDataViaShell($sQuery)
    {
        $iRetVal = false;
        $sFileName = CMS_TMP_DIR.'/import_cms_bulk_'.$this->sTableName.'.sql';
        if ($pFile = fopen($sFileName, 'w')) {
            fwrite($pFile, $sQuery);
            fclose($pFile);
            $sCommand = 'cat "'.$sFileName.'" | '.
                'mysql -u '.$this->dbUser.
                ' -h '.$this->dbHost.
                ' -p'.$this->dbPassword.
                ' '.$this->dbName.
                ' --local-infile';
            $aOutput = [];
            exec($sCommand, $aOutput, $iRetVal);
            if (0 != $iRetVal) {
                trigger_error('Import via shell reported an error: '.implode("\n", $aOutput), E_USER_WARNING);
            }
        } else {
            trigger_error("Can't open file for writing: ".$sFileName);
            $iRetVal = false;
        }

        return $iRetVal;
    }

    /**
     * @param string[] $aData
     *
     * @return string[]
     */
    protected function EscapeData($aData)
    {
        foreach (array_keys($aData) as $sKey) {
            $aData[$sKey] = $this->getDbConnection()->real_escape_string($aData[$sKey]);
        }

        return $aData;
    }

    public function setDbConnection(mysqli $dbConnection): void
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @return mysqli
     */
    protected function getDbConnection()
    {
        if (null !== $this->dbConnection) {
            return $this->dbConnection;
        }
        $this->dbConnection = new mysqli(
            $this->dbHost,
            $this->dbUser,
            $this->dbPassword,
            $this->dbName
        );
        if ($this->dbConnection->connect_errno) {
            throw new ErrorException('unable to open bulk import db connection', 0, E_USER_ERROR, __FILE__, __LINE__);
        }
        $this->dbConnection->options(MYSQLI_OPT_LOCAL_INFILE, 1);
        $this->dbConnection->set_charset('utf8');

        return $this->dbConnection;
    }
}
