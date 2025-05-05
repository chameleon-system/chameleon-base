<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSWizardStepNewsletterSignup extends TdbCmsWizardStep
{
    public const INPUT_DATA_NAME = 'aNewsletter';
    public const NEWSLETTEROPTINSEND = 'sNewsletterOptInSend';
    /**
     * current newsletter signup form.
     *
     * @var TdbPkgNewsletterUser
     */
    protected $oNewsletterSignup;

    /**
     * lazzy load newsletter object.
     *
     * @return TdbPkgNewsletterUser
     */
    protected function LoadNewsletterSignup()
    {
        if (is_null($this->oNewsletterSignup)) {
            $this->oNewsletterSignup = TdbPkgNewsletterUser::GetNewInstance();
            $oNewslettter = TdbPkgNewsletterUser::GetInstanceForActiveUser();
            if (!is_null($oNewslettter)) {
                $this->oNewsletterSignup = $oNewslettter;
            }

            $oGlobal = TGlobal::instance();
            if ($oGlobal->UserDataExists(self::INPUT_DATA_NAME)) {
                $aData = $oGlobal->GetUserData(self::INPUT_DATA_NAME);
                if (is_array($aData)) {
                    $aData['id'] = $this->oNewsletterSignup->id;
                    $this->oNewsletterSignup->LoadFromRowProtected($aData);
                }
            }
        }

        return $this->oNewsletterSignup;
    }

    /**
     * called by the ExecuteStep Method - place any logic for the standard proccessing of this step here
     * return false if any errors occure (returns the user to the current step for corrections).
     *
     * @return bool
     */
    protected function ProcessStep()
    {
        $this->LoadNewsletterSignup();
        $bContinue = true;
        $oMsgManager = TCMSMessageManager::GetInstance();
        // validate data...
        $aRequiredFields = $this->oNewsletterSignup->GetRequiredFields();
        if (!is_array($this->oNewsletterSignup->sqlData)) {
            $this->oNewsletterSignup->sqlData = [];
        }
        foreach ($aRequiredFields as $sFieldName) {
            $sVal = '';
            if (array_key_exists($sFieldName, $this->oNewsletterSignup->sqlData)) {
                $sVal = trim($this->oNewsletterSignup->sqlData[$sFieldName]);
            }
            if (empty($sVal)) {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-'.$sFieldName, 'ERROR-USER-REQUIRED-FIELD-MISSING');
                $bContinue = false;
            } else {
                $this->oNewsletterSignup->sqlData[$sFieldName] = $sVal;
            }
        }

        // check format for email field
        if (!empty($this->oNewsletterSignup->fieldEmail) && !TTools::IsValidEMail($this->oNewsletterSignup->fieldEmail)) {
            $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-email', 'ERROR-E-MAIL-INVALID-INPUT');
            $bContinue = false;
        }

        if ($bContinue) {
            // check email
            if ($this->oNewsletterSignup->EMailAlreadyRegistered()) {
                // someone else has this email...
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-email', 'ERROR-E-MAIL-ALREADY-REGISTERED');
                $bContinue = false;
            }
        }

        if ($bContinue) {
            // save!
            $this->oNewsletterSignup->AllowEditByAll(true);
            $oMsgManager->AddMessage(self::NEWSLETTEROPTINSEND, 'SENDNEWSOPTIN');
            $this->oNewsletterSignup->Save();
        }

        return $bContinue;
    }

    /**
     * use this method to add any variables to the render method that you may
     * require for some view.
     *
     * @param string $sViewName - the view being requested
     * @param string $sViewType - the location of the view (Core, Custom-Core, Customer)
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        $aViewVariables = [];
        if (is_null($this->oNewsletterSignup)) {
            $this->oNewsletterSignup = TdbPkgNewsletterUser::GetNewInstance();
        }
        $aViewVariables['oNewsletterSignup'] = $this->oNewsletterSignup;

        return $aViewVariables;
    }
}
