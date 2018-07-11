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
 * @deprecated since 6.2.0 - use the ChameleonSystemSanityCheckBundle instead.
 */
class TPkgCmsServerSetupValidatorMessage implements IPkgCmsServerSetupValidatorMessage
{
    private $messageType = null;
    private $message;

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * @param int    $messageType - one of MESSAGE_TYPE_*
     * @param string $message
     */
    public function __construct($messageType, $message)
    {
        $this->setMessageType($messageType)->setMessage($message);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $messageType
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getMessageType()
    {
        return $this->messageType;
    }
}
