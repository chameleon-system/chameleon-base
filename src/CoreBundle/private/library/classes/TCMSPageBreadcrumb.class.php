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
 * holds a breadcrumb to a page.
 *
 * @extends TIterator<TdbCmsTree|mixed>
 */
class TCMSPageBreadcrumb extends TIterator
{
    /**
     * set to true if the breadcrumb is the primary breadcrumb.
     *
     * @var bool
     */
    public $bIsPrimary = false;

    /**
     * @var string|null
     */
    protected $sPathString;

    /**
     * returns true if the node id is found in the breadcrumb.
     *
     * @param int $nodeID - id from the cms_tree table
     *
     * @return bool
     */
    public function NodeInBreadcrumb($nodeID)
    {
        $this->GoToStart();
        $found = false;
        while (!$found && ($oNode = $this->Next())) {
            if ($nodeID == $oNode->id) {
                $found = true;
            }
        }
        $this->GoToStart();

        return $found;
    }

    /**
     * returns path as string.
     *
     * @param string $sSeperator
     *
     * @return string
     */
    public function GetPathString($sSeperator = ' - ')
    {
        if (is_null($this->sPathString)) {
            $this->sPathString = '';
            $this->GoToStart();
            while ($oNode = $this->Next()) {
                /* @var $oNode TCMSTreeNode */
                $this->sPathString .= $sSeperator.$oNode->GetName();
            }
            $this->GoToStart();
        }

        return $this->sPathString;
    }
}
