<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Interfaces;

/**
 * Provide URLs to media manager.
 */
interface MediaManagerUrlGeneratorInterface
{
    /**
     * Get URL to open the media manager.
     *
     * @return string
     */
    public function getStandaloneMediaManagerUrl();

    /**
     * Return true if media manager should be opened in new window.
     *
     * @return bool
     */
    public function openStandaloneMediaManagerInNewWindow();

    /**
     * Return URL to media manager for picking images.
     *
     * @param string $javaScriptCallbackFunctionName
     * @param bool $canUseCrop
     * @param string|null $imageFieldName - legacy
     * @param string|null $tableId - legacy
     * @param string|null $recordId - legacy
     * @param int $position - legacy
     *
     * @return string
     */
    public function getUrlToPickImage(
        $javaScriptCallbackFunctionName = 'parent._SetImage',
        $canUseCrop = false,
        $imageFieldName = null,
        $tableId = null,
        $recordId = null,
        $position = 0
    );

    /**
     * Return URL to media manager for picking images from a WYSIWYG instance.
     *
     * @param string $javaScriptCallbackFunctionName
     *
     * @return string
     */
    public function getUrlToPickImageForWysiwyg($javaScriptCallbackFunctionName = 'selectImage');
}
