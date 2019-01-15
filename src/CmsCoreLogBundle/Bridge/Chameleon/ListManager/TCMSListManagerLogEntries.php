<?php

namespace ChameleonSystem\CmsCoreLogBundle\Bridge\Chameleon\ListManager;

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use TCMSListManagerFullGroupTable;
use ViewRenderer;

class TCMSListManagerLogEntries extends TCMSListManagerFullGroupTable
{
    const ALL_SELECTION_VALUE = '0';
    const ALL_ERROR_SELECTION_VALUE = '1';

    private static $levels = [
        'DEBUG' => Logger::DEBUG,
        'INFO' => Logger::INFO,
        'NOTICE' => Logger::NOTICE,
        'WARNING' => Logger::WARNING,
        'ERROR' => Logger::ERROR,
        'CRITICAL' => Logger::CRITICAL,
        'ALERT' => Logger::ALERT,
        'EMERGENCY' => Logger::EMERGENCY,
    ];

    /**
     * {@inheritdoc}
     */
    protected function PostCreateTableObjectHook()
    {
        $filterLogLevel = $this->getFilterInputUtil()->getFilteredInput('filterLogLevel', self::ALL_SELECTION_VALUE);

        $searchFilter = sprintf('<div class="form-group">%s</div>', $this->getFilterLogLevelSelect($filterLogLevel));

        $this->tableObj->searchBoxContent = $searchFilter;
        $this->tableObj->aHiddenFieldIgnoreList = ['filterLogLevel'];
    }

    /**
     * @param string $filterLogLevel
     *
     * @return string
     */
    private function getFilterLogLevelSelect($filterLogLevel)
    {
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sInputClass', 'form-control form-control-sm');
        $oViewRenderer->AddSourceObject('sName', 'filterLogLevel');
        $oViewRenderer->AddSourceObject('sLabelText', $this->getTranslation('pkg_cms_core_log.log_table.field_level'));

        $aValueList = array();
        $aValueList[] = array('sName' => $this->getTranslation('pkg_cms_core_log.log_table.select_level_all'), 'sValue' => self::ALL_SELECTION_VALUE);
        $aValueList[] = array('sName' => $this->getTranslation('pkg_cms_core_log.log_table.select_level_all_errors'), 'sValue' => self::ALL_ERROR_SELECTION_VALUE);

        $onlyLevelPrefix = $this->getTranslation('pkg_cms_core_log.log_table.select_level_only');
        foreach (self::$levels as $name => $level) {
            $aValueList[] = array('sName' => sprintf('%s: %s %s', $onlyLevelPrefix, $level, $name), 'sValue' => $level);
        }

        $oViewRenderer->AddSourceObject('aValueList', $aValueList);
        $oViewRenderer->AddSourceObject('sValue', $filterLogLevel);

        return $oViewRenderer->Render('userInput/form/select.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function GetCustomRestriction()
    {
        $query = parent::GetCustomRestriction();

        if (!isset($this->tableObj->_postData['filterLogLevel']) || self::ALL_SELECTION_VALUE === $this->tableObj->_postData['filterLogLevel']) {
            return $query;
        }

        $logLevelSelection = $this->tableObj->_postData['filterLogLevel'];

        if (self::ALL_ERROR_SELECTION_VALUE === $logLevelSelection) {
            $query .= sprintf(' `pkg_cms_core_log`.`level` >= %s ', Logger::WARNING);
        } else {
            $quotedLogLevelSelection = $this->getDBConnection()->quote($logLevelSelection);
            $query .= sprintf(' `pkg_cms_core_log`.`level` = %s ', $quotedLogLevelSelection);
        }

        return $query;
    }

    /**
     * @param string $id
     * @param array  $data
     *
     * @return string
     */
    private function getTranslation($id, $data = [])
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans($id, $data, TranslationConstants::DOMAIN_BACKEND);
    }

    /**
     * @return Connection
     */
    private function getDBConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getFilterInputUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
