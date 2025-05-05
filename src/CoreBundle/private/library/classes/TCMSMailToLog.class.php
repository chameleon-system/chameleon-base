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
use Psr\Log\LoggerInterface;

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
    public function Send($aData = [])
    {
        $bNeedsToSend = $this->prepareMailData($aData);
        if ($bNeedsToSend) {
            $aTo = [];
            $aCc = [];
            $aBcc = [];
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

            $logger = $this->getLogger();
            $logger->info($sLog);
        }

        return true;
    }

    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
