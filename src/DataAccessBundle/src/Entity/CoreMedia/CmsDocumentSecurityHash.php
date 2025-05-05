<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMedia;

use ChameleonSystem\ExtranetBundle\Entity\DataExtranetUser;

class CmsDocumentSecurityHash
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldDocument
        /** @var CmsDocument|null - */
        private ?CmsDocument $cmsDocument = null,
        // TCMSFieldLookupParentID
        /** @var DataExtranetUser|null - */
        private ?DataExtranetUser $dataExtranetUser = null,
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - */
        private ?\DateTime $publishdate = new \DateTime(),
        // TCMSFieldDateTime
        /** @var \DateTime|null - */
        private ?\DateTime $enddate = null,
        // TCMSFieldUID
        /** @var string - */
        private string $token = ''
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

    // TCMSFieldDocument
    public function getCmsDocument(): ?CmsDocument
    {
        return $this->cmsDocument;
    }

    public function setCmsDocument(?CmsDocument $cmsDocument): self
    {
        $this->cmsDocument = $cmsDocument;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getPublishdate(): ?\DateTime
    {
        return $this->publishdate;
    }

    public function setPublishdate(?\DateTime $publishdate): self
    {
        $this->publishdate = $publishdate;

        return $this;
    }

    // TCMSFieldDateTime
    public function getEnddate(): ?\DateTime
    {
        return $this->enddate;
    }

    public function setEnddate(?\DateTime $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    // TCMSFieldUID
    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
