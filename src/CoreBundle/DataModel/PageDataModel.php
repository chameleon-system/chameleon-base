<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class PageDataModel
{
    private ?string $pageId;
    private ?string $name;
    private ?string $primarytreeNodeId;
    private ?string $relativeUrl;
    private ?string $absoluteUrl;
    private ?string $portalId;
    private bool $isActivePage = false;

    public function getPageId(): ?string
    {
        return $this->pageId;
    }

    public function setPageId(?string $pageId): void
    {
        $this->pageId = $pageId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPrimarytreeNodeId(): ?string
    {
        return $this->primarytreeNodeId;
    }

    public function setPrimarytreeNodeId(?string $primarytreeNodeId): void
    {
        $this->primarytreeNodeId = $primarytreeNodeId;
    }

    public function getRelativeUrl(): ?string
    {
        return $this->relativeUrl;
    }

    public function setRelativeUrl(?string $relativeUrl): void
    {
        $this->relativeUrl = $relativeUrl;
    }

    public function getAbsoluteUrl(): ?string
    {
        return $this->absoluteUrl;
    }

    public function setAbsoluteUrl(?string $absoluteUrl): void
    {
        $this->absoluteUrl = $absoluteUrl;
    }

    public function getPortalId(): ?string
    {
        return $this->portalId;
    }

    public function setPortalId(?string $portalId): void
    {
        $this->portalId = $portalId;
    }

    public function isActivePage(): bool
    {
        return $this->isActivePage;
    }

    public function setIsActivePage(bool $isActivePage): void
    {
        $this->isActivePage = $isActivePage;
    }
}
