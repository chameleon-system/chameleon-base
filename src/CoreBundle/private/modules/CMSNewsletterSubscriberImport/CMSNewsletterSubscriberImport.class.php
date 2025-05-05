<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;

class CMSNewsletterSubscriberImport extends TCMSModelBase
{
    public const MESSAGE_MANAGER_CONSUMER = 'CMSNewsletterSubscriberImport';

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();
        $this->CheckRights();

        return $this->data;
    }

    /**
     * loads list of available newsletter groups for current CMS user.
     */
    protected function CheckRights()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $aPortals = $securityHelper->getUser()?->getPortals();
        if (null !== $aPortals) {
            $aPortals = array_keys($aPortals);
        }

        $databaseConnection = $this->getDatabaseConnection();
        $idListString = implode(',', array_map([$databaseConnection, 'quote'], $aPortals));

        $query = "SELECT `id`,`name` FROM `pkg_newsletter_group` WHERE `cms_portal_id` IN ($idListString) OR cms_portal_id = '' ORDER BY `name` ASC";
        $oPkgNewsletterGroupList = TdbPkgNewsletterGroupList::GetList($query);
        $this->data['oPkgNewsletterGroupList'] = $oPkgNewsletterGroupList;

        $oMessageManager = $this->getMessageManager();
        $sConsumerName = self::MESSAGE_MANAGER_CONSUMER.'-Step1';
        if (0 == $oPkgNewsletterGroupList->Length()) {
            $sMessageCode = 'ERROR_NO_GROUP_FOUND_FOR_IMPORT';
            $oMessageManager->addMessage($sConsumerName, $sMessageCode);
        }

        $this->data['messages'] = $oMessageManager->renderMessages($sConsumerName, 'standard', 'Core');
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['ParseFile'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * parses the CSV file and imports or updates subscriber.
     */
    public function ParseFile()
    {
        if (!empty($_FILES['csvfile']['tmp_name'])) {
            $bReplaceAllSubscriber = $this->global->GetUserData('replaceAllSubscriber');
            $pkg_newsletter_group_id = $this->global->GetUserData('pkg_newsletter_group_id');
            $bDoNotUpdateDataForExistingUsers = $this->global->GetUserData('notupdateSubscriber');
            if ('noGroup' == $pkg_newsletter_group_id) {
                $sPortalId = $this->global->GetUserData('cms_portal_id');
            } else {
                $oPkgNewsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
                if ($oPkgNewsletterGroup->Load($pkg_newsletter_group_id)) {
                    $sPortalId = $oPkgNewsletterGroup->fieldCmsPortalId;
                }
            }
            move_uploaded_file($_FILES['csvfile']['tmp_name'], PATH_CMS_CUSTOMER_DATA.'/'.$_FILES['csvfile']['name']);
            $csvFilePath = PATH_CMS_CUSTOMER_DATA.'/'.$_FILES['csvfile']['name'];

            // convert Windows Files to UTF-8
            $handle = fopen($csvFilePath, 'r');
            $contents = fread($handle, filesize($csvFilePath));
            $contents = iconv('CP1252', 'UTF-8', $contents);
            fclose($handle);

            $handle = fopen($csvFilePath, 'w');
            fwrite($handle, $contents);
            fclose($handle);

            $fileContent = file_get_contents($csvFilePath);

            // convert Windows CR to Linux
            $fileContent = str_replace("\r\n", "\n", $fileContent);

            // split file by finding right CR.
            $expr = "/\n(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
            $rows = preg_split($expr, trim($fileContent));
            unset($fileContent); // Free up some memory
            $aData = [];
            foreach ($rows as $row) {
                // find right semikolon
                $expr = '/;(?=(?:[^"]*"[^"]*")*(?![^"]*"))/';
                $results = preg_split($expr, trim($row));
                // remove quote
                $aData[] = preg_replace('/"(.*)"/s', '$1', $results);
            }
            unset($rows); // free up some memory

            if ('email' == $aData[0][0]) {
                unset($aData[0]);
            }

            $iTableID = TTools::GetCMSTableId('pkg_newsletter_user');
            $oTableEditor = new TCMSTableEditorManager();
            /* @var $oTableEditor TCMSTableEditorManager */
            if (isset($bReplaceAllSubscriber) && 'true' == $bReplaceAllSubscriber) {
                $this->DeleteSubscriber($pkg_newsletter_group_id);
            }

            $aListImported = [];
            $aListUpdated = [];
            $aListIgnored = [];
            $aListErrorImport = [];
            $aListErrorUpdate = [];

            foreach ($aData as $key => $aDataSet) {
                $errorOnCreatingNewsletterUser = false;
                $aInsertData = [];
                $aInsertData['from_import'] = '1';
                $aInsertData['cms_portal_id'] = $sPortalId;

                if (array_key_exists(0, $aDataSet) && !empty($aDataSet[0])) {
                    $aInsertData['email'] = $aDataSet[0];
                    if (TTools::IsValidEMail($aInsertData['email'])) {
                        if (array_key_exists(1, $aDataSet)) {
                            $aInsertData['lastname'] = $aDataSet[1];
                        }
                        if (array_key_exists(2, $aDataSet)) {
                            $aInsertData['firstname'] = $aDataSet[2];
                        }
                        if (array_key_exists(3, $aDataSet)) {
                            $aInsertData['data_extranet_salutation_id'] = $this->GetGenderID($aDataSet[3]);
                        }
                        $aInsertData['optin'] = 1;
                        $aInsertData['optincode'] = 'subscriberimport';

                        // check if email address already exists
                        $checkQuery = "SELECT *
              FROM `pkg_newsletter_user`
              WHERE `email` = '".MySqlLegacySupport::getInstance()->real_escape_string($aInsertData['email'])."'
              AND `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPortalId)."'";
                        $checkResult = MySqlLegacySupport::getInstance()->query($checkQuery);
                        $aNewsletterGroupList = [];
                        if (0 == MySqlLegacySupport::getInstance()->num_rows($checkResult)) { // email not yet in database so add it
                            // save new subscriber
                            $oTableEditor->Init($iTableID, null);

                            $aInsertData['signup_date'] = date('Y-m-d H:i:s');
                            if ('noGroup' != $pkg_newsletter_group_id) {
                                $aNewsletterGroupList = [$pkg_newsletter_group_id];
                            }
                            $newRecordData = $oTableEditor->Save($aInsertData);
                            if (false === $newRecordData) {
                                $aListErrorImport[] = $aInsertData['email'];
                                $errorOnCreatingNewsletterUser = true;
                            } else {
                                $aListImported[] = $aInsertData['email'];
                            }
                        } else { // email address found -> update subscriber info
                            $subscriberRow = MySqlLegacySupport::getInstance()->fetch_assoc($checkResult);
                            $oTableEditor->Init($iTableID, $subscriberRow['id']);
                            $aInsertData['id'] = $subscriberRow['id'];
                            $oNewsletterGroupList = $oTableEditor->oTableEditor->oTable->GetFieldPkgNewsletterGroupList();
                            if ('noGroup' != $pkg_newsletter_group_id) {
                                $aNewsletterGroupList = [$pkg_newsletter_group_id];
                            }
                            while ($oNewsletterGroup = $oNewsletterGroupList->Next()) {
                                if ($oNewsletterGroup->id != $pkg_newsletter_group_id) {
                                    $aNewsletterGroupList[] = $oNewsletterGroup->id;
                                }
                            }
                            if ('true' === $bDoNotUpdateDataForExistingUsers) {
                                $aListUpdated[] = $aInsertData['email'];
                            } else {
                                $newRecordData = $oTableEditor->Save($aInsertData);
                                if (false === $newRecordData) {
                                    $aListErrorUpdate[] = $aInsertData['email'];
                                } else {
                                    $aListUpdated[] = $aInsertData['email'];
                                }
                            }
                        }

                        if (false === $errorOnCreatingNewsletterUser && count($aNewsletterGroupList) > 0) {
                            $this->UpdateNewsletterGroups($oTableEditor, $aNewsletterGroupList, $aInsertData);
                        }
                    } else {
                        $aListIgnored[] = $aInsertData['email'];
                    }
                }
            }

            $this->data['aListImported'] = $aListImported;
            $this->data['aListUpdated'] = $aListUpdated;
            $this->data['aListIgnored'] = $aListIgnored;
            $this->data['aListErrorImport'] = $aListErrorImport;
            $this->data['aListErrorUpdate'] = $aListErrorUpdate;

            $this->SetTemplate('CMSNewsletterSubscriberImport', 'imported');

            unlink($csvFilePath);
        } else {
            $oMessageManager = TCMSMessageManager::GetInstance();
            /** @var $oMessageManager TCMSMessageManager */
            $sConsumerName = self::MESSAGE_MANAGER_CONSUMER.'-Step1';
            $sMessageCode = 'ERROR_NO_FILE_UPLOADED';
            $oMessageManager->AddMessage($sConsumerName, $sMessageCode);
        }
    }

    /**
     * Add the new newsletter group to the existing newsletter groups.
     *
     * @param array $aNewsletterGroupList
     * @param array $aInsertData was use for overwriting this function if you need to know imported for example email
     */
    protected function UpdateNewsletterGroups($oTableEditor, $aNewsletterGroupList, $aInsertData)
    {
        foreach ($aNewsletterGroupList as $sNewsletterId) {
            $oTableEditor->AddMLTConnection('pkg_newsletter_group_mlt', $sNewsletterId);
        }
    }

    /**
     * if you extended the newsletter subscriber table (pkg_newsletter_user)
     * you may need to extend this method to delete connected records (property table).
     */
    protected function DeleteSubscriber($iNewsletterGroupID)
    {
        if (!empty($iNewsletterGroupID)) {
            $query = "DELETE FROM `pkg_newsletter_user`, `pkg_newsletter_user_pkg_newsletter_group_mlt`
        USING `pkg_newsletter_user`
   INNER JOIN `pkg_newsletter_user_pkg_newsletter_group_mlt`
        WHERE `pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id` = `pkg_newsletter_user`.`id`
          AND `pkg_newsletter_user_pkg_newsletter_group_mlt`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iNewsletterGroupID)."'";
            MySqlLegacySupport::getInstance()->query($query);
        }
    }

    /**
     * returns the genderID matching some gender strings like w,f,female,mrs,frau.
     *
     * @param string $sGender
     *
     * @return string
     */
    protected function GetGenderID($sGender)
    {
        $sGender = strtolower($sGender);
        // Frau = 1
        // Herr = 2
        $genderID = '';
        $aGenderFemale = ['w', 'f', 'female', 'mrs', 'mrs.', 'frau'];
        $aGenderMale = ['m', 'male', 'mr', 'mr.', 'herr'];
        if (in_array($sGender, $aGenderFemale)) {
            $genderID = '1';
        } elseif (in_array($sGender, $aGenderMale)) {
            $genderID = '2';
        }

        return $genderID;
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private function getMessageManager(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }
}
