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
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

class CMSNewsletterRobinsonImport extends TCMSModelBase
{
    public const MESSAGE_MANAGER_CONSUMER = 'CMSNewsletterRobinsonImport';

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();
        $this->GetPortals();

        $oMessageManager = TCMSMessageManager::GetInstance();
        /** @var $oMessageManager TCMSMessageManager */
        $sConsumerName = self::MESSAGE_MANAGER_CONSUMER.'-Step1';
        $this->data['messages'] = $oMessageManager->RenderMessages($sConsumerName);

        return $this->data;
    }

    /**
     * loads list of available newsletter groups for current CMS user.
     */
    protected function GetPortals()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $portalIds = $securityHelper->getUser()?->getPortals();

        if (null === $portalIds || 0 === count($portalIds)) {
            return;
        }
        $portalIdList = implode(', ', array_map(static fn (string $portalId) => $portalId, array_keys($portalIds)));

        $portals = TdbCmsPortalList::GetList(sprintf('SELECT * FROM `cms_portal` WHERE `id` IN (%s)', $portalIdList));

        $this->data['oPortals'] = $portals;
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
            $portalID = $this->global->GetUserData('cms_portal_id');

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

            $iTableID = TTools::GetCMSTableId('pkg_newsletter_robinson');
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorManager */
            $aListImported = [];
            $aListIgnored = [];

            foreach ($aData as $key => $aDataSet) {
                $aInsertData = [];
                $aInsertData['cms_portal_id'] = $portalID;

                if (array_key_exists(0, $aDataSet) && !empty($aDataSet[0])) {
                    $aInsertData['email'] = $aDataSet[0];
                    if (TTools::IsValidEMail($aInsertData['email'])) {
                        // check if email address already exists
                        $checkQuery = "SELECT * FROM `pkg_newsletter_robinson` WHERE `email` = '".MySqlLegacySupport::getInstance()->real_escape_string($aInsertData['email'])."' AND `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($portalID)."'";
                        $checkResult = MySqlLegacySupport::getInstance()->query($checkQuery);
                        if (0 == MySqlLegacySupport::getInstance()->num_rows($checkResult)) { // email not yet in database so add it
                            // save new subscriber
                            $oTableEditor->Init($iTableID, null);
                            $oTableEditor->Save($aInsertData);

                            $aListImported[] = $aInsertData['email'];
                        } else { // email address found -> ignore
                            $aListIgnored[] = $aInsertData['email'];
                        }
                    } else {
                        $aListIgnored[] = $aInsertData['email'];
                    }
                }
            }

            $this->data['aListImported'] = $aListImported;
            $this->data['aListIgnored'] = $aListIgnored;

            $this->SetTemplate('CMSNewsletterRobinsonImport', 'imported');

            unlink($csvFilePath);
        } else {
            $oMessageManager = TCMSMessageManager::GetInstance();
            /** @var $oMessageManager TCMSMessageManager */
            $sConsumerName = self::MESSAGE_MANAGER_CONSUMER.'-Step1';
            $sMessageCode = 'ERROR_NO_FILE_UPLOADED';
            $oMessageManager->AddMessage($sConsumerName, $sMessageCode);
        }
    }
}
