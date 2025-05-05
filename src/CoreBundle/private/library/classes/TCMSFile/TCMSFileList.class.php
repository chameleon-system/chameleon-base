<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFileList extends TIterator
{
    public $sDir;

    /**
     * get file list for a dir and a file pattern.
     *
     * @param string $sDir
     * @param string $sPattern - regex
     * @param bool $bUseRegexPattern - set to false if you want a standard file mask as used in glob
     *
     * @return TCMSFileList
     */
    public static function GetInstance($sDir, $sPattern = null, $bUseRegexPattern = true)
    {
        $oInst = new self();
        $oInst->Load($sDir, $sPattern, $bUseRegexPattern);

        return $oInst;
    }

    /**
     * load the files in the dir.
     *
     * @param string $sDir
     * @param bool $bUseRegexPattern - set to false if you want a standard file mask as used in glob
     * @param string $sPattern - regex
     */
    public function Load($sDir, $sPattern = null, $bUseRegexPattern = true)
    {
        $this->Destroy();
        $sDir = realpath($sDir);
        if ($bUseRegexPattern) {
            $d = dir($sDir);
            while (false !== ($entry = $d->read())) {
                if (is_file($sDir.'/'.$entry) && (is_null($sPattern) || $this->StringMatchesPattern($entry, $sPattern))) {
                    $oItem = TCMSFile::GetInstance($sDir.'/'.$entry);
                    $this->AddItem($oItem);
                }
            }
            $d->close();
        } else {
            $aFiles = glob($sDir.'/'.$sPattern, GLOB_ERR);
            if (!is_array($aFiles)) {
                TTools::WriteLogEntry('TCMSFileList: '.print_r($aFiles, true).' called Directory:'.$sDir, '1', __FILE__, __LINE__);
            } else {
                if (count($aFiles) > 0) {
                    foreach ($aFiles as $filename) {
                        $oItem = TCMSFile::GetInstance($filename);
                        $this->AddItem($oItem);
                    }
                }
            }
        }
    }

    /**
     * return true if the string matches the pattern.
     *
     * @param string $sString
     * @param string $sPattern - regex
     *
     * @return array|false
     */
    protected function StringMatchesPattern($sString, $sPattern)
    {
        $bMatch = false;
        $aMatches = [];
        $iMatches = preg_match($sPattern, $sString, $aMatches);
        if ($iMatches > 0) {
            return $aMatches;
        }

        return $bMatch;
    }

    /**
     * returns current item without moving the item pointer.
     *
     * @return TCMSFile
     */
    public function current(): TCMSFile|bool
    {
        return parent::Current();
    }

    /**
     * returns the current item in the list and advances the list pointer by 1.
     *
     * @return TCMSFile|false
     */
    public function next(): TCMSFile|bool
    {
        return parent::Next();
    }

    /**
     * returns the current item in the list and moves the list pointer back by 1.
     *
     * @return TCMSFile|false
     */
    public function Previous()
    {
        return parent::Previous();
    }

    /**
     * returns one random element from the list.
     *
     * @return TCMSFile|false
     */
    public function Random()
    {
        return parent::Random();
    }
}
