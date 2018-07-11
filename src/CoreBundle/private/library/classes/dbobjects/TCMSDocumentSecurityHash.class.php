<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSDocumentSecurityHash extends TCMSDocumentSecurityHashAutoParent
{
    /**
     * @return bool
     */
    public function isValidTimeSpan()
    {
        $bValid = false;
        $iPublishTime = strtotime($this->fieldPublishdate);
        $iEndTime = strtotime($this->fieldEnddate);

        $iCurrentTime = time();
        if ($iCurrentTime > $iPublishTime && $iCurrentTime < $iEndTime) {
            $bValid = true;
        }

        return $bValid;
    }

    /**
     * if no extranet user is given current user is loaded.
     *
     * @param TdbDataExtranetUser|null $oExtranetUser
     *
     * @return bool
     */
    public function isValidForExtranetUser($oExtranetUser = null)
    {
        $bValid = false;
        if (null === $oExtranetUser) {
            $oExtranetUser = TdbDataExtranetUser::GetInstance();
        }

        if ($oExtranetUser->id == $this->fieldDataExtranetUserId || '' === $this->fieldDataExtranetUserId) {
            $bValid = true;
        }

        return $bValid;
    }

    /**
     * calls isValidForExtranetUser and isValidTimeSpan
     * if no extranet user is given current user is loaded.
     *
     * @param TdbDataExtranetUser|null $oExtranetUser
     *
     * @return bool
     */
    public function isValid($oExtranetUser = null)
    {
        $bIsValid = $this->isValidTimeSpan();
        if ($bIsValid) {
            $bIsValid = $this->isValidForExtranetUser($oExtranetUser);
        }

        return $bIsValid;
    }
}
