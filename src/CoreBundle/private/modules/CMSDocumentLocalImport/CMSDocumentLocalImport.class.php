<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSDocumentLocalImport extends CMSMediaLocalImport
{
    /**
     * the table where the files will be imported to.
     *
     * @var string
     */
    protected $sTargetTable = 'cms_document';

    /**
     * the table of the tree where the imported files will be attached to.
     *
     * @var string
     */
    protected $sTargetTreeTable = 'cms_document_tree';

    /**
     * called before any external functions gets called, but after the constructor.
     */
    public function Init()
    {
        parent::Init();
        $this->sImportFolder = PATH_DOCUMENT_LOCAL_IMPORT_FOLDER;
    }

    /**
     * returns an array of the record data which will be saved via TableEditor.
     *
     * @param string $sFile
     *
     * @return array
     */
    protected function GetFileRecordData($sFile)
    {
        $postData = parent::GetFileRecordData($sFile);
        unset($postData['metatags']);
        $postData['private'] = $this->global->GetUserData('private');

        return $postData;
    }
}
