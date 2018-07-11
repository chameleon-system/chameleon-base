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
interface IPkgCmsServerSetupValidatorMessage
{
    const MESSAGE_TYPE_VALID = 1;
    const MESSAGE_TYPE_NOTICE = 2;
    const MESSAGE_TYPE_WARNING = 3;
    const MESSAGE_TYPE_ERROR = 4;

    /**
     * @return int (one of MESSAGE_TYPE_*)
     */
    public function getMessageType();

    /**
     * @param int $messageType - one of MESSAGE_TYPE_
     *
     * @return $this
     */
    public function setMessageType($messageType);

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param int    $messageType - one of MESSAGE_TYPE_*
     * @param string $message
     */
    public function __construct($messageType, $message);
}
