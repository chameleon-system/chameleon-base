<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsChangelogSet
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - Change date */
        private ?\DateTime $modifyDate = new \DateTime(),
        // TCMSFieldCMSUser
        /** @var CmsUser|null - User who made the change */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldExtendedLookup
        /** @var CmsTblConf|null - The main table that was changed */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldVarchar
        /** @var string - ID of the changed data record */
        private string $modifiedId = '',
        // TCMSFieldVarchar
        /** @var string - Name of the changed data record */
        private string $modifiedName = '',
        // TCMSFieldVarchar
        /** @var string - Type of change (INSERT, UPDATE, DELETE) */
        private string $changeType = '',
        // TCMSFieldPropertyTable
        /** @var Collection<int, PkgCmsChangelogItem> - Changes */
        private Collection $pkgCmsChangelogItemCollection = new ArrayCollection()
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

    // TCMSFieldDateTimeNow
    public function getModifyDate(): ?\DateTime
    {
        return $this->modifyDate;
    }

    public function setModifyDate(?\DateTime $modifyDate): self
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    // TCMSFieldCMSUser
    public function getCmsUser(): ?CmsUser
    {
        return $this->cmsUser;
    }

    public function setCmsUser(?CmsUser $cmsUser): self
    {
        $this->cmsUser = $cmsUser;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

        return $this;
    }

    // TCMSFieldVarchar
    public function getModifiedId(): string
    {
        return $this->modifiedId;
    }

    public function setModifiedId(string $modifiedId): self
    {
        $this->modifiedId = $modifiedId;

        return $this;
    }

    // TCMSFieldVarchar
    public function getModifiedName(): string
    {
        return $this->modifiedName;
    }

    public function setModifiedName(string $modifiedName): self
    {
        $this->modifiedName = $modifiedName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getChangeType(): string
    {
        return $this->changeType;
    }

    public function setChangeType(string $changeType): self
    {
        $this->changeType = $changeType;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, PkgCmsChangelogItem>
     */
    public function getPkgCmsChangelogItemCollection(): Collection
    {
        return $this->pkgCmsChangelogItemCollection;
    }

    public function addPkgCmsChangelogItemCollection(PkgCmsChangelogItem $pkgCmsChangelogItem): self
    {
        if (!$this->pkgCmsChangelogItemCollection->contains($pkgCmsChangelogItem)) {
            $this->pkgCmsChangelogItemCollection->add($pkgCmsChangelogItem);
            $pkgCmsChangelogItem->setPkgCmsChangelogSet($this);
        }

        return $this;
    }

    public function removePkgCmsChangelogItemCollection(PkgCmsChangelogItem $pkgCmsChangelogItem): self
    {
        if ($this->pkgCmsChangelogItemCollection->removeElement($pkgCmsChangelogItem)) {
            // set the owning side to null (unless already changed)
            if ($pkgCmsChangelogItem->getPkgCmsChangelogSet() === $this) {
                $pkgCmsChangelogItem->setPkgCmsChangelogSet(null);
            }
        }

        return $this;
    }
}
