<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * module to upload video files to viddler.com
 * creates a local cms_media record.
 *
 * @deprecated since 6.2.0 - Viddler is no longer supported
 */
class CMSMediaViddlerImport extends TCMSModelBase
{
    /**
     * tree node id where the files will be added to.
     *
     * @var string - default null
     */
    protected $nodeID = null;

    /**
     * viddler.com API object.
     *
     * @var null|Viddler_V2
     */
    protected $oViddlerAPI = null;

    /**
     * @return array
     */
    public function &Execute()
    {
        parent::Execute();

        $this->nodeID = $this->global->GetUserData('nodeID');
        $this->data['nodeID'] = $this->nodeID;

        $this->data['sCallbackURL'] = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'CMSMediaViddlerImport', 'nodeID' => $this->nodeID, 'module_fnc[contentmodule]' => 'ImportVideoToLocalDB'));

        return $this->data;
    }

    /**
     * loads the API and performs a login.
     *
     * if login failes a TCMSMessageManager message is added 'VIDDLER_LOGIN' with consumer name 'viddlerLogin'
     *
     * @return null|Viddler_V2
     */
    protected function LoadViddlerAPI()
    {
        return null;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('ImportVideoToLocalDB');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * saves a temp record that is updated via cronjob when video file conversion is ready.
     */
    public function ImportVideoToLocalDB()
    {
        if (!$this->global->UserDataExists('videoid') || !$this->global->UserDataExists('nodeID')) {
            // error handling
        } else {
            $sViddlerVideoID = $this->global->GetUserData('videoid');
            $sNodeID = $this->global->GetUserData('nodeID');

            $oUser = TdbCmsUser::GetActiveUser();

            $aPostData = array();
            $aPostData['cms_media_tree_id'] = $sNodeID;
            $aPostData['external_video_id'] = $sViddlerVideoID;
            $aPostData['cms_user_id'] = $oUser->id;
            $aPostData['description'] = 'viddler '.$sViddlerVideoID;

            /** @var $oTableEditor TCMSTableEditorManager */
            $oTableEditor = TTools::GetTableEditorManager('cms_media', null);
            $oRecordData = $oTableEditor->Save($aPostData);

            /** @var $oMessageManager TCMSMessageManager */
            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            if (!$oMessageManager->ConsumerHasMessages($sConsumerName)) {
                // redirect to table editor
                $aParams = array();
                $aParams['tableid'] = $oTableEditor->oTableConf->id;
                $aParams['pagedef'] = 'tableeditorPopup';
                $aParams['id'] = $oRecordData->id;

                $this->controller->HeaderRedirect($aParams);
            } else {
                $this->data['errorMessage'] = $oMessageManager->RenderMessages($sConsumerName);
            }
        }
    }
}
