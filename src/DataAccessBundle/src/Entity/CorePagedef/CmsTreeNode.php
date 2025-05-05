<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CorePagedef;

class CmsTreeNode
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldBoolean
        /** @var bool - Create link */
        private bool $active = false,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Activate connection from */
        private ?\DateTime $startDate = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Deactivate connection after */
        private ?\DateTime $endDate = null,
        // TCMSFieldVarchar
        /** @var string - Table of linked record */
        private string $tbl = '',
        // TCMSFieldExtendedLookup
        /** @var CmsTplPage|null - ID of linked record */
        private ?CmsTplPage $contid = null,
        // TCMSFieldLookupParentID
        /** @var CmsTree|null - Navigation item */
        private ?CmsTree $cmsTree = null
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

    // TCMSFieldBoolean
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    // TCMSFieldDateTime
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    // TCMSFieldDateTime
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTbl(): string
    {
        return $this->tbl;
    }

    public function setTbl(string $tbl): self
    {
        $this->tbl = $tbl;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getContid(): ?CmsTplPage
    {
        return $this->contid;
    }

    public function setContid(?CmsTplPage $contid): self
    {
        $this->contid = $contid;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsTree(): ?CmsTree
    {
        return $this->cmsTree;
    }

    public function setCmsTree(?CmsTree $cmsTree): self
    {
        $this->cmsTree = $cmsTree;

        return $this;
    }
}
