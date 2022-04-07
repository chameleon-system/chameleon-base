<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

/**
 * RequestInfoServiceInterface defines a service that provides several pieces of information on the current request.
 * Try to use this interface only where you would use the request service (i.e. as far at the top of the call stack
 * as possible).
 */
interface RequestInfoServiceInterface
{
    /**
     * Returns the chameleon request type. This is always an integer that is defined as
     * a constant in ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface.
     *
     * @return int
     */
    public function getChameleonRequestType();

    /**
     * Compares a given chameleon request type to the currently active request type.
     *
     * @param int $requestType This must be an integer that is defined as a constant in
     * Returns the chameleon request type. This is always an integer that is defined as
     * a constant in ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface
     *
     * @return bool
     */
    public function isChameleonRequestType($requestType);

    /**
     * Returns true if the current request is a backend request AND the current page is the template engine.
     *
     * @return bool
     */
    public function isCmsTemplateEngineEditMode();

    /**
     * @return bool
     */
    public function isBackendMode();

    /**
     * Returns the request URI, without the portal and language prefixes.
     *
     * @return string
     */
    public function getPathInfoWithoutPortalAndLanguagePrefix();

    /**
     * Returns the a unique ID for every request.
     * It will be between 20 and 40 characters and only contain [a-zA-Z0-9-_].
     *
     * @return string
     */
    public function getRequestId(): string;
}
