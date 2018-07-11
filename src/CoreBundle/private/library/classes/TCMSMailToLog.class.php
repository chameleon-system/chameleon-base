<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMailToLog extends TCMSMail
{
    private $verbose = false;

    /**
     * processes the email templates and starts sending the email.
     *
     * @param array $aData - assoc array of variables that will be replaced in templates
     *
     * @return bool
     */
    public function Send($aData = array())
    {
        $bNeedsToSend = $this->prepareMailData($aData);
        if ($bNeedsToSend) {
            $aTo = array();
            $aCc = array();
            $aBcc = array();
            foreach ($this->to as $key => $aRecipient) {
                $aTo[] = $aRecipient[1].'('.$aRecipient[0].')';
            }
            reset($this->to);

            foreach ($this->cc as $key => $aRecipient) {
                $aCc[] = $aRecipient[1].'('.$aRecipient[0].')';
            }
            reset($this->cc);

            foreach ($this->bcc as $key => $aRecipient) {
                $aBcc[] = $aRecipient[1].'('.$aRecipient[0].')';
            }
            reset($this->bcc);

            $to = join(', ', $aTo);
            $cc = join(', ', $aCc);
            $bcc = join(', ', $aBcc);
            $body = $this->Body;

            $sLog = '';
            if ($this->verbose) {
                $sLog = "New Mail:\n
                            to: ".$to."\n
                            cc: ".$cc."\n
                            bcc: ".$bcc."\n
                            \n
                            -------
                            Subject: ".$this->actualSubject."\n
                            -------
                            \n
                            ".$body."\n
                            ";
            } else {
                $sLog = 'New Mail: '.$this->actualSubject."\nTo: ".$to;
            }
            $this->oTools->WriteLogEntry($sLog, 4, __FILE__, __LINE__, 'dev_mail_log.log');
        }

        return true;
    }

    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }
}
