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
 * @deprecated since 6.3.0 - not used anymore
 */
class TCMSMediaTreeNode extends TCMSTreeNode
{
    public function TCMSMediaTreeNode($id = null)
    {
        $table = 'cms_media_tree';
        parent::TCMSTreeNode($id);
        $this->table = $table;
    }

    /**
     * returns the media files linked to the node.
     *
     * @return array - _TCMSMediaTreeNodeMediaObj
     */
    public function GetLinkedMedia($startFrom = 0, $limit = null)
    {
        if (is_null($limit)) {
            $limit = 20;
        }

        if (!is_null($this->id)) {
            $query = "SELECT F.*
                      FROM `cms_media` AS F
                     WHERE
                          F.`cms_media_tree_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                     ORDER BY `description`
                     LIMIT ".MySqlLegacySupport::getInstance()->real_escape_string($startFrom).','.MySqlLegacySupport::getInstance()->real_escape_string($limit).'
                     ';

            $oFileList = new TCMSRecordList('TCMSRecord', 'cms_media', $query);

            $aMediaList = array();
            while ($oFile = $oFileList->Next()) {
                /** @var $oFile TCMSRecord */
                $oImage = new TCMSImage();
                /** @var $oImage TCMSImage */
                $oImage->Load($oFile->sqlData['id']);
                $oThumb = $oImage->GetThumbnail(100, 100);
                /** @var $oThumb TCMSImage */
                $dimensions = $oFile->sqlData['width'].' x '.$oFile->sqlData['height'];
                $fileSize = $oImage->GetImageSize();

                $mediaObj = new _TCMSMediaTreeNodeMediaObj();
                $mediaObj->oFile = $oFile;
                $mediaObj->oimage = $oImage;
                $mediaObj->oThumb = $oThumb;
                $mediaObj->dimensions = $dimensions;
                $mediaObj->fileSize = $fileSize;
                $mediaObj->iconURL = '';

                $aMediaList[] = $mediaObj;
            }

            return $aMediaList;
        } else {
            return array();
        }
    }

    /**
     * returns a html list (ul/li) of a media tree structure.
     *
     * @param array $aStopNodes
     *
     * @return string
     */
    public function GetBreadCrumb($aStopNodes = null)
    {
        if (!is_array($aStopNodes)) {
            $aStopNodes = array($aStopNodes);
        }
        $aPath = array();
        $currentNode = $this;
        $returnVal = "<ul>\n";
        $aPath[] = '<li>'.$currentNode->sqlData['name']."</li>\n";
        while (!empty($currentNode->sqlData['parent_id']) && !in_array($currentNode->sqlData['id'], $aStopNodes)) {
            $oParentNode = new self();
            /** @var $oParent TCMSTreeNode */
            $oParentNode->Load($currentNode->sqlData['parent_id']);
            $currentNode = $oParentNode;
            $aPath[] = '<li>'.$currentNode->sqlData['name']."</li>\n";
        }
        $aPath = array_reverse($aPath);
        foreach ($aPath as $val) {
            $returnVal .= $val;
        }

        $returnVal .= "</ul>\n";

        return $returnVal;
    }
}

class _TCMSMediaTreeNodeMediaObj
{
    public $oFile = null;
    public $oimage = null;
    public $oThumb = null;
    public $dimensions = null;
    public $fileSize = null;
    public $iconURL = null;
}
