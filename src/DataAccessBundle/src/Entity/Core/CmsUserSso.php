<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsUserSso
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsUser|null - Belongs to */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldVarchar
        /** @var string - SSO Service */
        private string $type = '',
        // TCMSFieldVarchar
        /** @var string - SSO ID */
        private string $ssoId = ''
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsUser(): ?CmsUser
    {
        return $this->cmsUser;
    }

    public function setCmsUser(?CmsUser $cmsUser): self
    {
        $this->cmsUser = $cmsUser;

        return $this;
    }

    // TCMSFieldVarchar
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSsoId(): string
    {
        return $this->ssoId;
    }

    public function setSsoId(string $ssoId): self
    {
        $this->ssoId = $ssoId;

        return $this;
    }
}
