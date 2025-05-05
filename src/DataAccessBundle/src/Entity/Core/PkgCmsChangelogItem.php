<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsFieldConf;

class PkgCmsChangelogItem
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var PkgCmsChangelogSet|null - Changeset */
        private ?PkgCmsChangelogSet $pkgCmsChangelogSet = null,
        // TCMSFieldLookupParentID
        /** @var CmsFieldConf|null - Changed field */
        private ?CmsFieldConf $cmsFieldConf = null,
        // TCMSFieldText
        /** @var string - Old value */
        private string $valueOld = '',
        // TCMSFieldText
        /** @var string - New value */
        private string $valueNew = ''
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
    public function getPkgCmsChangelogSet(): ?PkgCmsChangelogSet
    {
        return $this->pkgCmsChangelogSet;
    }

    public function setPkgCmsChangelogSet(?PkgCmsChangelogSet $pkgCmsChangelogSet): self
    {
        $this->pkgCmsChangelogSet = $pkgCmsChangelogSet;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsFieldConf(): ?CmsFieldConf
    {
        return $this->cmsFieldConf;
    }

    public function setCmsFieldConf(?CmsFieldConf $cmsFieldConf): self
    {
        $this->cmsFieldConf = $cmsFieldConf;

        return $this;
    }

    // TCMSFieldText
    public function getValueOld(): string
    {
        return $this->valueOld;
    }

    public function setValueOld(string $valueOld): self
    {
        $this->valueOld = $valueOld;

        return $this;
    }

    // TCMSFieldText
    public function getValueNew(): string
    {
        return $this->valueNew;
    }

    public function setValueNew(string $valueNew): self
    {
        $this->valueNew = $valueNew;

        return $this;
    }
}
