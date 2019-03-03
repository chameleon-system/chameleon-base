<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataModel;

class DownloadLink
{

    /**
     * @var null|string
     */
    private $id;

    /**
     * @var string
     */
    private $humanReadableFileSize = '';

    /**
     * Add internal attribute for wysiwyg editor integration and disable download url.
     *
     * @var bool
     */
    private $isBackendLink = false;

    /**
     * @var string
     */
    private $fileName = '';

    /**
     * @var bool
     */
    private $showSize = true;

    /**
     * @var string
     */
    private $iconCssClass = 'nofileicon';

    /**
     * @var string
     */
    private $linkStyle = '';

    /**
     * @var string
     */
    private $downloadUrl = '';

    /**
     * @var bool
     */
    private $showFilename = true;

    public function __construct(
        string $id,
        string $downloadUrl,
        string $fileName)
    {
        $this->id = $id;
        $this->downloadUrl = $downloadUrl;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHumanReadableFileSize(): string
    {
        return $this->humanReadableFileSize;
    }

    /**
     * @param string $humanReadableFileSize
     */
    public function setHumanReadableFileSize(string $humanReadableFileSize): void
    {
        $this->humanReadableFileSize = $humanReadableFileSize;
    }

    /**
     * @return bool
     */
    public function isBackendLink(): bool
    {
        return $this->isBackendLink;
    }

    /**
     * @param bool $isBackendLink
     */
    public function setIsBackendLink(bool $isBackendLink): void
    {
        $this->isBackendLink = $isBackendLink;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return bool
     */
    public function showSize(): bool
    {
        return $this->showSize;
    }

    /**
     * @param bool $showSize
     */
    public function setShowSize(bool $showSize): void
    {
        $this->showSize = $showSize;
    }

    /**
     * @return string
     */
    public function getIconCssClass(): string
    {
        return $this->iconCssClass;
    }

    /**
     * @param string $iconCssClass
     */
    public function setIconCssClass(string $iconCssClass): void
    {
        $this->iconCssClass = $iconCssClass;
    }

    /**
     * @return string
     */
    public function getLinkStyle(): string
    {
        return $this->linkStyle;
    }

    /**
     * @param string $linkStyle
     */
    public function setLinkStyle(string $linkStyle): void
    {
        $this->linkStyle = $linkStyle;
    }

    /**
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    /**
     * @param string $downloadUrl
     */
    public function setDownloadUrl(string $downloadUrl): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * @return bool
     */
    public function showFilename(): bool
    {
        return $this->showFilename;
    }

    /**
     * @param bool $showFilename
     */
    public function setShowFilename(bool $showFilename): void
    {
        $this->showFilename = $showFilename;
    }


}