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
     */
    public function getChameleonRequestType(): int;

    /**
     * Compares a given chameleon request type to the currently active request type.
     *
     * @param int $requestType This must be an integer that is defined as a constant in
     *                         Returns the chameleon request type. This is always an integer that is defined as
     *                         a constant in ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface
     */
    public function isChameleonRequestType(int $requestType): bool;

    /**
     * Returns true if the user is accessing a frontend page in template mode.
     * This is used when editing spots in the backend.
     */
    public function isCmsTemplateEngineEditMode(): bool;

    /**
     * Returns true if the user is accessing a frontend page in preview mode.
     * This is used when a page is previewed from the backend.
     */
    public function isPreviewMode(): bool;

    public function isFrontendJsDisabled(): bool;

    public function isBackendMode(): bool;

    /**
     * Returns the request URI, without the portal and language prefixes.
     */
    public function getPathInfoWithoutPortalAndLanguagePrefix(): string;

    /**
     * Returns a unique ID for every request.
     * It will be between 20 and 40 characters and only contain [a-zA-Z0-9-_].
     */
    public function getRequestId(): string;

    /**
     * @param $requestType int one of \ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface::REQUEST_TYPE_*
     */
    public function setChameleonRequestType(int $requestType): void;
}
