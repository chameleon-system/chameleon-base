<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgCmsTheme
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Descriptive name */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Snippet chain */
        private string $snippetChain = '',
        // TCMSFieldVarchar
        /** @var string - Own LESS file */
        private string $lessFile = '',
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, PkgCmsThemeBlockLayout> - Theme block layouts */
        private Collection $pkgCmsThemeBlockLayoutCollection = new ArrayCollection(),
        // TCMSFieldLookupDirectory
        /** @var string - Directory */
        private string $directory = '',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Preview image */
        private ?CmsMedia $cmsMedia = null
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

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldText
    public function getSnippetChain(): string
    {
        return $this->snippetChain;
    }

    public function setSnippetChain(string $snippetChain): self
    {
        $this->snippetChain = $snippetChain;

        return $this;
    }

    // TCMSFieldVarchar
    public function getLessFile(): string
    {
        return $this->lessFile;
    }

    public function setLessFile(string $lessFile): self
    {
        $this->lessFile = $lessFile;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, PkgCmsThemeBlockLayout>
     */
    public function getPkgCmsThemeBlockLayoutCollection(): Collection
    {
        return $this->pkgCmsThemeBlockLayoutCollection;
    }

    public function addPkgCmsThemeBlockLayoutCollection(PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayoutMlt): self
    {
        if (!$this->pkgCmsThemeBlockLayoutCollection->contains($pkgCmsThemeBlockLayoutMlt)) {
            $this->pkgCmsThemeBlockLayoutCollection->add($pkgCmsThemeBlockLayoutMlt);
            $pkgCmsThemeBlockLayoutMlt->set($this);
        }

        return $this;
    }

    public function removePkgCmsThemeBlockLayoutCollection(PkgCmsThemeBlockLayout $pkgCmsThemeBlockLayoutMlt): self
    {
        if ($this->pkgCmsThemeBlockLayoutCollection->removeElement($pkgCmsThemeBlockLayoutMlt)) {
            // set the owning side to null (unless already changed)
            if ($pkgCmsThemeBlockLayoutMlt->get() === $this) {
                $pkgCmsThemeBlockLayoutMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupDirectory
    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getCmsMedia(): ?CmsMedia
    {
        return $this->cmsMedia;
    }

    public function setCmsMedia(?CmsMedia $cmsMedia): self
    {
        $this->cmsMedia = $cmsMedia;

        return $this;
    }
}
