<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

/**
 * the class is used to generate a tracking pixel (js/img) which logs
 * the view of one or more objects.
 */
class TPkgTrackObjectViews
{
    const VIEW_PATH = 'pkgTrackViews/views/';

    /**
     * @var array<string, array{ table_name: string, owner_id: string }>
     */
    protected $aObjects = array();

    /**
     * @var list<array{ table_name: string, owner_id: string }>
     */
    protected $aClickObjects = array();

    /**
     * return instance of Tracking object.
     *
     * @return TPkgTrackObjectViews
     **/
    public static function &GetInstance()
    {
        static $oInstance = null;
        if (is_null($oInstance)) {
            $oInstance = new self();
        }

        return $oInstance;
    }

    /**
     * track a view - note: only none-bot-requests will be tracked
     * @var TCMSRecord|mixed $oObject
     * @param bool $bCountReloads
     * @param bool $bAllowMultipleViewsPerPage
     *
     * @psalm-suppress UndefinedPropertyFetch
     * @FIXME Static access to non-static property. Should probably be $this->aObjects
    */
    public function TrackObject($oObject, $bCountReloads = true, $bAllowMultipleViewsPerPage = false)
    {
        if (false == TdbCmsConfig::RequestIsInBotList() && ($bCountReloads || false == self::WasViewedLast($oObject))) {
            $aTmpObject = array('table_name' => $oObject->table, 'owner_id' => $oObject->id);

            $sKey = $oObject->table.'-'.$oObject->id;
            if ($bAllowMultipleViewsPerPage) {
                $iCount = 0;
                $sBaseKey = $sKey;
                do {
                    $sKey = $sBaseKey.'-'.$iCount;
                    ++$iCount;
                } while (array_key_exists($sKey, self::$aObjects));
            }
            $this->aObjects[$sKey] = $aTmpObject;
            self::SetLastViewObjectHistory($oObject);
        }
    }

    /*
     * render tracking html
     * @return string
    */
    public function Render($sViewName = 'standard', $sViewType = 'Core')
    {
        $oView = new TViewParser();
        $oView->AddVar('sPayload', $this->GetPayloadOutgoing());

        return $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, $sViewType);
    }

    /*
    * Set last viewed object in session
    * @param TCMSRecord $oTableObject
    */
    protected static function SetLastViewObjectHistory($oTableObject)
    {
        $_SESSION['TPkgTrackObjectViews'] = array('tbl' => $oTableObject->table, 'id' => $oTableObject->id);
    }

    protected function GetPayloadOutgoing()
    {
        $sPayload = '';
        if (count($this->aObjects) > 0) {
            $aPayload = $this->GetPayloadBaseData('view');
            $aPayload['aObjects'] = $this->aObjects;
            $sPayload = gzcompress(json_encode($aPayload));
            $sPayload = base64_encode($sPayload);
        }

        return $sPayload;
    }

    protected function GetPayloadBaseData($sPayloadType = 'view')
    {
        $sUserId = '';
        $oUser = TdbDataExtranetUser::GetInstance();
        /** @var Request $request */
        $request = \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        if ($oUser) {
            $sUserId = $oUser->id;
        }
        $aPayload = [
            'sType' => $sPayloadType,
            'aObjects' => array(),
            'userId' => $sUserId,
            'ip' => $request->getClientIp(),
            'requestTime' => time(),
        ];

        return $aPayload;
    }

    protected function GetPayloadFromURL()
    {
        $oGlobal = TGlobal::instance();
        $aPayload = null;
        $sPayload = $oGlobal->GetUserData('pg');
        if (!empty($sPayload)) {
            $sChecksum = md5($sPayload);
            $sPayload = base64_decode($sPayload);
            $sPayload = gzuncompress($sPayload);
            $aPayload = json_decode($sPayload, true);
            $aPayload['request_checksum'] = $sChecksum;
        }

        return $aPayload;
    }

    /*
    * check if current TCMSRecord object ist equal to the last viewed TCMSRecord object
    * @param TCMSRecord $oTableObject
    * @return boolean
    */
    protected static function WasViewedLast($oTableObject)
    {
        $bViewHistorySet = (array_key_exists('TPkgTrackObjectViews', $_SESSION) && is_array($_SESSION['TPkgTrackObjectViews']) && array_key_exists('tbl', $_SESSION['TPkgTrackObjectViews']) && array_key_exists('id', $_SESSION['TPkgTrackObjectViews']));
        $bWasViewedLast = ($bViewHistorySet && $_SESSION['TPkgTrackObjectViews']['tbl'] == $oTableObject->table && $_SESSION['TPkgTrackObjectViews']['id'] == $oTableObject->id);

        return $bWasViewedLast;
    }

    /**
     * write the view to the database.
     */
    public function WriteView()
    {
        $aPayload = $this->GetPayloadFromURL();
        if (is_array($aPayload)) {
            $targetTable = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_track_views.target_table').'_history';
            $databaseConnection = $this->getDatabaseConnection();
            $quotedTargetTable = $databaseConnection->quoteIdentifier($targetTable);
            foreach ($aPayload['aObjects'] as $aData) {
                $aWrite = array(
                    'table_name' => $aData['table_name'],
                    'owner_id' => $aData['owner_id'],
                    'datecreated' => date('Y-m-d H:i:s', $aPayload['requestTime']),
                    'data_extranet_user_id' => $aPayload['userId'],
                    'session_id' => '',
                    'ip' => $aPayload['ip'],
                    'request_checksum' => $aPayload['request_checksum'],
                    'id' => TTools::GetUUID(),
                );
                $aKeys = array_map(array($databaseConnection, 'quoteIdentifier'), array_keys($aWrite));
                $aValues = array_map(array($databaseConnection, 'quote'), array_values($aWrite));
                $query = "INSERT INTO $quotedTargetTable (".implode(',', $aKeys).') VALUES ('.implode(',', $aValues).')';
                MySqlLegacySupport::getInstance()->query($query);
            }
        }
    }

    public function GetTrackClickClass($oObject)
    {
        $aPayload = array('table_name' => $oObject->table, 'owner_id' => $oObject->id);
        $this->aClickObjects[] = $aPayload;

        return 'cmstrack cmstrack-'.md5(serialize($aPayload));
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
