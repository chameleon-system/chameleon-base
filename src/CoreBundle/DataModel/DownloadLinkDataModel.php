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

class DownloadLinkDataModel
{
    /**
     * @var string|null
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

    private ?string $fileType;

    public function __construct(
        string $id,
        string $downloadUrl,
        string $fileName)
    {
        $this->id = $id;
        $this->downloadUrl = $downloadUrl;
        $this->fileName = $fileName;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getHumanReadableFileSize(): string
    {
        return $this->humanReadableFileSize;
    }

    public function setHumanReadableFileSize(string $humanReadableFileSize): void
    {
        $this->humanReadableFileSize = $humanReadableFileSize;
    }

    public function isBackendLink(): bool
    {
        return $this->isBackendLink;
    }

    public function setIsBackendLink(bool $isBackendLink): void
    {
        $this->isBackendLink = $isBackendLink;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function showSize(): bool
    {
        return $this->showSize;
    }

    public function setShowSize(bool $showSize): void
    {
        $this->showSize = $showSize;
    }

    public function getIconCssClass(): string
    {
        return $this->iconCssClass;
    }

    public function setIconCssClass(string $iconCssClass): void
    {
        $this->iconCssClass = $iconCssClass;
    }

    public function getLinkStyle(): string
    {
        return $this->linkStyle;
    }

    public function setLinkStyle(string $linkStyle): void
    {
        $this->linkStyle = $linkStyle;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(string $downloadUrl): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    public function showFilename(): bool
    {
        return $this->showFilename;
    }

    public function setShowFilename(bool $showFilename): void
    {
        $this->showFilename = $showFilename;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }
}
