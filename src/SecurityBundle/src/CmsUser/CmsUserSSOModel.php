<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

class CmsUserSSOModel
{
    public function __construct(
        private string $cmsUserId,
        private string $type,
        private string $ssoId,
        private ?string $id = null
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getCmsUserId(): string
    {
        return $this->cmsUserId;
    }

    public function setCmsUserId(string $cmsUserId): void
    {
        $this->cmsUserId = $cmsUserId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSsoId(): string
    {
        return $this->ssoId;
    }

    public function setSsoId(string $ssoId): void
    {
        $this->ssoId = $ssoId;
    }
}
